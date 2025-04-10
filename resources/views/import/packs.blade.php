@extends('layouts.app')

@section('page-title', 'Liste des packs importés')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow-md">
        <h1 class="text-4xl font-bold text-tipblue mb-6 flex items-center gap-2">
            Tous les Packs Importés
        </h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-sm">{{ session('error') }}</div>
        @endif

        <div class="rounded-xl border border-gray-200">
            <table class="w-full text-xs text-left divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Nom</th>
                        <th class="px-3 py-2">Par</th>
                        <th class="px-3 py-2">Lignes</th>
                        <th class="px-3 py-2">Avancement</th>
                        <th class="px-3 py-2">Assigné</th>
                        <th class="px-3 py-2 text-center">Assigner</th>
                        <th class="px-3 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($packs as $pack)
                        @php
                            $total = $pack->lines->count();
                            $completed = $pack->lines->where('status', 'Terminée')->count();
                            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                            $barColor =
                                $percent >= 100 ? 'bg-green-500' : ($percent >= 50 ? 'bg-yellow-400' : 'bg-red-400');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-semibold text-gray-700">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2 text-tipblue font-medium truncate max-w-[140px]">{{ $pack->name }}</td>
                            <td class="px-3 py-2">{{ $pack->importedBy->name ?? 'Inconnu' }}</td>
                            <td class="px-3 py-2">{{ $total }}</td>
                            <td class="px-3 py-2">
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $barColor }}" style="width: {{ $percent }}%">
                                    </div>
                                </div>
                                <span class="text-[11px] text-gray-500 mt-1 block">{{ $percent }}%</span>
                            </td>
                            <td class="px-3 py-2">
                                @if ($pack->assignedTo)
                                    <span
                                        class="inline-block bg-blue-50 text-blue-700 text-[11px] px-2 py-0.5 rounded font-medium">{{ $pack->assignedTo->name }}</span>
                                @else
                                    <span class="text-gray-400 italic text-xs">Non assigné</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <form method="POST" action="{{ route('import.assignPack') }}"
                                    class="flex items-center justify-center gap-1">
                                    @csrf
                                    <input type="hidden" name="pack_id" value="{{ $pack->id }}">
                                    <select name="user_id" class="border border-gray-300 rounded px-2 py-1 text-xs">
                                        <option value="">Choisir</option>
                                        @foreach ($collaborateurs as $collaborateur)
                                            <option value="{{ $collaborateur->id }}">{{ $collaborateur->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="bg-tipblue text-white text-[11px] px-2 py-1 rounded hover:bg-blue-700 transition">
                                        Assigner
                                    </button>
                                </form>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('import.previewPack', $pack->id) }}"
                                    class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 text-xs font-medium rounded-md transition border border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
