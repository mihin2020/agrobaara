@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row md:items-end justify-between gap-4']) }}>
    <div>
        <h2 class="font-sora text-xl font-bold text-[#1e1b18]">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-[#41493b] mt-1 text-sm">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
    @isset($actions)
        <div class="flex items-center gap-2 flex-wrap">{{ $actions }}</div>
    @endisset
</div>
