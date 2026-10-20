<div class="max-w-3xl mx-auto py-10 px-4">
    <a href="{{ route('public.offers.index') }}" wire:navigate class="text-sm text-[#2c6904] font-semibold">← Offres</a>
    <h1 class="text-2xl font-bold mt-3">{{ $offer->title }}</h1>
    <p class="text-sm text-[#717a69] mt-1">{{ $offer->company?->name }} · {{ $offer->contract_type?->label() ?? $offer->contract_type }} · {{ $offer->reference }}</p>
    <div class="mt-6 prose prose-sm max-w-none bg-white border border-[#c1c9b6] rounded-2xl p-6">
        {!! nl2br(e($offer->mission_description)) !!}
    </div>
    @if($offer->skills->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach($offer->skills as $skill)
                <span class="text-xs px-2.5 py-1 rounded-full bg-[#aef585]/20 text-[#2c6904] border border-[#2c6904]/20">{{ $skill->name }}</span>
            @endforeach
        </div>
    @endif
</div>
