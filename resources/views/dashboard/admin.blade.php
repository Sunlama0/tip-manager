@extends('layouts.app')
@section('page-title', 'Dashboard Admin')

@section('content')
    <h1 class="text-2xl font-semibold text-tipblue mb-4">Vue d'administration</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Total des packs</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $totalPacks }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes importées</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $totalLines }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes terminées</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $linesCompleted }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes en cours</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $linesInProgress }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="text-gray-500 text-sm mb-1">Lignes à compléter</h3>
            <p class="text-2xl font-semibold text-tipblue">{{ $linesTodo }}</p>
        </div>
        @foreach ($usersByRole as $role => $count)
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="text-gray-500 text-sm mb-1">Utilisateurs {{ ucfirst($role) }}</h3>
                <p class="text-2xl font-semibold text-tipblue">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-2">Répartition des packs par région</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($packsByRegion as $region => $count)
                <div class="bg-white p-4 rounded-xl shadow">
                    <h4 class="text-sm text-gray-600 mb-1">{{ $region }}</h4>
                    <p class="text-lg font-semibold text-tipblue">{{ $count }} packs</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
