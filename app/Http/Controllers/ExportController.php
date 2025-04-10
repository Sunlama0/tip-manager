<?php

namespace App\Http\Controllers;

use App\Models\ImportPack;
use Illuminate\Http\Request;
use App\Exports\PackExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Affiche la vue d’export
     */
    public function index()
    {
        $packs = ImportPack::withCount('lines')->latest()->get();
        return view('exports.index', compact('packs'));
    }

    /**
     * Prévisualisation dynamique (max 10 lignes)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'pack_id' => 'required|exists:import_packs,id',
            'status' => 'nullable|string|in:all,À compléter,En cours,Terminée',
            'columns' => 'required|array'
        ]);

        $columns = $request->columns;
        $pack = ImportPack::findOrFail($request->pack_id);
        $query = $pack->lines()->with('landlords');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $lines = $query->limit(10)->get();
        $preview = [];

        foreach ($lines as $line) {
            if ($line->landlords->count()) {
                $grouped = $line->landlords->groupBy(function ($landlord) {
                    return $landlord->address . '|' . $landlord->postal_code . '|' . $landlord->city;
                });

                foreach ($grouped as $group) {
                    $first = $group->first();
                    $names = $group->pluck('name')->implode(', ');

                    $row = [];

                    foreach ($columns as $col) {
                        $row[$col] = match ($col) {
                            'landlord' => $names,
                            'landlord_address' => $first->address,
                            'landlord_postal_code' => $first->postal_code,
                            'landlord_city' => $first->city,
                            default => $line->$col ?? null,
                        };
                    }

                    $preview[] = $row;
                }
            } else {
                $row = [];

                foreach ($columns as $col) {
                    $row[$col] = match ($col) {
                        'landlord', 'landlord_address', 'landlord_postal_code', 'landlord_city' => null,
                        default => $line->$col ?? null,
                    };
                }

                $preview[] = $row;
            }
        }

        return response()->json($preview);
    }


    /**
     * Exporte le fichier .xlsx
     */
    public function export(Request $request)
    {
        $request->validate([
            'pack_id' => 'required|exists:import_packs,id',
            'columns' => 'required|array',
            'status' => 'nullable|in:all,À compléter,En cours,Terminée',
        ]);

        $pack = ImportPack::findOrFail($request->pack_id);
        $columns = $request->input('columns');
        $status = $request->input('status');

        $filename = 'export_TIP_' . $pack->name . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PackExport($pack, $columns, $status), $filename);
    }
}
