<?php

namespace App\Http\Controllers;

use App\Models\ImportPack;
use App\Models\ImportLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin', [
            'totalPacks' => ImportPack::count(),
            'totalLines' => ImportLine::count(),
            'linesCompleted' => ImportLine::where('status', 'Terminé')->count(),
            'linesInProgress' => ImportLine::where('status', 'En cours')->count(),
            'linesTodo' => ImportLine::where('status', 'À compléter')->count(),
            'usersByRole' => Role::withCount('users')->pluck('users_count', 'name'),
            'packsByRegion' => ImportPack::select('region', DB::raw('count(*) as total'))
                ->groupBy('region')
                ->pluck('total', 'region'),
        ]);
    }

    public function directeur()
    {
        $user = Auth::user();

        $packs = ImportPack::where('imported_by', $user->id)
            ->with('lines')
            ->get();

        return view('dashboard.directeur', [
            'packs' => $packs,
            'packsValidated' => $packs->filter(fn($p) => $p->lines->every(fn($l) => $l->status === 'Terminée'))->count(),
            'packsInProgress' => $packs->filter(fn($p) => $p->lines->contains(fn($l) => $l->status !== 'Terminée'))->count(),
            'collaborators' => User::role('collaborateur')->get(),
            'packsByRegion' => $packs->groupBy('region')->map->count(),
        ]);
    }

    public function responsable()
    {
        $user = Auth::user();

        $packs = ImportPack::where('imported_by', $user->id)
            ->with('lines.assignedUser')
            ->get();

        $collaboratorsStats = User::role('collaborateur')->get()->map(function ($collab) {
            $lines = ImportLine::where('assigned_to', $collab->id)->get();
            return [
                'name' => $collab->name,
                'assigned' => $lines->count(),
                'completed' => $lines->where('status', 'Terminé')->count(),
            ];
        });

        return view('dashboard.responsable', [
            'packs' => $packs,
            'collaboratorsStats' => $collaboratorsStats,
            'packsByRegion' => $packs->groupBy('region')->map->count(),
        ]);
    }

    public function collaborateur()
    {
        $user = Auth::user();

        // Récupère 10 packs assignés aléatoirement
        $packs = ImportPack::where('assigned_to', $user->id)
            ->inRandomOrder()
            ->with('lines')
            ->take(5)
            ->get();

        $totalLines = $packs->flatMap->lines->count();
        $done = $packs->flatMap->lines->where('status', 'Terminée')->count();

        return view('dashboard.collaborateur', [
            'packs' => $packs,
            'totalLines' => $totalLines,
            'done' => $done,
        ]);
    }
}
