@extends('layouts.app')

@section('page-title', 'Détail du pack')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8 bg-white shadow-md rounded-xl">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-tipblue mb-2">Détail du Pack : {{ $pack->name }}</h1>

            <div class="text-sm text-gray-600 mt-3 space-y-1 leading-6">
                <p><span class="font-medium">Importé par :</span> {{ $pack->importedBy->name ?? 'Inconnu' }}</p>
                <p><span class="font-medium">Assigné à :</span> {{ $pack->assignedTo->name ?? 'Non assigné' }}</p>

                @php
                    $completedCount = $lines->where('status', 'Terminée')->count();
                    $totalCount = $lines->total();
                    $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;
                    $progressColor =
                        $completionRate >= 80
                            ? 'bg-green-500'
                            : ($completionRate >= 50
                                ? 'bg-yellow-500'
                                : 'bg-red-500');
                @endphp

                <p><span class="font-medium">Nombre total de lignes :</span> {{ $totalCount }}</p>
                <p><span class="font-medium">Taux de complétion :</span> {{ $completionRate }}% ({{ $completedCount }} /
                    {{ $totalCount }} lignes terminées)</p>

                <div class="w-full h-2 bg-gray-200 rounded-full mt-1">
                    <div class="h-full text-xs text-white text-center rounded-full {{ $progressColor }}"
                        style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-md border border-gray-200 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-left">Adresse</th>
                        <th class="px-5 py-3 text-left">Code Postal</th>
                        <th class="px-5 py-3 text-left">Ville</th>
                        <th class="px-5 py-3 text-left">Cadastre</th>
                        <th class="px-5 py-3 text-left">Landlord(s)</th>
                        <th class="px-5 py-3 text-left">Statut</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @foreach ($lines as $line)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3">{{ $line->site_address }}</td>
                            <td class="px-5 py-3">{{ $line->postal_code }}</td>
                            <td class="px-5 py-3">{{ $line->city }}</td>
                            <td class="px-5 py-3">{{ $line->cadaster_number ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <button onclick="document.getElementById('view-modal-{{ $line->id }}').showModal()"
                                    class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 text-xs font-medium rounded-md transition border border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Voir
                                </button>
                                <dialog id="view-modal-{{ $line->id }}"
                                    class="rounded-xl w-full max-w-3xl p-6 bg-white shadow-xl">
                                    <h2 class="text-xl font-semibold mb-4">Propriétaires</h2>
                                    @foreach ($line->landlords as $landlord)
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 border-b pb-4">
                                            <div>
                                                <p class="text-xs text-gray-500">Nom</p>
                                                <p class="text-sm font-medium text-gray-800">{{ $landlord->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Adresse</p>
                                                <p class="text-sm text-gray-700">{{ $landlord->address }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Code Postal</p>
                                                <p class="text-sm text-gray-700">{{ $landlord->postal_code }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Ville</p>
                                                <p class="text-sm text-gray-700">{{ $landlord->city }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    <form method="dialog" class="mt-4 text-right">
                                        <button class="px-4 py-2 bg-tipblue text-white rounded">Fermer</button>
                                    </form>
                                </dialog>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $status = strtolower($line->status ?? 'à compléter');
                                    $badgeStyles = [
                                        'à compléter' => 'bg-red-100 text-red-700',
                                        'en cours' => 'bg-yellow-100 text-yellow-800',
                                        'terminée' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeStyles[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3 text-sm">
                                    {{-- Supprimer --}}
                                    <form action="{{ route('packs.lines.destroy', $line) }}" method="POST"
                                        onsubmit="return confirm('Confirmer la suppression de cette ligne ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer"
                                            class="text-red-500 hover:text-red-700 transition duration-150 ease-in-out">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6v14h8V6" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 10v6" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10v6" />
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Modifier --}}
                                    <button onclick="document.getElementById('edit-modal-{{ $line->id }}').showModal()"
                                        title="Modifier"
                                        class="text-tipblue hover:text-blue-700 transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                </div>
                                <dialog id="edit-modal-{{ $line->id }}"
                                    class="rounded-xl w-full max-w-4xl p-6 bg-white shadow-xl" x-data="{ landlords: @js($line->landlords) }">
                                    <form method="POST" action="{{ route('packs.lines.update', $line) }}">
                                        @csrf
                                        @method('PUT')
                                        <h2 class="text-xl font-semibold mb-4">Modifier l'antenne</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm text-gray-600 font-medium">Adresse site</label>
                                                <input type="text" name="site_address"
                                                    value="{{ $line->site_address }}"
                                                    class="w-full border px-3 py-2 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="text-sm text-gray-600 font-medium">Code postal</label>
                                                <input type="text" name="postal_code"
                                                    value="{{ $line->postal_code }}"
                                                    class="w-full border px-3 py-2 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="text-sm text-gray-600 font-medium">Ville</label>
                                                <input type="text" name="city" value="{{ $line->city }}"
                                                    class="w-full border px-3 py-2 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="text-sm text-gray-600 font-medium">Numéro de cadastre</label>
                                                <input type="text" name="cadaster_number"
                                                    value="{{ $line->cadaster_number }}"
                                                    class="w-full border px-3 py-2 rounded text-sm">
                                            </div>
                                        </div>

                                        <div class="mt-6">
                                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Propriétaires fonciers
                                            </h3>
                                            <template x-for="(landlord, index) in landlords" :key="index">
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 relative bg-gray-50 border rounded p-4">
                                                    <input type="hidden" :name="`landlords[${index}][id]`"
                                                        x-model="landlord.id">
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
                                                        <label class="text-xs text-gray-600">Code Postal</label>
                                                        <input type="text" :name="`landlords[${index}][postal_code]`"
                                                            x-model="landlord.postal_code"
                                                            class="w-full border px-2 py-1 rounded text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-gray-600">Ville</label>
                                                        <input type="text" :name="`landlords[${index}][city]`"
                                                            x-model="landlord.city"
                                                            class="w-full border px-2 py-1 rounded text-sm">
                                                    </div>
                                                    <button type="button" @click="landlords.splice(index, 1)"
                                                        class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <button type="button"
                                                @click="landlords.push({name: '', address: '', postal_code: '', city: ''})"
                                                class="text-sm text-tipblue hover:underline mt-2">+ Ajouter un
                                                propriétaire</button>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button"
                                                onclick="document.getElementById('edit-modal-{{ $line->id }}').close()"
                                                class="px-4 py-2 border rounded text-sm text-gray-600">Annuler</button>
                                            <button type="submit"
                                                class="px-5 py-2 bg-tipblue text-white rounded hover:bg-blue-700 text-sm">Enregistrer</button>
                                        </div>
                                    </form>
                                </dialog>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $lines->links() }}
        </div>

        <div class="mt-6">
            <a href="{{ route('import.index') }}" class="text-sm text-tipblue hover:underline">⬅ Retour aux imports</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endsection
