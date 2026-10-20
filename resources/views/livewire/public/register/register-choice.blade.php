<div>
    <div class="mb-8">
        <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">Inscription</h2>
        <p class="mt-2 text-[#41493b]">Choisissez le type de compte. Validation par un opérateur avant activation.</p>
    </div>

    <div class="space-y-3">
        <a href="{{ route('register.candidate') }}" wire:navigate
           class="flex items-center gap-4 p-4 rounded-2xl border border-[#c1c9b6] bg-[#fbf2ed] hover:border-[#2c6904] hover:bg-[#aef585]/15 transition-all">
            <div class="w-12 h-12 rounded-xl bg-[#2c6904]/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#2c6904] text-2xl">person</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-[#1e1b18]">Candidat</p>
                <p class="text-xs text-[#717a69] mt-0.5">Profil, offres pertinentes, contrats</p>
            </div>
            <span class="material-symbols-outlined text-[#717a69]">chevron_right</span>
        </a>

        <a href="{{ route('register.company') }}" wire:navigate
           class="flex items-center gap-4 p-4 rounded-2xl border border-[#c1c9b6] bg-[#fbf2ed] hover:border-[#2c6904] hover:bg-[#aef585]/15 transition-all">
            <div class="w-12 h-12 rounded-xl bg-[#2c6904]/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#2c6904] text-2xl">domain</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-[#1e1b18]">Entreprise</p>
                <p class="text-xs text-[#717a69] mt-0.5">Publier des offres (après validation)</p>
            </div>
            <span class="material-symbols-outlined text-[#717a69]">chevron_right</span>
        </a>

        <a href="{{ route('register.center') }}" wire:navigate
           class="flex items-center gap-4 p-4 rounded-2xl border border-[#c1c9b6] bg-[#fbf2ed] hover:border-[#2c6904] hover:bg-[#aef585]/15 transition-all">
            <div class="w-12 h-12 rounded-xl bg-[#2c6904]/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#2c6904] text-2xl">school</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-[#1e1b18]">Centre de formation</p>
                <p class="text-xs text-[#717a69] mt-0.5">Offres + candidats formés</p>
            </div>
            <span class="material-symbols-outlined text-[#717a69]">chevron_right</span>
        </a>
    </div>

    <p class="mt-8 text-center text-sm text-[#41493b]">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="text-[#2c6904] font-semibold hover:underline">Se connecter</a>
    </p>

    <a href="{{ route('home') }}" class="mt-4 flex items-center justify-center gap-1 text-sm text-[#2c6904] hover:underline font-medium">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Retour à l'accueil
    </a>
</div>
