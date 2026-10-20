<div class="space-y-6">
    <x-page-header title="Offres à valider" />

    @if($autoMatch)
        <p class="text-xs text-[#717a69]">Phase 2 active : après publication, le top 5 matching auto sera disponible sur la fiche offre.</p>
    @endif

    <div class="bg-white border border-[#c1c9b6] rounded-2xl divide-y">
        @forelse($offers as $o)
            <div class="p-4 flex flex-wrap justify-between gap-3 items-center">
                <div>
                    <p class="font-semibold">{{ $o->title }}</p>
                    <p class="text-xs text-[#717a69]">{{ $o->company?->name }} · {{ $o->reference }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.offers.show', $o) }}" wire:navigate class="px-3 py-1.5 text-xs border rounded-lg">Voir</a>
                    <button wire:click="approve('{{ $o->id }}')" class="px-3 py-1.5 text-xs bg-[#2c6904] text-white rounded-lg font-semibold">Publier</button>
                    <button wire:click="reject('{{ $o->id }}')" class="px-3 py-1.5 text-xs border border-red-300 text-red-700 rounded-lg">Rejeter</button>
                </div>
            </div>
        @empty
            <x-empty-state icon="pending_actions" title="Aucune offre en attente." />
        @endforelse
    </div>

    @if($offers->hasPages())
        <div class="pt-2">{{ $offers->links() }}</div>
    @endif
</div>
