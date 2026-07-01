<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Agro Eco BAARA</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#fff8f5] font-inter antialiased">

<div class="min-h-full flex">

    {{-- Panneau gauche décoratif --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <div class="absolute inset-0" style="background-color: #69a313;"></div>
        <div class="relative z-10 flex flex-col items-center justify-center px-16 text-white w-full">
            <div class="mb-12 w-full flex justify-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Agro Eco BAARA" class="w-auto rounded-2xl" style="height: 9.5rem;">
            </div>
            <div class="space-y-8 w-full">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-3xl text-white">person_search</span>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Gestion des candidats</p>
                        <p class="text-base opacity-70 mt-0.5">Suivi complet des profils</p>
                    </div>
                </div>
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-3xl text-white">handshake</span>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Mise en relation</p>
                        <p class="text-base opacity-70 mt-0.5">Matching assisté candidats / offres</p>
                    </div>
                </div>
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-3xl text-white">bar_chart</span>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Tableau de bord</p>
                        <p class="text-base opacity-70 mt-0.5">Indicateurs en temps réel</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panneau droit - formulaire --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden mt-4 mb-8 flex justify-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-24 w-auto mx-auto">
            </div>

            {{-- Message succès --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8">
                <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Connexion</h2>
                <p class="mt-2 text-[#41493b]">Accédez à votre espace de gestion.</p>
            </div>

            {{-- FORMULAIRE CLASSIQUE HTML --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#1e1b18] mb-1.5">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">mail</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            placeholder="votre@email.bf"
                            class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-[#1e1b18]">
                            Mot de passe
                        </label>
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-[#2c6904] hover:underline font-medium">
                            Mot de passe oublié ?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-12 py-3 bg-[#fbf2ed] border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all"
                        />
                        <button type="button"
                                onclick="const i=document.getElementById('password');i.type=i.type==='password'?'text':'password';this.querySelector('span').textContent=i.type==='password'?'visibility':'visibility_off'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#717a69] hover:text-[#2c6904]">
                            <span class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Se souvenir --}}
                <div class="flex items-center gap-2">
                    <input id="remember" name="remember" type="checkbox" value="1"
                           class="w-4 h-4 text-[#2c6904] border-[#c1c9b6] rounded focus:ring-[#2c6904]/20" />
                    <label for="remember" class="text-sm text-[#41493b]">Se souvenir de moi</label>
                </div>

                {{-- Bouton --}}
                <button type="submit"
                        class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#2c6904]/20">
                    <span class="material-symbols-outlined">login</span>
                    Se connecter
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-[#717a69]">
                Accès réservé aux agents du guichet Agro Eco BAARA.
            </p>

            <a href="{{ route('home') }}" class="mt-4 flex items-center justify-center gap-1 text-sm text-[#2c6904] hover:underline font-medium">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>

</body>
</html>
