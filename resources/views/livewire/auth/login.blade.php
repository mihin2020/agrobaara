<div>
    <div class="mb-8">
        <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Connexion</h2>
        <p class="mt-2 text-[#41493b]">Accédez à votre espace de gestion.</p>
    </div>

    <form wire:submit="authenticate" class="space-y-5">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-[#1e1b18] mb-1.5">
                Adresse e-mail
            </label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">mail</span>
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    autocomplete="email"
                    placeholder="votre@email.bf"
                    class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border @error('email') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all"
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
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-sm text-[#2c6904] hover:underline font-medium">
                    Mot de passe oublié ?
                </a>
            </div>
            <div class="relative" x-data="{ show: false }">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                <input
                    wire:model.blur="password"
                    id="password"
                    :type="show ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full pl-10 pr-12 py-3 bg-[#fbf2ed] border @error('password') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all"
                />
                <button type="button"
                        @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#717a69] hover:text-[#2c6904]">
                    <span class="material-symbols-outlined text-xl" x-text="show ? 'visibility_off' : 'visibility'"></span>
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
            <input wire:model="remember" id="remember" type="checkbox"
                   class="w-4 h-4 text-[#2c6904] border-[#c1c9b6] rounded focus:ring-[#2c6904]/20" />
            <label for="remember" class="text-sm text-[#41493b]">Se souvenir de moi</label>
        </div>

        {{-- Bouton --}}
        <button type="submit"
                class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#2c6904]/20"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-wait">
            <span wire:loading.remove class="material-symbols-outlined">login</span>
            <span wire:loading class="material-symbols-outlined animate-spin">progress_activity</span>
            <span wire:loading.remove>Se connecter</span>
            <span wire:loading>Connexion...</span>
        </button>

    </form>

    <p class="mt-8 text-center text-xs text-[#717a69]">
        Accès réservé aux agents du guichet Agro Eco BAARA.
    </p>
</div>
