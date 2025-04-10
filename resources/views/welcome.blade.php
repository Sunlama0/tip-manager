<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TIP | Portail Collaborateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --tip-bleu: #002B45;
            --tip-gris: #A4A9AD;
            --tip-blanc: #F6F7F8;
            --tip-bleu-accent: #1474C4;
        }
    </style>
</head>

<body class="bg-[var(--tip-blanc)] text-[var(--tip-bleu)] min-h-screen flex flex-col items-center justify-center px-6">

    {{-- Logo TIP --}}
    <img src="{{ asset('images/logo-tip.png') }}" alt="Logo TIP" class="w-32 mb-6">

    {{-- Titre / Description --}}
    <div class="text-center max-w-xl mb-10">
        <h1 class="text-3xl font-semibold mb-4">Bienvenue sur la plateforme TIP</h1>
        <p class="text-base text-[var(--tip-gris)]">
            Cet outil est destiné aux équipes internes de <strong>Telecom Infrastructure Partners</strong>
            pour gérer, enrichir et exporter les données relatives aux antennes relais.
        </p>
    </div>

    {{-- Boutons d'accès selon l'état d'authentification --}}
    @if (Route::has('login'))
        <div class="space-x-4">
            @auth
                <a href="{{ route('dashboard.' . Auth::user()->getRoleNames()->first()) }}"
                    class="px-6 py-2 text-white bg-[var(--tip-bleu-accent)] rounded shadow hover:bg-[#105b99] transition">
                    Accéder à mon espace
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="px-6 py-2 text-[var(--tip-bleu)] border border-[var(--tip-bleu)] rounded hover:bg-[var(--tip-bleu)] hover:text-white transition">
                    Se connecter
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="px-6 py-2 text-[var(--tip-bleu)] border border-[var(--tip-bleu)] rounded hover:bg-[var(--tip-bleu)] hover:text-white transition">
                        S'inscrire
                    </a>
                @endif
            @endauth
        </div>
    @endif

    {{-- Bas de page ou mention légale --}}
    <footer class="text-xs text-[var(--tip-gris)] mt-12">
        © {{ date('Y') }} Telecom Infrastructure Partners. Tous droits réservés.
    </footer>

</body>

</html>
