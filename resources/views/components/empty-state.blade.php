@props([
    'icon' => 'inbox',
    'title' => 'Aucun élément',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'px-4 py-12 text-center']) }}>
    <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">{{ $icon }}</span>
    <p class="text-[#717a69] font-medium text-sm">{{ $title }}</p>
    @if($description)
        <p class="text-xs text-[#717a69] mt-1">{{ $description }}</p>
    @endif
    @isset($actions)
        <div class="mt-3">{{ $actions }}</div>
    @endisset
</div>
