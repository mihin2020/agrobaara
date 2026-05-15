@props([
    'label'  => '',
    'field'  => '',
    'value'  => '',
    'rows'   => null,
])

<div>
    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">{{ $label }}</label>
    @if($rows)
        <textarea wire:model.live="{{ $field }}" rows="{{ $rows }}"
                  class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none transition-all">{{ $value }}</textarea>
    @else
        <input wire:model.live="{{ $field }}" type="text" value="{{ $value }}"
               class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
    @endif
</div>