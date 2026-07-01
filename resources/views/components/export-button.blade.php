<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2 bg-white border border-[#c1c9b6] text-[#41493b] py-2.5 px-4 rounded-xl font-semibold text-sm hover:bg-[#f5ece7] transition-colors">
        <span class="material-symbols-outlined text-lg">download</span>
        Excel
        <span class="material-symbols-outlined text-sm" x-text="open ? 'expand_less' : 'expand_more'"></span>
    </button>
    <div x-show="open" @click.away="open = false" x-transition
         class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-[#c1c9b6] p-4 z-50 space-y-3">
        <p class="text-xs font-bold text-[#1e1b18] uppercase tracking-wide">Période d'export</p>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="text-[10px] font-semibold text-[#717a69]">Du</label>
                <input wire:model="exportFrom" type="date"
                       class="w-full px-2 py-1.5 text-xs border border-[#c1c9b6] rounded-lg focus:outline-none focus:ring-1 focus:ring-[#2c6904]/30 focus:border-[#2c6904]" />
            </div>
            <div>
                <label class="text-[10px] font-semibold text-[#717a69]">Au</label>
                <input wire:model="exportTo" type="date"
                       class="w-full px-2 py-1.5 text-xs border border-[#c1c9b6] rounded-lg focus:outline-none focus:ring-1 focus:ring-[#2c6904]/30 focus:border-[#2c6904]" />
            </div>
        </div>
        <p class="text-[10px] text-[#717a69]">Laissez vide pour exporter tout.</p>
        <button wire:click="export" @click="open = false" type="button"
                class="w-full flex items-center justify-center gap-2 bg-[#2c6904] text-white py-2 px-4 rounded-lg font-semibold text-xs hover:bg-[#448322] transition-colors">
            <span class="material-symbols-outlined text-sm">download</span>
            Télécharger
        </button>
    </div>
</div>
