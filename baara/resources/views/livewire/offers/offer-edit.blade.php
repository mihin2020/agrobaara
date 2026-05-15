<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.offers.show', $offer) }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Modifier l'offre</h2>
            <p class="text-[#41493b] text-sm mt-0.5">{{ $offer->title }} · <span class="font-mono text-xs">{{ $offer->reference }}</span></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-amber-300 p-8 text-center">
        <span class="material-symbols-outlined text-4xl text-amber-400 mb-3 block">build_circle</span>
        <h3 class="font-sora text-base font-bold text-[#1e1b18] mb-2">Formulaire d'édition en cours de finalisation</h3>
        <p class="text-[#41493b] text-sm mb-4">Le formulaire d'édition complet de l'offre sera disponible prochainement.</p>
        <a href="{{ route('admin.offers.show', $offer) }}" wire:navigate
           class="inline-flex items-center gap-2 bg-[#615c47] text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Retour à l'offre
        </a>
    </div>
</div>