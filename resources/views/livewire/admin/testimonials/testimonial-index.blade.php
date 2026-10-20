<div class="space-y-6">
    <x-page-header title="Témoignages" />

    <div class="space-y-3">
        @forelse($items as $t)
            <x-section-card>
                <div class="flex justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ $t->author_name }} <span class="text-xs font-normal text-[#717a69]">{{ $t->role_label }}</span></p>
                        <p class="text-sm mt-2 text-[#41493b]">{{ $t->body }}</p>
                        <p class="text-[11px] mt-2 uppercase font-bold tracking-wide
                            {{ $t->status === 'published' ? 'text-[#2c6904]' : ($t->status === 'pending' ? 'text-amber-700' : 'text-gray-500') }}">
                            {{ $t->status }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        @if($t->status !== 'published')
                            <button wire:click="approve('{{ $t->id }}')" class="px-3 py-1.5 text-xs bg-[#2c6904] text-white rounded-lg">Publier</button>
                        @else
                            <button wire:click="unpublish('{{ $t->id }}')" class="px-3 py-1.5 text-xs border rounded-lg">Dépublier</button>
                        @endif
                    </div>
                </div>
            </x-section-card>
        @empty
            <x-empty-state icon="format_quote" title="Aucun témoignage." />
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="pt-2">{{ $items->links() }}</div>
    @endif
</div>
