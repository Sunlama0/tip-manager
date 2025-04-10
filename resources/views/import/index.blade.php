@extends('layouts.app')

@section('page-title', 'Importation de données')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-8 bg-white rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-tipblue mb-6">Nouvel import de données</h1>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('import.preview') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="pack_name" class="block text-sm font-semibold text-gray-700 mb-1">
                    Nom du pack
                </label>
                <input type="text" id="pack_name" name="pack_name" required placeholder="Ex. Région Est - Avril"
                    class="w-full rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-tipblue focus:ring-tipblue">
            </div>

            <div>
                <label for="region" class="block text-sm font-semibold text-gray-700 mb-1">
                    Région
                </label>
                <select id="region" name="region" required
                    class="w-full rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-tipblue focus:ring-tipblue">
                    <option value="" disabled selected>Choisir une région</option>
                    <option value="Auvergne-Rhône-Alpes">Auvergne-Rhône-Alpes</option>
                    <option value="Bourgogne-Franche-Comté">Bourgogne-Franche-Comté</option>
                    <option value="Bretagne">Bretagne</option>
                    <option value="Centre-Val de Loire">Centre-Val de Loire</option>
                    <option value="Corse">Corse</option>
                    <option value="Grand Est">Grand Est</option>
                    <option value="Hauts-de-France">Hauts-de-France</option>
                    <option value="Île-de-France">Île-de-France</option>
                    <option value="Normandie">Normandie</option>
                    <option value="Nouvelle-Aquitaine">Nouvelle-Aquitaine</option>
                    <option value="Occitanie">Occitanie</option>
                    <option value="Pays de la Loire">Pays de la Loire</option>
                    <option value="Provence-Alpes-Côte d'Azur">Provence-Alpes-Côte d'Azur</option>
                    <option value="Guadeloupe">Guadeloupe</option>
                    <option value="Martinique">Martinique</option>
                    <option value="Guyane">Guyane</option>
                    <option value="La Réunion">La Réunion</option>
                    <option value="Mayotte">Mayotte</option>
                </select>
            </div>

            <div>
                <label for="file" class="block text-sm font-semibold text-gray-700 mb-1">
                    Fichier Excel
                </label>
                <input type="file" name="file" id="file" accept=".xls,.xlsx" required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                   file:rounded-md file:border-0 file:text-sm file:font-medium
                   file:bg-tipblue file:text-white hover:file:bg-blue-700 transition" />
                <p class="mt-1 text-xs text-gray-500">Formats autorisés : .xls, .xlsx — 2 Mo max</p>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-tipblue hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-md transition">
                    Prévisualiser l'import
                </button>
            </div>
        </form>
    </div>
@endsection
