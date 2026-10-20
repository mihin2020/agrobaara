<div class="min-h-screen bg-[#fff8f5]">
    <header class="bg-white border-b border-[#c1c9b6] sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto object-contain" />
            </a>
            <a href="{{ route('public.news.index') }}" class="text-sm font-semibold text-[#2c6904] hover:underline">← Actualités</a>
        </div>
    </header>

    <article class="max-w-3xl mx-auto py-10 px-4">
        <p class="text-[10px] uppercase font-bold text-[#2c6904] tracking-wide">{{ $item->kindLabel() }}</p>
        <h1 class="font-sora text-3xl font-bold text-[#1e1b18] mt-2">{{ $item->title }}</h1>
        @if($item->published_at)
            <p class="text-xs text-[#717a69] mt-2">{{ $item->published_at->format('d/m/Y') }}</p>
        @endif
        <div class="mt-6 bg-white border border-[#c1c9b6] rounded-2xl p-6 text-sm leading-relaxed text-[#1e1b18]">
            {!! nl2br(e($item->body ?: $item->excerpt)) !!}
        </div>
    </article>
</div>
