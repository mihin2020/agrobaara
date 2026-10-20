@props([
    'icon',
    'title' => null,
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#615c47] rounded-lg transition-colors';
@endphp

@if($href)
    <a href="{{ $href }}" wire:navigate title="{{ $title }}" {{ $attributes->merge(['class' => $base]) }}>
        <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
    </a>
@else
    <button type="button" title="{{ $title }}" {{ $attributes->merge(['class' => $base]) }}>
        <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
    </button>
@endif
