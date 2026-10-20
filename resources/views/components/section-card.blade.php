@props([
    'title' => null,
    'icon' => null,
])

<section {{ $attributes->merge(['class' => 'bg-white border border-[#c1c9b6] rounded-2xl p-5 shadow-sm']) }}>
    @if($title || isset($header))
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2 min-w-0">
                @if($icon)
                    <span class="material-symbols-outlined text-[#2c6904] text-lg">{{ $icon }}</span>
                @endif
                @if($title)
                    <h2 class="font-semibold text-sm text-[#1e1b18] truncate">{{ $title }}</h2>
                @endif
                {{ $header ?? '' }}
            </div>
            @isset($actions)
                <div class="flex items-center gap-2 flex-shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    {{ $slot }}
</section>
