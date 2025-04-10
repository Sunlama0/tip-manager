<?php

namespace App\Http\Controllers;

use App\Models\ImportPack;
use App\Models\ImportLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ImportLandlord;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'pack_name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        $filename = uniqid('import_') . '.' . $request->file('file')->getClientOriginalExtension();
        $tempDirectory = storage_path('app/temp');

        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $request->file('file')->move($tempDirectory, $filename);
        $relativePath = 'temp/' . $filename;
        $fullPath = storage_path("app/{$relativePath}");

        $rows = Excel::toCollection(null, $fullPath)->first()?->toArray() ?? [];

        if (isset($rows[0]) && strtolower(trim($rows[0][0])) === 'site adress') {
            unset($rows[0]);
            $rows = array_values($rows);
        }

        Session::put('preview_file_path', $relativePath);
        Session::put('preview_pack_name', $request->pack_name);
        Session::put('preview_region', $request->region);
        Session::put('preview_rows', $rows);

        return view('import.preview', [
            'packName' => $request->pack_name,
            'region' => $request->region,
        ]);
    }

    public function cancelPreview()
    {
        if ($path = Session::get('preview_file_path')) {
            $fullPath = storage_path("app/{$path}");
            if (file_exists($fullPath)) unlink($fullPath);
        }

        Session::forget(['preview_file_path', 'preview_pack_name', 'preview_rows']);

        return redirect()->route('import.index')->with('success', 'Import annulé.');
    }

    public function confirm()
    {
        $packName = Session::get('preview_pack_name');
        $rows = Session::get('preview_rows', []);

        if (!$packName || empty($rows)) {
            return redirect()->route('import.index')->with('error', 'Session expirée. Veuillez recommencer l’import.');
        }

        $filteredRows = array_filter($rows, fn($row) => !isset($row['_deleted']));

        if (empty($filteredRows)) {
            return redirect()->route('import.index')->with('error', 'Aucune ligne à importer.');
        }

        $pack = ImportPack::create([
            'name' => $packName,
            'imported_by' => Auth::id(),
            'region' => Session::get('preview_region'), // ✅ ajout de la région ici
        ]);

        foreach ($filteredRows as $row) {
            ImportLine::create([
                'site_address' => $row[0] ?? '',
                'postal_code'  => $row[1] ?? '',
                'city'         => $row[2] ?? '',
                'import_pack_id' => $pack->id,
            ]);
        }

        if ($path = Session::get('preview_file_path')) {
            $fullPath = storage_path("app/{$path}");
            if (file_exists($fullPath)) unlink($fullPath);
        }

        Session::forget(['preview_file_path', 'preview_pack_name', 'preview_region', 'preview_rows']);

        return redirect()->route('import.index')->with('success', 'Importation réussie et enregistrée.');
    }

    public function list()
    {
        $packs = ImportPack::with(['lines', 'importedBy'])->latest()->get();
        $collaborateurs = \App\Models\User::role('collaborateur')->get();

        return view('import.packs', compact('packs', 'collaborateurs'));
    }

    public function assignPack(Request $request)
    {
        $request->validate([
            'pack_id' => 'required|exists:import_packs,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $pack = ImportPack::findOrFail($request->pack_id);
        $pack->assigned_to = $request->user_id;
        $pack->save();

        // return response()->json(['message' => 'Pack assigné avec succès.']);
        return redirect()->back()->with('success', 'Pack assigné avec succès.');
    }

    public function showPackPreview($id)
    {
        $pack = ImportPack::with(['importedBy', 'assignedTo'])->findOrFail($id);
        $lines = $pack->lines()->paginate(40);

        return view('import.previewpacks', compact('pack', 'lines'));
    }

    public function updateLine(Request $request, ImportLine $line)
    {
        $request->validate([
            'site_address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'cadaster_number' => 'nullable|string|max:255',
            'landlords' => 'nullable|array',
            'landlords.*.id' => 'nullable|integer|exists:import_landlords,id',
            'landlords.*.name' => 'required|string|max:255',
            'landlords.*.address' => 'nullable|string|max:255',
            'landlords.*.postal_code' => 'nullable|string|max:20',
            'landlords.*.city' => 'nullable|string|max:100',
        ]);

        // Mise à jour des infos de la ligne
        $line->update([
            'site_address' => $request->site_address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'cadaster_number' => $request->cadaster_number,
        ]);

        // Traitement des landlords
        $submitted = collect($request->landlords ?? []);
        $existing = $line->landlords->keyBy('id');
        $submittedIds = [];

        foreach ($submitted as $data) {
            if (!empty($data['id']) && $existing->has($data['id'])) {
                $existing[$data['id']]->update([
                    'name' => $data['name'],
                    'address' => $data['address'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'city' => $data['city'] ?? null,
                ]);
                $submittedIds[] = $data['id'];
            } else {
                $landlord = ImportLandlord::create([
                    'import_line_id' => $line->id,
                    'name' => $data['name'],
                    'address' => $data['address'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'city' => $data['city'] ?? null,
                ]);
                $submittedIds[] = $landlord->id;
            }
        }

        // Supprimer ceux qui ne sont plus dans le formulaire
        $line->landlords()->whereNotIn('id', $submittedIds)->delete();

        // Mise à jour du statut
        $hasCadaster = !empty($line->cadaster_number);
        $hasLandlord = $line->landlords()->exists();

        if ($hasCadaster && $hasLandlord) {
            $line->status = 'Terminée';
        } elseif ($hasCadaster || $hasLandlord) {
            $line->status = 'En cours';
        } else {
            $line->status = 'À compléter';
        }

        $line->save();

        return back()->with('success', 'Ligne mise à jour avec succès.');
    }

    public function destroyLine(ImportLine $line)
    {
        $line->landlords()->delete();
        $line->delete();

        return back()->with('success', 'Ligne supprimée avec succès.');
    }
}
