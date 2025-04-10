@extends('layouts.app')

@section('page-title', 'Prévisualisation du pack : ' . $packName)

@section('content')
    @php
        $rows = Session::get('preview_rows', []);
    @endphp

    <div class="max-w-6xl mx-auto px-6 py-8 bg-white rounded-xl shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-tipblue mb-1">Prévisualisation de l’import</h1>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Nom du pack :</span> {{ $packName }}
                    <span class="mx-2">•</span>
                    <span class="font-medium">Lignes détectées :</span>
                    {{ count(array_filter($rows, fn($r) => !isset($r['_deleted']))) }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('import.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded text-sm font-medium">
                    Retour à l’import
                </a>

                <form method="POST" action="{{ route('import.cancel') }}">
                    @csrf
                    <button type="submit"
                        class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm font-medium">
                        Annuler l’import
                    </button>
                </form>

                <form method="POST" action="{{ route('import.confirm') }}">
                    @csrf
                    <button type="submit"
                        class="bg-tipblue hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded transition">
                        Valider l'import
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Site Address</th>
                        <th class="px-4 py-3">Postal Code</th>
                        <th class="px-4 py-3">City</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @foreach ($rows as $index => $row)
                        @if (!isset($row['_deleted']))
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $row[0] ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $row[1] ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $row[2] ?? '-' }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function deleteRow(index) {
            if (!confirm("Es-tu sûr de vouloir supprimer cette ligne ?")) return;

            fetch("{{ route('import.preview.deleteRow') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    index: index
                })
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression.');
                }
            });
        }
    </script>
@endsection
