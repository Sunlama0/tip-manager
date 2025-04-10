@extends('layouts.app')

@section('page-title', 'Mes Packs à Compléter')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-tipblue mb-6 flex items-center gap-2">
            Packs qui vous sont attribués
        </h1>

        <!-- Filtre par avancement -->
        <form method="GET" class="mb-6 max-w-md">
            <label for="progress" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par avancement :</label>
            <select name="progress" id="progress" onchange="this.form.submit()"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-tipblue focus:ring-tipblue">
                <option value="">Tous les packs</option>
                <option value="todo" {{ request('progress') === 'todo' ? 'selected' : '' }}>À compléter (0%)</option>
                <option value="inprogress" {{ request('progress') === 'inprogress' ? 'selected' : '' }}>En cours (1-99%)
                </option>
                <option value="done" {{ request('progress') === 'done' ? 'selected' : '' }}>Complétés (100%)</option>
            </select>
        </form>

        @if ($packs->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-md text-sm">
                Aucun pack ne correspond au filtre sélectionné.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($packs as $pack)
                    @php
                        $total = $pack->lines->count();
                        $completed = $pack->lines->where('status', 'Terminée')->count();
                        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                        $color = $percent >= 100 ? 'bg-green-500' : ($percent >= 50 ? 'bg-yellow-400' : 'bg-red-400');
                    @endphp

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h2 class="text-lg font-semibold text-gray-800 truncate" title="{{ $pack->name }}">
                                    {{ $pack->name }}
                                </h2>
                                <p class="text-sm text-gray-500">Importé le {{ $pack->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                {{ $total }} ligne{{ $total > 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-600 mb-1">Progression</p>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="h-2 transition-all duration-300 ease-out rounded-full {{ $color }}"
                                    style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $completed }} / {{ $total }} lignes
                                terminées ({{ $percent }}%)</p>
                        </div>

                        <a href="{{ route('completion.pack.show', $pack->id) }}"
                            class="inline-flex items-center justify-center gap-2 w-full bg-tipblue hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-md transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m2 0a2 2 0 01-2 2H9a2 2 0 110-4h6a2 2 0 012 2z" />
                            </svg>
                            Compléter ce pack
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $packs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
