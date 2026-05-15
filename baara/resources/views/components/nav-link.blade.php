@props([
    'href'   => '#',
    'icon'   => 'circle',
    'active' => false,
])

<a href="{{ $href }}" wire:navigate
   @class([
       'flex items-center gap-3 py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors',
       'bg-[#448322] text-white shadow-sm' => $active,
       'text-[#41493b] hover:bg-[#e9e1dc] hover:text-[#1e1b18]' => !$active,
   ])>
    <span @class([
        'material-symbols-outlined text-xl',
        "font-variation-settings: 'FILL' 1" => $active,
    ])>{{ $icon }}</span>
    {{ $slot }}
</a>
