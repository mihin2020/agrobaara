<div class="min-h-screen bg-[#fff8f5]">
    <header class="bg-white border-b border-[#c1c9b6] sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto object-contain" />
            </a>
            <nav class="flex items-center gap-3 text-sm font-semibold">
                <a href="{{ route('home') }}" class="text-[#41493b] hover:text-[#2c6904]">Accueil</a>
                @feature('public_offers')
                    <a href="{{ route('public.offers.index') }}" class="text-[#41493b] hover:text-[#2c6904]">Offres</a>
                @endfeature
                <a href="{{ route('public.news.index') }}" class="text-[#2c6904]">Actualités</a>
            </nav>
        </div>
    </header>

    <div class="max-w-3xl mx-auto py-10 px-4">
        <h1 class="font-sora text-3xl font-bold text-[#1e1b18] mb-2">Actualités</h1>
        <p class="text-sm text-[#717a69] mb-8">Annonces, événements et infos du guichet Agro Eco BAARA.</p>

        <div class="space-y-4">
            @forelse($items as $item)
                <a href="{{ route('public.news.show', $item->slug) }}" wire:navigate
                   class="block p-5 bg-white border border-[#c1c9b6] rounded-2xl hover:border-[#2c6904] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] uppercase font-bold text-[#2c6904] bg-[#aef585]/25 px-2 py-0.5 rounded">{{ $item->kindLabel() }}</span>
                        @if($item->published_at)
                            <span class="text-[11px] text-[#717a69]">{{ $item->published_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <p class="font-semibold text-[#1e1b18] mt-2">{{ $item->title }}</p>
                    @if($item->excerpt)
                        <p class="text-sm text-[#717a69] mt-1">{{ $item->excerpt }}</p>
                    @endif
                </a>
            @empty
                <div class="p-8 text-center bg-white border border-[#c1c9b6] rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-[#c1c9b6]">newspaper</span>
                    <p class="text-sm text-[#717a69] mt-2">Aucune actualité publiée pour le moment.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $items->links() }}</div>
    </div>
</div>
