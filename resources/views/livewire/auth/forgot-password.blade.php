<div>
    @if ($sent)
        <div class="text-center py-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-green-600 text-4xl">mark_email_read</span>
            </div>
            <h2 class="font-sora text-2xl font-bold text-[#1e1b18] mb-3">E-mail envoyé</h2>
            <p class="text-[#41493b] mb-8">
                Si un compte correspond à cette adresse, vous recevrez un lien de réinitialisation sous peu.
            </p>
            <a href="{{ route('login') }}" wire:navigate
               class="text-[#2c6904] font-semibold hover:underline flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Retour à la connexion
            </a>
        </div>
    @else
        <div class="mb-8">
            <a href="{{ route('login') }}" wire:navigate
               class="text-sm text-[#41493b] hover:text-[#2c6904] flex items-center gap-1 mb-6">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Retour
            </a>
            <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Mot de passe oublié</h2>
            <p class="mt-2 text-[#41493b]">Saisissez votre e-mail pour recevoir un lien de réinitialisation.</p>
        </div>

        <form wire:submit="sendResetLink" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Adresse e-mail</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">mail</span>
                    <input wire:model="email" type="email" placeholder="votre@email.bf"
                           class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border @error('email') border-red-400 @else border-[#c1c9b6] @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                </div>
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all shadow-lg shadow-[#2c6904]/20"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Envoyer le lien</span>
                <span wire:loading>Envoi en cours...</span>
            </button>
        </form>
    @endif
</div>
