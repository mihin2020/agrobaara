@props([
    'message' => 'Chargement...',
])

<div wire:loading.flex {{ $attributes->merge(['class' => 'fixed inset-0 bg-black/10 z-50 items-center justify-center']) }}>
    <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-[#2c6904] animate-spin">progress_activity</span>
        <span class="text-sm font-medium text-[#1e1b18]">{{ $message }}</span>
    </div>
</div>
