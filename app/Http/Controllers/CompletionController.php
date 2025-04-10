<?php

namespace App\Http\Controllers;

use App\Models\ImportPack;
use App\Models\ImportLine;
use App\Models\ImportLandlord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class CompletionController extends Controller
{
    /**
     * Liste des packs attribués au collaborateur
     */
    public function index(Request $request)
    {
        $packs = ImportPack::with('lines')
            ->where('assigned_to', Auth::id())
            ->latest()
            ->get()
            ->filter(function ($pack) use ($request) {
                $total = $pack->lines->count();
                $completed = $pack->lines->where('status', 'Terminée')->count();

                // Ignore les packs sans ligne
                if ($total === 0) return false;

                $percent = round(($completed / $total) * 100);

                return match ($request->input('progress')) {
                    'todo' => $percent == 0,
                    'inprogress' => $percent > 0 && $percent < 100,
                    'done' => $percent == 100,
                    default => true,
                };
            });

        // Recrée une pagination manuelle après filtrage
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 6;
        $currentItems = $packs->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $currentItems,
            $packs->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('completion.index', ['packs' => $paginated]);
    }

    /**
     * Affiche les lignes d’un pack attribué
     */
    public function show(ImportPack $pack)
    {
        abort_if($pack->assigned_to !== Auth::id(), 403);

        // $lines = $pack->lines()
        //     ->with('landlords')
        //     ->orderBy('created_at')
        //     ->paginate(10);

        $lines = $pack->lines()
            ->selectRaw("*, CASE
        WHEN status = 'Terminée' THEN 1
        ELSE 0 END as status_order")
            ->orderBy('status_order')
            ->orderBy('created_at')
            ->paginate(30);

        return view('completion.pack.show', compact('pack', 'lines'));
    }

    /**
     * Enregistrement via modale (cadastre + plusieurs propriétaires)
     */
    public function saveLineCompletion(Request $request, ImportLine $line)
    {
        $request->validate([
            'cadaster_number' => 'nullable|string|max:255',
            'landlords' => 'nullable|array',
            'landlords.*.name' => 'required|string|max:255',
            'landlords.*.address' => 'nullable|string|max:255',
            'landlords.*.postal_code' => 'nullable|string|max:20',
            'landlords.*.city' => 'nullable|string|max:100',
        ]);

        // Enregistrement du numéro de cadastre
        $line->cadaster_number = $request->input('cadaster_number');
        $line->save();

        // Suppression des anciens propriétaires liés
        $line->landlords()->delete();

        // Création des nouveaux propriétaires
        if ($request->has('landlords')) {
            foreach ($request->landlords as $data) {
                ImportLandlord::create([
                    'import_line_id' => $line->id,
                    'name' => $data['name'],
                    'address' => $data['address'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'city' => $data['city'] ?? null,
                ]);
            }
        }

        // Mise à jour du statut
        if ($line->cadaster_number && count($request->landlords ?? []) > 0) {
            $hasIncomplete = collect($request->landlords)->contains(function ($l) {
                return empty($l['address']) || empty($l['postal_code']) || empty($l['city']);
            });

            $line->status = $hasIncomplete ? 'En cours' : 'Terminée';
        } elseif ($line->cadaster_number || !empty($request->landlords)) {
            $line->status = 'En cours';
        } else {
            $line->status = 'À compléter';
        }

        $line->save();

        return back()->with('success', 'Ligne complétée avec succès.');
    }

    /**
     * Règle de calcul du statut
     */
    private function computeStatus(ImportLine $line): string
    {
        $hasCadaster = !empty($line->cadaster_number);
        $hasLandlord = $line->landlords()->exists();

        return match (true) {
            $hasCadaster && $hasLandlord => 'Terminée',
            $hasCadaster || $hasLandlord => 'En cours',
            default => 'À compléter',
        };
    }

    public function destroy(ImportLine $line)
    {
        // Supprimer les propriétaires associés si nécessaire
        $line->landlords()->delete();

        // Supprimer la ligne
        $line->delete();

        return redirect()->back()->with('success', 'Ligne supprimée avec succès.');
    }
}
