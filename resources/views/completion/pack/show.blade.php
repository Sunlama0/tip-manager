@extends('layouts.app')

@section('page-title', 'Complétion du pack : ' . $pack->name)

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">
        <h1 class="text-2xl font-semibold text-tipblue mb-6">Complétion du pack : {{ $pack->name }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden bg-white border rounded-lg shadow">
            <table class="min-w-full text-sm text-left rounded overflow-hidden border border-gray-200 shadow-sm"
                id="linesTable">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-medium">Adresse site</th>
                        <th class="px-4 py-3 font-medium">Code Postal</th>
                        <th class="px-4 py-3 font-medium">Ville</th>
                        <th class="px-4 py-3 font-medium">Statut</th>
                        <th class="px-4 py-3 text-center font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @foreach ($lines->sortBy(fn($l) => $l->status === 'Terminée' ? 1 : 0) as $line)
                        <tr class="hover:bg-blue-50/40 transition-all duration-150 ease-in-out">
                            <td class="px-4 py-3">{{ $line->site_address }}</td>
                            <td class="px-4 py-3">{{ $line->postal_code }}</td>
                            <td class="px-4 py-3">{{ $line->city }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-medium
                                    @if ($line->status === 'Terminée') bg-green-100 text-green-700
                                    @elseif($line->status === 'En cours') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-600 @endif">
                                    {{ $line->status === 'À compléter' ? 'À compléter' : $line->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div x-data="{ open: false, copied: false, landlords: @js(count($line->landlords) ? $line->landlords->map(fn($l) => ['name' => $l->name, 'address' => $l->address, 'postal_code' => $l->postal_code, 'city' => $l->city]) : []) }">
                                    <button @click="open = true"
                                        class="inline-flex items-center bg-tipblue hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                                        Compléter
                                    </button>

                                    <!-- Modal -->
                                    <template x-if="open">
                                        <div
                                            class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center px-4">
                                            <div
                                                class="bg-white rounded-xl shadow-lg w-full max-w-4xl p-6 relative overflow-y-auto max-h-[90vh]">
                                                <button @click="open = false"
                                                    class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-2xl">&times;</button>
                                                <h2 class="text-xl font-bold mb-4 border-b pb-2">Compléter l'antenne</h2>

                                                <form action="{{ route('completion.line.complete', $line->id) }}"
                                                    method="POST">
                                                    @csrf

                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse
                                                        complète du site</label>
                                                    <div class="flex items-center mb-4 space-x-2">
                                                        @php $fullAddress = $line->site_address . ' ' . $line->postal_code . ' ' . $line->city; @endphp
                                                        <input type="text"
                                                            class="w-full border px-2 py-1 rounded bg-gray-100 text-sm"
                                                            readonly value="{{ $fullAddress }}">
                                                        <button type="button"
                                                            @click="navigator.clipboard.writeText('{{ $fullAddress }}'); copied = true"
                                                            :class="copied ? 'text-green-600' : 'text-tipblue'"
                                                            class="text-sm">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <rect x="9" y="9" width="13" height="13"
                                                                    rx="2" ry="2"></rect>
                                                                <path
                                                                    d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1">
                                                                </path>
                                                            </svg>
                                                            <span x-text="copied ? 'Copié' : 'Copier'"></span>
                                                        </button>
                                                    </div>

                                                    <div class="mb-4 w-full md:w-1/2">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 text-left">Numéro
                                                            de cadastre</label>
                                                        <input type="text" name="cadaster_number"
                                                            value="{{ $line->cadaster_number }}"
                                                            class="mt-1 w-full border rounded px-3 py-2 text-sm">
                                                    </div>

                                                    <label
                                                        class="block text-sm font-semibold text-gray-700 mb-1 text-left">Liste
                                                        des propriétaires</label>
                                                    <template x-for="(landlord, index) in landlords" :key="index">
                                                        <div
                                                            class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4 bg-gray-50 p-4 rounded relative">
                                                            <button type="button" @click="landlords.splice(index, 1)"
                                                                class="absolute top-2 right-2 text-gray-400 hover:text-red-600">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                            <div>
                                                                <label class="text-xs text-gray-600">Nom</label>
                                                                <input type="text" :name="`landlords[${index}][name]`"
                                                                    x-model="landlord.name"
                                                                    class="w-full border px-2 py-1 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="text-xs text-gray-600">Adresse</label>
                                                                <input type="text" :name="`landlords[${index}][address]`"
                                                                    x-model="landlord.address"
                                                                    class="w-full border px-2 py-1 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="text-xs text-gray-600">CP</label>
                                                                <input type="text"
                                                                    :name="`landlords[${index}][postal_code]`"
                                                                    x-model="landlord.postal_code"
                                                                    class="w-full border px-2 py-1 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="text-xs text-gray-600">Ville</label>
                                                                <input type="text" :name="`landlords[${index}][city]`"
                                                                    x-model="landlord.city"
                                                                    class="w-full border px-2 py-1 rounded text-sm">
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <button type="button"
                                                        @click="landlords.push({name:'', address:'', postal_code:'', city:''})"
                                                        class="text-sm text-tipblue hover:underline mb-4">+ Ajouter un
                                                        propriétaire</button>

                                                    <div
                                                        class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
                                                        <form action="{{ route('completion.line.delete', $line->id) }}"
                                                            method="POST" onsubmit="return confirmDelete();">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 hover:text-red-800 text-sm border border-red-500 px-4 py-2 rounded">Supprimer
                                                                la ligne</button>
                                                        </form>
                                                        <button type="submit"
                                                            class="bg-tipblue text-white px-5 py-2 rounded hover:bg-blue-700 transition text-sm">Enregistrer
                                                            l'antenne</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $lines->links() }}
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer définitivement cette ligne ?");
        }
    </script>
@endsection
