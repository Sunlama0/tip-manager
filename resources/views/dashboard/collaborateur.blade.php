@extends('layouts.app')
@section('page-title', 'Dashboard Collaborateur')

@section('content')
    <h1 class="text-2xl font-semibold text-tipblue mb-6">Bienvenue sur votre espace</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Packs attribués</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $packs->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Total des lignes</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $totalLines }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes complétées</h3>
            <p class="text-2xl font-semibold text-green-600">{{ $done }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Avancement</h3>
            <p class="text-2xl font-semibold text-blue-500">
                @if ($totalLines > 0)
                    {{ round(($done / $totalLines) * 100) }} %
                @else
                    0 %
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <h2 class="px-6 py-4 border-b text-lg font-semibold text-tipblue">Mes packs</h2>
        @if ($packs->isEmpty())
            <p class="px-6 py-4 text-sm text-gray-500">Aucun pack ne vous est actuellement attribué.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom
                                du pack</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Région</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lignes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Complétées</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avancement</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($packs as $pack)
                            @php
                                $total = $pack->lines->count();
                                $completed = $pack->lines->where('status', 'Terminée')->count();
                                $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $pack->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $pack->region ?? 'Non définie' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $total }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ $completed }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $percent }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
