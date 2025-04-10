@extends('layouts.app')

@section('page-title', 'Exporter les Données')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8 bg-white shadow-md rounded-xl" x-data="exportData()">
        <h1 class="text-2xl font-semibold text-tipblue mb-6">Exporter un Pack</h1>

        <form method="POST" action="{{ route('exports.export') }}">
            @csrf

            {{-- Sélection du pack et statut --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="pack_id" class="block text-sm font-medium text-gray-700 mb-1">Pack à exporter</label>
                    <select name="pack_id" id="pack_id" x-model="packId" @change="loadPreview"
                        class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Choisir un pack --</option>
                        @foreach ($packs as $pack)
                            <option value="{{ $pack->id }}">{{ $pack->name }} ({{ $pack->lines_count }} lignes)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par statut</label>
                    <select name="status" id="status" x-model="status" @change="loadPreview"
                        class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="all">Tous</option>
                        <option value="À compléter">À compléter</option>
                        <option value="En cours">En cours</option>
                        <option value="Terminée">Terminée</option>
                    </select>
                </div>
            </div>

            {{-- Colonnes à exporter --}}
            <div class="mb-8">
                <label class="block font-medium text-sm text-gray-700 mb-2">Colonnes à exporter</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @php
                        $columns = [
                            'site_address',
                            'postal_code',
                            'city',
                            'cadaster_number',
                            'landlord',
                            'landlord_address',
                            'landlord_postal_code',
                            'landlord_city',
                            'status',
                        ];
                    @endphp
                    @foreach ($columns as $col)
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="checkbox" name="columns[]" value="{{ $col }}" checked
                                class="text-tipblue border-gray-300">
                            <span class="ml-2">{{ ucfirst(str_replace('_', ' ', $col)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Prévisualisation --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Prévisualisation (10 lignes max)</h2>

                <template x-if="preview.length > 0">
                    <div class="overflow-x-auto border rounded-md shadow-sm mt-4">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600">
                                <tr>
                                    <template x-for="(key, index) in Object.keys(preview[0] || {})" :key="index">
                                        <th class="px-4 py-2 text-left" x-text="key.replaceAll('_', ' ')"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800">
                                <template x-for="(row, idx) in preview" :key="idx">
                                    <tr class="hover:bg-gray-50 transition">
                                        <template x-for="(value, key) in row" :key="key">
                                            <td class="px-4 py-2" x-text="value ?? '-'"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template x-if="preview.length === 0">
                    <p class="text-sm text-gray-500 mt-2">Aucune donnée à afficher.</p>
                </template>
            </div>

            {{-- Bouton d'export --}}
            <div class="mt-8">
                <button type="submit"
                    class="px-6 py-3 bg-tipblue text-white rounded-md font-semibold hover:bg-blue-700 transition">
                    Exporter (.xlsx)
                </button>
            </div>
        </form>
    </div>

    {{-- Script Alpine.js --}}
    <script>
        function exportData() {
            return {
                packId: '',
                status: 'all',
                preview: [],
                selectedColumns: [...document.querySelectorAll('input[name="columns[]"]:checked')].map(c => c.value),

                loadPreview() {
                    this.selectedColumns = [...document.querySelectorAll('input[name="columns[]"]:checked')].map(c => c
                        .value);

                    if (!this.packId) {
                        this.preview = [];
                        return;
                    }

                    fetch("{{ route('exports.preview') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                pack_id: this.packId,
                                status: this.status,
                                columns: this.selectedColumns
                            })
                        })
                        .then(res => res.json())
                        .then(data => this.preview = data)
                        .catch(() => this.preview = []);
                }
            }
        }
    </script>
@endsection
