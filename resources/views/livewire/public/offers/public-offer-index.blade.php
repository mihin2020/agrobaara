<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-4">Offres disponibles</h1>
    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Rechercher..."
           class="w-full max-w-md mb-6 px-4 py-2.5 border border-[#c1c9b6] rounded-xl bg-white text-sm" />
    <div class="grid gap-4 sm:grid-cols-2">
        @forelse($offers as $offer)
            <a href="{{ route('public.offers.show', $offer) }}" wire:navigate
               class="block p-5 bg-white border border-[#c1c9b6] rounded-2xl hover:border-[#2c6904] transition">
                <p class="font-semibold text-[#1e1b18]">{{ $offer->title }}</p>
                <p class="text-xs text-[#717a69] mt-1">{{ $offer->company?->name }} · {{ $offer->contract_type?->label() ?? $offer->contract_type }}</p>
                @if($offer->published_at)
                    <p class="text-[11px] text-[#717a69] mt-2">Publiée le {{ $offer->published_at->format('d/m/Y') }}</p>
                @endif
            </a>
        @empty
            <p class="text-sm text-[#717a69] col-span-2">Aucune offre publiée pour le moment.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $offers->links() }}</div>
</div>
