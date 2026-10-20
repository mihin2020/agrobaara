<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Espace - Agro Eco BAARA' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    @livewireStyles
</head>
<body class="bg-[#fbf2ed] text-[#1e1b18] min-h-screen">
    <header class="bg-white border-b border-[#c1c9b6] sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto object-contain" />
                <span class="font-semibold text-sm hidden sm:inline">Mon espace</span>
            </a>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-[#41493b] hidden sm:inline">{{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-[#c1c9b6] hover:bg-[#f5ece7] text-xs font-semibold">Déconnexion</button>
                </form>
            </div>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>
    <x-toast />
    @livewireScripts
</body>
</html>
