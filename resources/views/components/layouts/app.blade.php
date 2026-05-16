<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Back-office — Agro Eco BAARA' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=JetBrains+Mono:wght@500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Barre de progression navigation */
        [wire\:loading-bar] { position:fixed;top:0;left:0;height:3px;background:#2c6904;z-index:9999;transition:width .2s; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-[#fff8f5] font-inter antialiased text-[#1e1b18]">

{{-- Barre de chargement globale --}}
<div wire:loading.delay wire:loading.attr="aria-busy"
     class="fixed top-0 left-0 right-0 z-[9999] h-[3px] bg-[#2c6904]/20 overflow-hidden">
    <div class="h-full bg-[#2c6904] animate-[progress_1s_ease-in-out_infinite]"
         style="width:40%;animation:progress 1s ease-in-out infinite;"></div>
</div>
<style>@keyframes progress{0%{transform:translateX(-100%)}100%{transform:translateX(350%)}}</style>

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- ── Sidebar mobile overlay ── --}}
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden">
    </div>

    {{-- ── Sidebar ── --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-64 flex-shrink-0 flex flex-col bg-[#f5ece7] border-r border-[#c1c9b6] transform transition-transform duration-200 ease-in-out lg:relative lg:translate-x-0">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-[#c1c9b6]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-[#2c6904] rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-lg">eco</span>
                </div>
                <div>
                    <h1 class="font-sora text-base font-bold text-[#2c6904] leading-none">BAARA Ops</h1>
                    <p class="text-[10px] text-[#41493b] uppercase tracking-wider font-medium mt-0.5">Gestion des talents</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <x-nav-link href="{{ route('admin.dashboard') }}" icon="dashboard" :active="request()->routeIs('admin.dashboard')">
                Tableau de bord
            </x-nav-link>

            <x-nav-link href="{{ route('admin.candidates.index') }}" icon="person_search" :active="request()->routeIs('admin.candidates.*')">
                Candidats
            </x-nav-link>

            <x-nav-link href="{{ route('admin.companies.index') }}" icon="domain" :active="request()->routeIs('admin.companies.*')">
                Entreprises
            </x-nav-link>

            <x-nav-link href="{{ route('admin.offers.index') }}" icon="work_outline" :active="request()->routeIs('admin.offers.*')">
                Offres
            </x-nav-link>

            <x-nav-link href="{{ route('admin.matches.index') }}" icon="handshake" :active="request()->routeIs('admin.matches.*')">
                Matching
            </x-nav-link>

            @php
                $user = auth()->user();
                $hasAdminSection = $user->hasAnyPermission(['users.view', 'roles.manage', 'audit.view', 'settings.view']);
            @endphp

            @if($hasAdminSection)
                <div class="pt-4 pb-1.5 px-4">
                    <p class="text-[10px] font-bold text-[#717a69] uppercase tracking-widest">Administration</p>
                </div>

                @if($user->hasPermission('users.view'))
                    <x-nav-link href="{{ route('admin.admin.users.index') }}" icon="manage_accounts" :active="request()->routeIs('admin.admin.users.*')">
                        Utilisateurs
                    </x-nav-link>
                @endif

                @if($user->hasPermission('roles.manage'))
                    <x-nav-link href="{{ route('admin.admin.roles.index') }}" icon="admin_panel_settings" :active="request()->routeIs('admin.admin.roles.*')">
                        Rôles & Permissions
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.admin.landing.configurator') }}" icon="web" :active="request()->routeIs('admin.admin.landing.*')">
                        Config. Landing
                    </x-nav-link>
                @endif

                @if($user->hasPermission('audit.view'))
                    <x-nav-link href="{{ route('admin.admin.audit.index') }}" icon="history" :active="request()->routeIs('admin.admin.audit.*')">
                        Journal d'audit
                    </x-nav-link>
                @endif

                @if($user->hasPermission('settings.view'))
                    <x-nav-link href="{{ route('admin.admin.settings.index') }}" icon="settings" :active="request()->routeIs('admin.admin.settings.*')">
                        Paramètres
                    </x-nav-link>
                @endif
            @endif
        </nav>

    </aside>

    {{-- ── Contenu principal ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TopBar --}}
        <header class="sticky top-0 z-20 bg-[#fff8f5] border-b border-[#c1c9b6]">
            <div class="flex items-center justify-between px-4 md:px-8 py-3.5">

                {{-- Burger mobile + Recherche --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-lg">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div class="relative hidden sm:block">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
                        <input type="text"
                               placeholder="Rechercher un candidat, une entreprise..."
                               class="pl-10 pr-4 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] w-72 lg:w-96 transition-all" />
                    </div>
                </div>

                {{-- Actions topbar + User --}}
                <div class="flex items-center gap-3">
                    {{-- Nouveau Dossier --}}
                    <a href="{{ route('admin.candidates.create') }}" wire:navigate
                       class="hidden sm:flex items-center gap-1.5 px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span class="hidden md:inline">Nouveau Dossier</span>
                    </a>

                    {{-- Notifications --}}
                    <button class="relative p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-full transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div class="h-6 w-px bg-[#c1c9b6] mx-1 hidden sm:block"></div>

                    {{-- Profil dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                        <button @click="profileOpen = !profileOpen"
                                class="flex items-center gap-2.5 py-1.5 px-2 rounded-xl hover:bg-[#f5ece7] transition-colors">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-semibold text-[#1e1b18] leading-none">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-[#41493b] mt-0.5">
                                    {{ auth()->user()->roles->first()?->name ?? 'Utilisateur' }}
                                </p>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-[#2c6904] flex items-center justify-center text-white font-bold text-sm overflow-hidden border-2 border-[#93d86c] flex-shrink-0">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover" />
                                @else
                                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="material-symbols-outlined text-base text-[#717a69] hidden sm:inline"
                                  :class="profileOpen ? 'rotate-180' : ''"
                                  style="transition: transform .2s">expand_more</span>
                        </button>

                        {{-- Dropdown menu --}}
                        <div x-show="profileOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                             class="absolute right-0 mt-2 w-56 bg-white border border-[#c1c9b6] rounded-2xl shadow-lg z-50 overflow-hidden origin-top-right">

                            {{-- Identité --}}
                            <div class="px-4 py-3 border-b border-[#f0e8e2]">
                                <p class="text-sm font-semibold text-[#1e1b18] truncate">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-[#717a69] truncate mt-0.5">{{ auth()->user()->email }}</p>
                            </div>

                            {{-- Liens --}}
                            <div class="py-1.5">
                                <a href="{{ route('admin.profile') }}" wire:navigate
                                   @click="profileOpen = false"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1e1b18] hover:bg-[#f5ece7] transition-colors">
                                    <span class="material-symbols-outlined text-base text-[#41493b]">person</span>
                                    Mon profil
                                </a>
                                <a href="{{ route('admin.profile') }}#securite" wire:navigate
                                   @click="profileOpen = false"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1e1b18] hover:bg-[#f5ece7] transition-colors">
                                    <span class="material-symbols-outlined text-base text-[#41493b]">lock</span>
                                    Changer le mot de passe
                                </a>
                            </div>

                            {{-- Déconnexion --}}
                            <div class="border-t border-[#f0e8e2] py-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <span class="material-symbols-outlined text-base">logout</span>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-[1280px] mx-auto px-4 md:px-8 py-6 md:py-8">
                {{ $slot }}
            </div>
        </main>

    </div>
</div>

@livewireScripts
</body>
</html>
