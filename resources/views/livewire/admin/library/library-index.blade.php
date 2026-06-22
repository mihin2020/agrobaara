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
            <p class="text-[#41493b] mt-1 text-sm">Documents publiés sur la page <a href="{{ route('bibliotheque') }}" target="_blank" class="text-[#875212] font-semibold hover:underline">Bibliothèque publique</a>.</p>
        </div>
        <button wire:click="openModal" type="button"
                class="flex items-center gap-2 bg-[#875212] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:bg-[#6b3f0e] transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            Ajouter un document
        </button>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-[#c1c9b6] px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#f5ece7] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#875212] text-lg">library_books</span>
            </div>
            <div>
                <p class="text-xl font-bold text-[#1e1b18] leading-none">{{ $statsTotal }}</p>
                <p class="text-[10px] font-semibold text-[#717a69] uppercase tracking-wide mt-0.5">Total</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-[#c1c9b6] px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600 text-lg">description</span>
            </div>
            <div>
                <p class="text-xl font-bold text-[#1e1b18] leading-none">{{ $statsFiles }}</p>
                <p class="text-[10px] font-semibold text-[#717a69] uppercase tracking-wide mt-0.5">Fichiers</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-[#c1c9b6] px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-lg">link</span>
            </div>
            <div>
                <p class="text-xl font-bold text-[#1e1b18] leading-none">{{ $statsLinks }}</p>
                <p class="text-[10px] font-semibold text-[#717a69] uppercase tracking-wide mt-0.5">Liens</p>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
                <input wire:model.live.debounce.400ms="search" type="text"
                       placeholder="Rechercher par titre ou description…"
                       class="w-full pl-10 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#875212]/20 focus:border-[#875212] transition-all" />
            </div>
            <div class="flex bg-[#fbf2ed] p-1 rounded-xl gap-0.5 flex-shrink-0">
                <button type="button" wire:click="$set('typeFilter', '')"
                        @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#875212] shadow-sm' => $typeFilter === '', 'text-[#41493b]' => $typeFilter !== ''])>
                    Tous
                </button>
                <button type="button" wire:click="$set('typeFilter', 'file')"
                        @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#875212] shadow-sm' => $typeFilter === 'file', 'text-[#41493b]' => $typeFilter !== 'file'])>
                    Fichiers
                </button>
                <button type="button" wire:click="$set('typeFilter', 'link')"
                        @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-blue-600 shadow-sm' => $typeFilter === 'link', 'text-[#41493b]' => $typeFilter !== 'link'])>
                    Liens
                </button>
            </div>
        </div>
    </div>

    {{-- Liste --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        @if($documents->isEmpty())
            <div class="p-14 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] mb-4">menu_book</span>
                <p class="font-sora font-bold text-[#1e1b18] mb-1">Aucun document trouvé</p>
                <p class="text-sm text-[#717a69] mb-4">
                    @if($search || $typeFilter)
                        Aucun résultat pour ces filtres.
                    @else
                        Commencez par ajouter votre premier document.
                    @endif
                </p>
                @if(!$search && !$typeFilter)
                    <button wire:click="openModal" type="button"
                            class="inline-flex items-center gap-2 bg-[#875212] text-white py-2 px-4 rounded-xl text-sm font-semibold hover:bg-[#6b3f0e]">
                        <span class="material-symbols-outlined text-base">add</span> Ajouter un document
                    </button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider w-16">Couv.</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Document</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Type</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Taille</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden sm:table-cell">Ajouté le</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c1c9b6]/40">
                        @foreach($documents as $doc)
                            @php
                                $ext = $doc->type === 'file' ? strtoupper(pathinfo($doc->original_name ?? '', PATHINFO_EXTENSION)) : 'LIEN';
                                $iconColor = match(true) {
                                    $doc->isPdf() => 'bg-red-50 text-red-600',
                                    $doc->type === 'link' => 'bg-blue-50 text-blue-600',
                                    default => 'bg-[#f5ece7] text-[#875212]',
                                };
                                $icon = match(true) {
                                    $doc->isPdf() => 'picture_as_pdf',
                                    $doc->type === 'link' => 'link',
                                    default => 'description',
                                };
                            @endphp
                            <tr class="hover:bg-[#fbf2ed]/50 transition-colors" wire:key="doc-{{ $doc->id }}">
                                <td class="px-4 py-3">
                                    <div class="w-11 h-14 rounded-md overflow-hidden border border-[#c1c9b6] bg-[#f5ece7] flex-shrink-0">
                                        @if($doc->coverUrl())
                                            <img src="{{ $doc->coverUrl() }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center {{ $iconColor }}">
                                                <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 min-w-[200px]">
                                    <p class="font-semibold text-sm text-[#1e1b18] leading-snug">{{ $doc->title }}</p>
                                    @if($doc->description)
                                        <p class="text-xs text-[#717a69] mt-0.5 line-clamp-1">{{ $doc->description }}</p>
                                    @endif
                                    @if($doc->type === 'file' && $doc->original_name)
                                        <p class="text-[10px] text-[#717a69] mt-1 truncate max-w-xs">{{ $doc->original_name }}</p>
                                    @elseif($doc->type === 'link')
                                        <p class="text-[10px] text-blue-600 mt-1 truncate max-w-xs">{{ $doc->external_url }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $iconColor }}">
                                        {{ $ext }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <span class="text-xs text-[#717a69]">
                                        {{ $doc->type === 'file' && $doc->file_size ? $doc->fileSizeForHumans() : '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="text-xs text-[#717a69]">{{ $doc->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($doc->type === 'file')
                                            @if($doc->isPdf())
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                               class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors" title="Lire">
                                                <span class="material-symbols-outlined text-base">menu_book</span>
                                            </a>
                                            @endif
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                               class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors" title="Télécharger">
                                                <span class="material-symbols-outlined text-base">download</span>
                                            </a>
                                        @else
                                            <a href="{{ $doc->external_url }}" target="_blank" rel="noopener"
                                               class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors" title="Ouvrir">
                                                <span class="material-symbols-outlined text-base">open_in_new</span>
                                            </a>
                                        @endif
                                        <button wire:click="openEditModal('{{ $doc->id }}')" type="button"
                                                class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors" title="Modifier">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                        </button>
                                        <button wire:click="confirmDelete('{{ $doc->id }}')" type="button"
                                                class="p-1.5 text-[#41493b] hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Supprimer">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="px-4 py-3 border-t border-[#c1c9b6] bg-[#fbf2ed]/30">
                    {{ $documents->links('livewire.partials.pagination') }}
                </div>
            @endif
        @endif
    </div>

    {{-- ── Modale : Ajouter un document ── --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
             x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col max-h-[min(560px,90vh)]"
                 @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#e9e1dc] flex-shrink-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-[#f5ece7] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[#875212] text-base">menu_book</span>
                        </div>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18] truncate">
                            {{ $editingId ? 'Modifier le document' : 'Nouveau document' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showModal', false)" type="button"
                            class="p-1.5 text-[#717a69] hover:text-[#1e1b18] hover:bg-[#f5ece7] rounded-lg transition-colors flex-shrink-0">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                {{-- Corps scrollable --}}
                <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3.5">

                    {{-- Type --}}
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 py-2 px-2 border rounded-lg cursor-pointer text-xs font-semibold transition-all
                            {{ $type === 'file' ? 'border-[#615c47] bg-[#f5ece7] text-[#615c47]' : 'border-[#c1c9b6] text-[#41493b] hover:border-[#615c47]/40' }}">
                            <input type="radio" wire:model.live="type" value="file" class="sr-only" />
                            <span class="material-symbols-outlined text-sm">description</span> Fichier
                        </label>
                        <label class="flex items-center justify-center gap-1.5 py-2 px-2 border rounded-lg cursor-pointer text-xs font-semibold transition-all
                            {{ $type === 'link' ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-[#c1c9b6] text-[#41493b] hover:border-blue-300' }}">
                            <input type="radio" wire:model.live="type" value="link" class="sr-only" />
                            <span class="material-symbols-outlined text-sm">link</span> Lien
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Titre *</label>
                        <input wire:model="title" type="text" placeholder="Ex: Rapport annuel 2024"
                               class="w-full px-3 py-2 bg-[#fbf2ed] border {{ $errors->has('title') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20" />
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Description <span class="text-[#717a69] font-normal">(opt.)</span></label>
                        <textarea wire:model="description" rows="2" placeholder="Brève description..."
                                  class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20"></textarea>
                    </div>

                    @if($type === 'file')
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1">
                                Fichier @if(!$editingId)<span class="text-red-500">*</span>@endif
                                <span class="text-[#717a69] font-normal">· max 10 Mo</span>
                            </label>
                            @if($editingId && $currentFileName && !$file)
                                <p class="text-[10px] text-[#717a69] mb-1.5 truncate">Actuel : {{ $currentFileName }}</p>
                            @endif
                            <label class="relative flex items-center gap-2.5 px-3 py-2.5 bg-[#fbf2ed] border border-dashed {{ $errors->has('file') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-lg cursor-pointer hover:border-[#615c47]/60 text-sm">
                                <span class="material-symbols-outlined text-lg text-[#615c47] flex-shrink-0">upload_file</span>
                                <span class="flex-1 min-w-0 truncate text-[#41493b] text-xs">
                                    @if($file)
                                        {{ $file->getClientOriginalName() }}
                                    @else
                                        Choisir un fichier (PDF, Word…)
                                    @endif
                                </span>
                                <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                            </label>
                            @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1">URL *</label>
                            <input wire:model="external_url" type="url" placeholder="https://…"
                                   class="w-full px-3 py-2 bg-[#fbf2ed] border {{ $errors->has('external_url') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20" />
                            @error('external_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Couverture repliable --}}
                    <details class="group rounded-lg border border-[#e9e1dc] bg-[#fbf2ed]/50">
                        <summary class="flex items-center justify-between px-3 py-2 cursor-pointer list-none text-xs font-semibold text-[#41493b]">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-[#875212]">image</span>
                                Couverture (optionnelle)
                            </span>
                            <span class="material-symbols-outlined text-sm text-[#717a69] group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-3 pb-3 pt-1 space-y-2 border-t border-[#e9e1dc]">
                            <p class="text-[10px] text-[#717a69]">Sans image, la 1<sup>re</sup> page du PDF est utilisée.</p>
                            @if($cover)
                                <img src="{{ $cover->temporaryUrl() }}" alt="" class="w-16 h-20 object-cover rounded border border-[#c1c9b6]" />
                            @elseif($editingId && $currentCoverPath && !$removeCover)
                                <img src="{{ Storage::url($currentCoverPath) }}" alt="" class="w-16 h-20 object-cover rounded border border-[#c1c9b6]" />
                            @endif
                            <label class="relative flex items-center gap-2 px-3 py-2 bg-white border border-dashed border-[#c1c9b6] rounded-lg cursor-pointer text-xs text-[#41493b]">
                                <span class="material-symbols-outlined text-base text-[#875212]">add_photo_alternate</span>
                                <span>{{ $cover ? 'Changer la couverture' : 'Ajouter une couverture' }}</span>
                                <input type="file" wire:model="cover" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" />
                            </label>
                            @error('cover') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            @if(($editingId && $currentCoverPath) || $cover)
                                <label class="flex items-center gap-1.5 text-[10px] text-red-600 cursor-pointer">
                                    <input type="checkbox" wire:model.live="removeCover" class="rounded border-[#c1c9b6]" />
                                    Supprimer la couverture
                                </label>
                            @endif
                        </div>
                    </details>
                </div>

                {{-- Footer --}}
                <div class="flex gap-2 px-4 py-3 border-t border-[#e9e1dc] bg-[#fff8f5] flex-shrink-0 rounded-b-2xl">
                    <button wire:click="$set('showModal', false)" type="button"
                            class="flex-1 px-3 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-lg hover:bg-[#f5ece7] transition-colors">
                        Annuler
                    </button>
                    <button wire:click="save" type="button"
                            wire:loading.attr="disabled" wire:target="save,file,cover"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-[#1e1b18] text-white font-semibold text-sm rounded-lg hover:opacity-90 disabled:opacity-60">
                        <span wire:loading wire:target="save,file,cover" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                        <span wire:loading.remove wire:target="save,file,cover">{{ $editingId ? 'Enregistrer' : 'Ajouter' }}</span>
                        <span wire:loading wire:target="save,file,cover">…</span>
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
