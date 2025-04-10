@extends('layouts.app')
@section('page-title', 'Dashboard Directeur')

@section('content')
    <h1 class="text-2xl font-semibold text-tipblue mb-4">Vue directeur</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Total de packs</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $packs->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Total des lignes</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $packs->flatMap->lines->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes complétées</h3>
            <p class="text-2xl font-semibold text-green-600">
                {{ $packs->flatMap->lines->where('status', 'Terminée')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Collaborateurs</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $collaborators->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Packs complétés</h3>
            <p class="text-2xl font-semibold text-green-600">{{ $packsValidated }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Packs en cours</h3>
            <p class="text-2xl font-semibold text-yellow-600">{{ $packsInProgress }}</p>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">Répartition des packs par région</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($packsByRegion as $region => $count)
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-gray-500 text-sm mb-1">{{ $region }}</h3>
                    <p class="text-2xl font-semibold text-tipblue">{{ $count }} pack(s)</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-2">Détail des Packs</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-xl">
                <thead class="bg-gray-100 text-gray-600 text-sm">
                    <tr>
                        <th class="py-3 px-4 text-left">Nom du pack</th>
                        <th class="py-3 px-4 text-left">Nombre de lignes</th>
                        <th class="py-3 px-4 text-left">Complétées</th>
                        <th class="py-3 px-4 text-left">Avancement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packs as $pack)
                        @php
                            $total = $pack->lines->count();
                            $completed = $pack->lines->where('status', 'Terminée')->count();
                            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $pack->name }}</td>
                            <td class="py-2 px-4">{{ $total }}</td>
                            <td class="py-2 px-4 text-green-600">{{ $completed }}</td>
                            <td class="py-2 px-4">{{ $percent }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
