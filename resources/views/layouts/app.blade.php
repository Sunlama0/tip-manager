<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'TIP Manager') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tipblue: '#003DA5',
                        tiplight: '#009CDE',
                        graylight: '#F4F4F4',
                        darktext: '#333333',
                        active: '#E5F0FB',
                    },
                    fontFamily: {
                        montserrat: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="font-montserrat bg-graylight text-darktext">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-100">
                <img src="{{ asset('images/logo-tip.png') }}" alt="Logo TIP" class="h-10 object-contain mx-auto">
            </div>
            <nav class="flex-1 overflow-y-auto px-4 pt-6 text-sm">
                @php
                    $role = auth()->user()->roles->first()?->name ?? 'invité';

                    $menus = [
                        'admin' => [
                            ['route' => route('dashboard.admin'), 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                            ['route' => route('admin.settings'), 'icon' => 'settings', 'label' => 'Paramètres système'],
                        ],
                        'directeur' => [
                            [
                                'route' => route('dashboard.directeur'),
                                'icon' => 'layout-dashboard',
                                'label' => 'Dashboard',
                            ],
                            [
                                'route' => route('import.index'),
                                'icon' => 'upload',
                                'label' => 'Importation',
                            ],
                            [
                                'route' => route('import.packs'),
                                'icon' => 'folder-open',
                                'label' => 'Packs',
                            ],
                            [
                                'route' => route('exports.index'),
                                'icon' => 'download',
                                'label' => 'Exports',
                            ],
                        ],
                        'responsable' => [
                            [
                                'route' => route('dashboard.responsable'),
                                'icon' => 'layout-dashboard',
                                'label' => 'Dashboard',
                            ],
                            [
                                'route' => route('import.packs'),
                                'icon' => 'folder-open',
                                'label' => 'Packs à gérer',
                            ],
                            [
                                'route' => route('import.index'),
                                'icon' => 'upload',
                                'label' => 'Nouvel import',
                            ],
                            [
                                'route' => '#',
                                'icon' => 'edit',
                                'label' => 'Complétion / Suivi',
                            ],
                        ],
                        'collaborateur' => [
                            [
                                'route' => route('dashboard.collaborateur'),
                                'icon' => 'layout-dashboard',
                                'label' => 'Dashboard',
                            ],
                            [
                                'route' => route('completion.index'),
                                'icon' => 'list-checks',
                                'label' => 'Mes Packs',
                            ],
                        ],
                    ];

                    $menu = $menus[$role] ?? [];
                @endphp

                @foreach ($menu as $item)
                    <a href="{{ $item['route'] }}"
                        class="flex items-center gap-3 py-2 px-3 rounded-lg mb-2 transition-all
                   {{ request()->is(ltrim($item['route'], '#')) ? 'bg-active text-tipblue font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="{{ $item['icon'] }}"
                            class="w-5 h-5 {{ request()->is(ltrim($item['route'], '#')) ? 'text-tipblue' : 'text-gray-400' }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-gray-100 p-4 text-xs text-gray-500">
                Connecté en tant que <br>
                <strong>{{ auth()->user()->name }}</strong><br>
                <span class="text-gray-400">{{ ucfirst(auth()->user()->roles->first()?->name ?? 'Utilisateur') }}</span>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 relative z-10">

                <!-- Titre -->
                <div class="text-lg font-semibold text-tipblue">
                    @yield('page-title', 'Dashboard')
                </div>

                <!-- Zone d’actions -->
                <div class="flex items-center gap-6">

                    <!-- Icônes -->
                    <div class="flex items-center gap-4 text-gray-500">
                        <i data-lucide="search" class="w-5 h-5 hover:text-tipblue cursor-pointer"></i>
                        <i data-lucide="bell" class="w-5 h-5 hover:text-tipblue cursor-pointer relative">
                            {{-- Badge notification --}}
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </i>
                        <i data-lucide="info" class="w-5 h-5 hover:text-tipblue cursor-pointer"></i>
                    </div>

                    <!-- Dropdown utilisateur -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open"
                            class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-tipblue">
                            <img src="{{ asset('images/logo-tip.png') }}" class="w-8 h-8 rounded-full object-cover"
                                alt="Logo">
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Menu déroulant -->
                        <div x-show="open"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg text-sm z-20">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="font-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ ucfirst(auth()->user()->roles->first()?->name ?? 'Utilisateur') }}</div>
                            </div>
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
