<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Bibliothèque</h2>
            <p class="text-[#41493b] mt-1 text-sm">Livres, rapports et documents de référence.</p>
        </div>
        <button wire:click="openModal" type="button"
                class="flex items-center gap-2 bg-[#1e1b18] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-lg">add</span>
            Ajouter un document
        </button>
    </div>

    {{-- Recherche --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="relative max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
            <input wire:model.live.debounce.400ms="search" type="text"
                   placeholder="Rechercher un document..."
                   class="w-full pl-10 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all" />
        </div>
    </div>

    {{-- Liste --}}
    @if($documents->isEmpty())
        <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm p-14 flex flex-col items-center justify-center text-center">
            <span class="material-symbols-outlined text-5xl text-[#c1c9b6] mb-4">menu_book</span>
            <p class="font-sora font-bold text-[#1e1b18] mb-1">Aucun document pour l'instant</p>
            <p class="text-sm text-[#717a69]">Cliquez sur « Ajouter un document » pour commencer.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($documents as $doc)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col gap-3">
                    {{-- Icône + titre --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $doc->type === 'file' ? 'bg-[#f5ece7] text-[#875212]' : 'bg-blue-50 text-blue-600' }}">
                            <span class="material-symbols-outlined text-xl">
                                {{ $doc->type === 'file' ? 'description' : 'link' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-[#1e1b18] leading-snug truncate">{{ $doc->title }}</p>
                            @if($doc->description)
                                <p class="text-xs text-[#717a69] mt-0.5 line-clamp-2">{{ $doc->description }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center gap-2 text-[11px] text-[#717a69]">
                        @if($doc->type === 'file')
                            <span class="px-2 py-0.5 bg-[#f5ece7] rounded-full font-semibold text-[#875212]">
                                {{ strtoupper(pathinfo($doc->original_name, PATHINFO_EXTENSION)) }}
                            </span>
                            @if($doc->file_size)
                                <span>{{ $doc->fileSizeForHumans() }}</span>
                            @endif
                        @else
                            <span class="px-2 py-0.5 bg-blue-50 rounded-full font-semibold text-blue-600">Lien externe</span>
                        @endif
                        <span class="ml-auto">{{ $doc->created_at->format('d/m/Y') }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-1 border-t border-[#c1c9b6]/40">
                        @if($doc->type === 'file')
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                               class="flex-1 flex items-center justify-center gap-1.5 py-1.5 text-xs font-semibold text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">download</span>
                                Télécharger
                            </a>
                        @else
                            <a href="{{ $doc->external_url }}" target="_blank" rel="noopener"
                               class="flex-1 flex items-center justify-center gap-1.5 py-1.5 text-xs font-semibold text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">open_in_new</span>
                                Ouvrir
                            </a>
                        @endif
                        <button wire:click="openEditModal('{{ $doc->id }}')" type="button"
                                class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#615c47] rounded-lg transition-colors"
                                title="Modifier">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button wire:click="confirmDelete('{{ $doc->id }}')" type="button"
                                class="p-1.5 text-[#41493b] hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors"
                                title="Supprimer">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @if($documents->hasPages())
            <div class="flex justify-center">
                {{ $documents->links('livewire.partials.pagination') }}
            </div>
        @endif
    @endif

    {{-- ── Modale : Ajouter un document ── --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#f5ece7] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#875212] text-lg">menu_book</span>
                        </div>
                        <h3 class="font-sora font-bold text-base text-[#1e1b18]">
                            {{ $editingId ? 'Modifier le document' : 'Ajouter un document' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showModal', false)"
                            class="p-1.5 text-[#717a69] hover:text-[#1e1b18] hover:bg-[#f5ece7] rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                {{-- Type --}}
                <div>
                    <p class="text-sm font-semibold text-[#1e1b18] mb-2">Type de document</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 border-2 rounded-xl cursor-pointer transition-all
                            {{ $type === 'file' ? 'border-[#615c47] bg-[#f5ece7]' : 'border-[#c1c9b6] hover:border-[#615c47]/40' }}">
                            <input type="radio" wire:model.live="type" value="file" class="sr-only" />
                            <span class="material-symbols-outlined text-base {{ $type === 'file' ? 'text-[#615c47]' : 'text-[#717a69]' }}">description</span>
                            <span class="text-sm font-semibold {{ $type === 'file' ? 'text-[#615c47]' : 'text-[#41493b]' }}">Fichier</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 border-2 rounded-xl cursor-pointer transition-all
                            {{ $type === 'link' ? 'border-blue-500 bg-blue-50' : 'border-[#c1c9b6] hover:border-blue-300' }}">
                            <input type="radio" wire:model.live="type" value="link" class="sr-only" />
                            <span class="material-symbols-outlined text-base {{ $type === 'link' ? 'text-blue-600' : 'text-[#717a69]' }}">link</span>
                            <span class="text-sm font-semibold {{ $type === 'link' ? 'text-blue-600' : 'text-[#41493b]' }}">Lien externe</span>
                        </label>
                    </div>
                </div>

                {{-- Titre --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Titre *</label>
                    <input wire:model="title" type="text" placeholder="Ex: Rapport annuel 2024"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('title') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18]" />
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Description <span class="text-[#717a69] font-normal">(optionnel)</span></label>
                    <textarea wire:model="description" rows="2" placeholder="Brève description du document..."
                              class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18]"></textarea>
                </div>

                {{-- Fichier ou Lien --}}
                @if($type === 'file')
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[#1e1b18]">
                            Fichier
                            @if(!$editingId) <span class="text-red-500">*</span> @endif
                            <span class="text-[#717a69] font-normal">(max 10 Mo)</span>
                        </label>

                        {{-- Fichier actuel (mode édition seulement) --}}
                        @if($editingId && $currentFileName && !$file)
                            <div class="flex items-center gap-3 px-4 py-3 bg-[#f5ece7] border border-[#c1c9b6] rounded-xl">
                                <span class="material-symbols-outlined text-xl text-[#875212]">description</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#1e1b18] truncate">{{ $currentFileName }}</p>
                                    @if($currentFileSize)
                                        <p class="text-xs text-[#717a69]">{{ $currentFileSize }}</p>
                                    @endif
                                </div>
                                <a href="{{ Storage::url($currentFilePath) }}" target="_blank"
                                   class="flex items-center gap-1 text-xs font-semibold text-[#615c47] hover:underline flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">open_in_new</span>
                                    Voir
                                </a>
                            </div>
                            <p class="text-[11px] text-[#717a69]">
                                <span class="material-symbols-outlined text-[11px] align-middle">info</span>
                                Laisser vide pour conserver ce fichier, ou choisir un nouveau pour le remplacer.
                            </p>
                        @endif

                        {{-- Zone d'upload --}}
                        <label class="relative flex items-center gap-3 px-4 py-4 bg-[#fbf2ed] border-2 border-dashed {{ $errors->has('file') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl cursor-pointer hover:border-[#615c47]/60 transition-colors overflow-hidden">
                            <span class="material-symbols-outlined text-2xl text-[#615c47]">upload_file</span>
                            <div class="flex-1 min-w-0">
                                @if($file)
                                    <p class="text-sm font-semibold text-[#1e1b18] truncate">{{ $file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-[#717a69]">{{ round($file->getSize() / 1024 / 1024, 2) }} Mo</p>
                                @else
                                    <p class="text-sm text-[#41493b]">{{ $editingId ? 'Cliquer pour remplacer le fichier' : 'Cliquer pour choisir un fichier' }}</p>
                                    <p class="text-xs text-[#717a69]">PDF, Word, Excel, images...</p>
                                @endif
                            </div>
                            <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                        </label>
                        @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">URL du document *</label>
                        <input wire:model="external_url" type="url" placeholder="https://exemple.com/rapport.pdf"
                               class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('external_url') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18]" />
                        @error('external_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3 pt-1">
                    <button wire:click="$set('showModal', false)" type="button"
                            class="flex-1 px-4 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                        Annuler
                    </button>
                    <button wire:click="save" type="button"
                            wire:loading.attr="disabled" wire:target="save,file"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1e1b18] text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity disabled:opacity-60">
                        <span wire:loading wire:target="save,file" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                        <span wire:loading.remove wire:target="save,file">{{ $editingId ? 'Enregistrer' : 'Ajouter' }}</span>
                        <span wire:loading wire:target="save,file">{{ $editingId ? 'Mise à jour...' : 'Envoi...' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modale : Confirmer suppression ── --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
             x-data x-init="$el.focus()" @keydown.escape.window="$wire.set('confirmingDelete', false)">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600">warning</span>
                    </div>
                    <h3 class="font-sora font-bold text-base text-[#1e1b18]">Supprimer le document ?</h3>
                </div>
                <p class="text-sm text-[#41493b] mb-6">Cette action est irréversible. Le fichier sera définitivement supprimé.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingDelete', false)"
                            class="px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
                        Annuler
                    </button>
                    <button wire:click="deleteDocument"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-sm">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
