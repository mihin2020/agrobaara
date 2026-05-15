<div>
    <div class="mb-8">
        <div class="w-14 h-14 bg-[#2c6904]/10 rounded-2xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-[#2c6904] text-3xl">key</span>
        </div>
        <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Définissez votre mot de passe</h2>
        <p class="mt-2 text-[#41493b]">Pour sécuriser votre compte, veuillez choisir un mot de passe personnel.</p>
    </div>

    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
        <p class="font-semibold mb-1">Exigences du mot de passe :</p>
        <ul class="space-y-1 text-xs">
            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check</span> Au moins 8 caractères</li>
            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check</span> Au moins une majuscule et une minuscule</li>
            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check</span> Au moins un chiffre</li>
            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check</span> Au moins un caractère spécial (! @ # $...)</li>
        </ul>
    </div>

    <form wire:submit="save" class="space-y-5">
        <div x-data="{ show: false }">
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nouveau mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                <input wire:model.blur="password" :type="show ? 'text' : 'password'"
                       class="w-full pl-10 pr-12 py-3 bg-[#fbf2ed] border @error('password') border-red-400 @else border-[#c1c9b6] @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#717a69]">
                    <span class="material-symbols-outlined text-xl" x-text="show ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Confirmer le mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock_reset</span>
                <input wire:model.blur="password_confirmation" type="password"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all shadow-lg shadow-[#2c6904]/20"
                wire:loading.attr="disabled">
            <span wire:loading.remove>Enregistrer et accéder au back-office</span>
            <span wire:loading>Enregistrement...</span>
        </button>
    </form>
</div>
