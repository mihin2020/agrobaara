<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion — Agro Eco BAARA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#fff8f5] font-inter antialiased">

<div class="min-h-full flex">

    {{-- Panneau gauche décoratif --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#2c6904] to-[#1a4003]"></div>
        <div class="absolute inset-0 opacity-10"
             style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>
        <div class="relative z-10 flex flex-col justify-center px-16 text-white">
            <div class="mb-12">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Agro Eco BAARA" class="h-16 w-auto mb-8 rounded-xl">
                <h1 class="font-sora text-4xl font-bold leading-tight mb-4">
                    Agro Eco<br/><span class="text-[#93d86c]">BAARA</span>
                </h1>
                <p class="text-lg opacity-80 leading-relaxed">
                    Connecter les talents et les opportunités en agroécologie au Burkina Faso.
                </p>
            </div>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#93d86c]">person_search</span>
                    </div>
                    <div>
                        <p class="font-semibold">Gestion des candidats</p>
                        <p class="text-sm opacity-70">Suivi complet des profils agroécologiques</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ffb870]">handshake</span>
                    </div>
                    <div>
                        <p class="font-semibold">Mise en relation</p>
                        <p class="text-sm opacity-70">Matching assisté candidats / offres</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#93d86c]">bar_chart</span>
                    </div>
                    <div>
                        <p class="font-semibold">Tableau de bord</p>
                        <p class="text-sm opacity-70">Indicateurs en temps réel</p>
                    </div>
                </div>
            </div>
            <div class="mt-auto pt-16">
                <p class="text-sm opacity-50">
                    Association Yelemani · Partenaire CRIC · Financement Min. Intérieur Italien
                </p>
            </div>
        </div>
    </div>

    {{-- Panneau droit — formulaire --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden mb-10 text-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-12 w-auto mx-auto mb-3">
                <span class="font-sora text-2xl font-bold text-[#2c6904]">Agro Eco BAARA</span>
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
        </div>
    </div>
</div>

</body>
</html>
