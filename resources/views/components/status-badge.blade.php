@props([
    'status' => null,
    'label' => null,
    'class' => null,
])

@php
    $text = $label
        ?? (is_object($status) && method_exists($status, 'label') ? $status->label() : (string) $status);
    $classes = $class
        ?? (is_object($status) && method_exists($status, 'badgeClass')
            ? $status->badgeClass()
            : 'bg-[#f5ece7] text-[#41493b]');
@endphp

<span {{ $attributes->merge(['class' => 'text-[10px] font-bold px-2 py-1 rounded-full inline-flex items-center ' . $classes]) }}>
    {{ $text }}
</span>
