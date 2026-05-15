@props([
    'label'       => '',
    'placeholder' => '',
    'rows'        => 3,
    'error'       => null,
])

<div>
    @if($label)
        <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">{{ $label }}</label>
    @endif
    <textarea
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-2.5 bg-[#fbf2ed] border ' . ($error ? 'border-red-400 bg-red-50' : 'border-[#c1c9b6]') . ' rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all resize-none'
        ]) }}
    ></textarea>
    @if($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
