<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="font-sora text-2xl font-bold text-[#1e1b18]">Activation Phase 2</h1>
        <p class="text-sm text-[#41493b] mt-1">Réservé au compte maître système. L’interrupteur global active aussi le matching automatique top-5.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-[#aef585]/30 border border-[#2c6904]/30 text-[#2c6904] px-4 py-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-[#c1c9b6] rounded-2xl p-6 space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-[#1e1b18]">Interrupteur global Phase 2</p>
                <p class="text-xs text-[#717a69] mt-0.5">Si désactivé, toutes les cases ci-dessous sont ignorées.</p>
            </div>
            <button type="button" wire:click="toggleMaster"
                    class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors {{ $phase2_master ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}">
                <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow transition {{ $phase2_master ? 'translate-x-7' : 'translate-x-1' }}"></span>
            </button>
        </div>
    </div>

    <form wire:submit="save" class="bg-white border border-[#c1c9b6] rounded-2xl p-6 space-y-4">
        <p class="text-sm font-semibold text-[#1e1b18]">Fonctionnalités individuelles</p>

        @foreach([
            'self_registration' => 'Inscriptions publiques',
            'portal_candidate' => 'Portail candidat',
            'portal_company' => 'Portail entreprise',
            'portal_training_center' => 'Portail centre de formation',
            'public_offers' => 'Offres publiques',
            'testimonials' => 'Témoignages',
            'news_ribbon' => 'Actualités & bandeau',
        ] as $key => $fallbackLabel)
            <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-[#fbf2ed]/60 cursor-pointer">
                <input type="checkbox" wire:model="{{ $key }}" class="mt-1 rounded border-[#c1c9b6] text-[#2c6904] focus:ring-[#2c6904]" />
                <span>
                    <span class="block text-sm font-medium text-[#1e1b18]">{{ $flagMeta[$key]->label ?? $fallbackLabel }}</span>
                    <span class="block text-xs text-[#717a69]">{{ $flagMeta[$key]->description ?? '' }}</span>
                </span>
            </label>
        @endforeach

        <div class="pt-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-[#2c6904] text-white font-semibold rounded-xl text-sm hover:bg-[#245503] transition-colors">
                Enregistrer
            </button>
        </div>
    </form>
</div>
