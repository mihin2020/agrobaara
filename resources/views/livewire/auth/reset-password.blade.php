<div>
    <div class="mb-8">
        <a href="{{ route('login') }}" wire:navigate
           class="text-sm text-[#41493b] hover:text-[#2c6904] flex items-center gap-1 mb-6">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Retour à la connexion
        </a>
        <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Nouveau mot de passe</h2>
        <p class="mt-2 text-[#41493b]">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
            <span class="material-symbols-outlined text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="resetPassword" class="space-y-5">

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Adresse e-mail</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">mail</span>
                <input wire:model="email" type="email" placeholder="votre@email.bf" readonly
                       class="w-full pl-10 pr-4 py-3 bg-[#f5ece7] border border-[#c1c9b6] rounded-xl text-sm text-[#717a69] outline-none cursor-default" />
            </div>
            @error('email')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nouveau mot de passe --}}
        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nouveau mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                <input wire:model="password" type="password" placeholder="••••••••"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border @error('password') border-red-400 @else border-[#c1c9b6] @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
            </div>
            @error('password')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1.5 text-xs text-[#717a69]">8 caractères minimum, majuscule, chiffre et symbole requis.</p>
        </div>

        {{-- Confirmation --}}
        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Confirmer le mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock_reset</span>
                <input wire:model="password_confirmation" type="password" placeholder="••••••••"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
            </div>
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
                class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all shadow-lg shadow-[#2c6904]/20 flex items-center justify-center gap-2">
            <span wire:loading.remove class="material-symbols-outlined text-base">lock_reset</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Réinitialiser le mot de passe</span>
            <span wire:loading>Réinitialisation...</span>
        </button>
    </form>
</div>
