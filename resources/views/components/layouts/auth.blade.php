<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Agro Eco BAARA' }}</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-[#fff8f5] font-inter antialiased">

<div class="min-h-full flex">

    {{-- Panneau gauche décoratif --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <div class="absolute inset-0" style="background-color: #69a313;"></div>
        <div class="relative z-10 flex flex-col justify-center px-16 text-white">
            <div class="mb-12 flex flex-col items-center">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo" class="h-16 w-auto mb-8"
                     onerror="this.style.display='none'">
                <p class="text-lg opacity-80 leading-relaxed">
                    Connecter les talents et les opportunités en agroécologie au Burkina Faso.
                </p>
            </div>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">person_search</span>
                    </div>
                    <div>
                        <p class="font-semibold">Gestion des candidats</p>
                        <p class="text-sm opacity-70">Suivi complet des profils</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">handshake</span>
                    </div>
                    <div>
                        <p class="font-semibold">Mise en relation</p>
                        <p class="text-sm opacity-70">Matching assisté candidats / offres</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">bar_chart</span>
                    </div>
                    <div>
                        <p class="font-semibold">Tableau de bord</p>
                        <p class="text-sm opacity-70">Indicateurs en temps réel</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panneau droit formulaire --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-md">
            {{-- Logo mobile --}}
            <div class="lg:hidden mb-10 flex justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-14 w-auto"
                     onerror="this.style.display='none'">
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
