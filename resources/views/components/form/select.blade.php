@props([
    'label' => '',
    'error' => null,
])

<div>
    @if($label)
        <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">{{ $label }}</label>
    @endif
    <select
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-2.5 bg-[#fbf2ed] border ' . ($error ? 'border-red-400 bg-red-50' : 'border-[#c1c9b6]') . ' rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all'
        ]) }}
    >
        {{ $slot }}
    </select>
    @if($error)
        <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">error</span>
            {{ $error }}
        </p>
    @endif
</div>
