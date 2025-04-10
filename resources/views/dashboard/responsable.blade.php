@extends('layouts.app')
@section('page-title', 'Dashboard Responsable')

@section('content')
    <h1 class="text-2xl font-semibold text-tipblue mb-4">Vue responsable</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach ($packsByRegion as $region => $count)
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="text-gray-500 text-sm mb-1">{{ $region }}</h3>
                <p class="text-2xl font-semibold text-tipblue">{{ $count }} pack(s)</p>
            </div>
        @endforeach
    </div>

    <h2 class="text-lg font-semibold mb-4">Progression des collaborateurs</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($collaboratorsStats as $stat)
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-semibold text-gray-800">{{ $stat['name'] }}</h3>
                <p class="text-sm text-gray-500">Lignes assignées : <span
                        class="text-tipblue font-medium">{{ $stat['assigned'] }}</span></p>
                <p class="text-sm text-gray-500">Lignes complétées : <span
                        class="text-green-600 font-medium">{{ $stat['completed'] }}</span></p>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-2">Liste des Packs</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-xl">
                <thead class="bg-gray-100 text-gray-600 text-sm">
                    <tr>
                        <th class="py-3 px-4 text-left">Nom du pack</th>
                        <th class="py-3 px-4 text-left">Lignes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packs as $pack)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $pack->name }}</td>
                            <td class="py-2 px-4">{{ $pack->lines->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
