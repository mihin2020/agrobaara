<div class="space-y-6 max-w-4xl">
    <x-page-header title="Actualités, annonces & événements" />

    <x-section-card title="Nouvel élément" icon="add_circle">
        <form wire:submit="save" class="space-y-3">
            <input type="text" wire:model="title" placeholder="Titre" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]" />
            @error('title') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <select wire:model="kind" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]">
                <option value="actu">Actualité</option>
                <option value="annonce">Annonce</option>
                <option value="evenement">Événement</option>
            </select>
            <textarea wire:model="excerpt" rows="2" placeholder="Résumé" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]"></textarea>
            <textarea wire:model="body" rows="4" placeholder="Contenu" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]"></textarea>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs">Début (bandeau)</label>
                    <input type="datetime-local" wire:model="starts_at" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]" />
                </div>
                <div>
                    <label class="text-xs">Fin</label>
                    <input type="datetime-local" wire:model="ends_at" class="w-full px-3 py-2 border rounded-xl text-sm bg-[#fbf2ed]" />
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl">Créer brouillon</button>
        </form>
    </x-section-card>

    <div class="space-y-3">
        @forelse($items as $item)
            <div class="bg-white border border-[#c1c9b6] rounded-2xl p-4 flex justify-between gap-3">
                <div>
                    <p class="font-semibold">{{ $item->title }}
                        <span class="text-[10px] uppercase ml-2 px-2 py-0.5 rounded-full bg-[#f5ece7]">{{ $item->kindLabel() }}</span>
                    </p>
                    <p class="text-xs text-[#717a69]">{{ $item->status }} @if($item->published_at)· {{ $item->published_at->format('d/m/Y') }}@endif</p>
                </div>
                <div class="flex gap-2">
                    @if($item->status !== 'published')
                        <button wire:click="publish('{{ $item->id }}')" class="px-3 py-1.5 text-xs bg-[#2c6904] text-white rounded-lg">Publier</button>
                    @endif
                    @if($item->status !== 'archived')
                        <button wire:click="archive('{{ $item->id }}')" class="px-3 py-1.5 text-xs border rounded-lg">Archiver</button>
                    @endif
                </div>
            </div>
        @empty
            <x-empty-state icon="newspaper" title="Aucune actualité." />
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="pt-2">{{ $items->links() }}</div>
    @endif
</div>
