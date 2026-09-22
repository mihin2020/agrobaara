@props([
    'skills',
    'selected' => [],
    'model' => 'skill_ids',
    'accent' => 'offer', // offer|candidate|company
])

@php
    $styles = match ($accent) {
        'candidate' => [
            'input' => 'focus:ring-[#2c6904]/20 focus:border-[#2c6904]',
            'idle'  => 'border-[#c1c9b6] bg-white text-[#41493b] hover:border-[#2c6904]/50',
            'on'    => 'border-[#2c6904] bg-[#aef585]/20 text-[#2c6904]',
        ],
        'company' => [
            'input' => 'focus:ring-[#2c6904]/20 focus:border-[#2c6904]',
            'idle'  => 'bg-white text-[#41493b] border-[#c1c9b6] hover:border-[#2c6904]/50',
            'on'    => 'bg-[#2c6904] text-white border-[#2c6904]',
        ],
        default => [
            'input' => 'focus:ring-[#615c47]/20 focus:border-[#615c47]',
            'idle'  => 'border-[#c1c9b6] bg-white text-[#41493b] hover:border-[#615c47]/50',
            'on'    => 'border-[#615c47] bg-[#ebe2c8]/30 text-[#615c47]',
        ],
    };
@endphp

<div data-skills-root {{ $attributes }}>
    <div class="relative mb-3">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
        <input type="search"
               placeholder="Rechercher une compétence..."
               autocomplete="off"
               oninput="
                   const root = this.closest('[data-skills-root]');
                   const q = this.value.trim().toLowerCase();
                   root.querySelectorAll('[data-skill-name]').forEach((el) => {
                       const name = (el.getAttribute('data-skill-name') || '').toLowerCase();
                       el.style.display = (!q || name.includes(q)) ? '' : 'none';
                   });
               "
               class="w-full pl-9 pr-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 {{ $styles['input'] }}" />
    </div>

    <div class="flex flex-wrap gap-2 max-h-52 overflow-y-auto p-3 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6]">
        @foreach($skills as $skill)
            @php $isOn = in_array($skill->id, $selected, true) || in_array((string) $skill->id, array_map('strval', $selected), true); @endphp
            <button type="button"
                    wire:key="skill-pick-{{ $model }}-{{ $skill->id }}"
                    wire:click="toggleSkill('{{ $skill->id }}')"
                    data-skill-name="{{ $skill->name }}"
                    class="px-2.5 py-1.5 rounded-lg border text-xs font-semibold transition-all {{ $isOn ? $styles['on'] : $styles['idle'] }}">
                {{ $skill->name }}
            </button>
        @endforeach
    </div>
</div>
