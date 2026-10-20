<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Vérification 2FA - Agro Eco BAARA</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#fff8f5] font-inter antialiased">

<div class="min-h-full flex">
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <div class="absolute inset-0" style="background-color: #69a313;"></div>
        <div class="relative z-10 flex flex-col items-center justify-center px-16 text-white w-full">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Agro Eco BAARA" class="w-auto rounded-2xl mb-10" style="height: 9.5rem;">
            <p class="text-lg opacity-90 text-center max-w-sm">
                Sécurisez votre accès avec le code reçu par e-mail.
            </p>
        </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-md">
            <div class="lg:hidden mb-8 flex justify-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-24 w-auto">
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">mark_email_read</span>
                    {{ session('success') }}
                </div>
            @endif

            @if (app()->environment('local') && session('2fa_dev_code'))
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-sm">
                    <p class="font-semibold mb-1">Mode local — code de test</p>
                    <p class="text-2xl font-bold tracking-[0.35em] text-center">{{ session('2fa_dev_code') }}</p>
                    <p class="mt-2 text-xs text-amber-800">Visible uniquement en local. En prod, seul l’e-mail compte.</p>
                </div>
            @endif

            <div class="mb-8">
                <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Vérification</h2>
                <p class="mt-2 text-[#41493b] text-sm">
                    Saisissez le code à 6 chiffres envoyé à
                    <span class="font-semibold text-[#1e1b18]">{{ $emailMask }}</span>.
                </p>
            </div>

            <form action="{{ route('login.2fa.verify') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Code de validation</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        autofocus
                        placeholder="••••••"
                        class="w-full px-4 py-3 bg-[#fbf2ed] border {{ $errors->has('code') ? 'border-red-400 bg-red-50' : 'border-[#c1c9b6]' }} rounded-xl text-center text-2xl tracking-[0.4em] font-bold focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]"
                    />
                    @error('code')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#2c6904]/20">
                    <span class="material-symbols-outlined">verified_user</span>
                    Valider et se connecter
                </button>
            </form>

            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
                <form action="{{ route('login.2fa.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[#2c6904] font-semibold hover:underline">
                        Renvoyer le code
                    </button>
                </form>
                <form action="{{ route('login.2fa.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[#717a69] hover:underline">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
