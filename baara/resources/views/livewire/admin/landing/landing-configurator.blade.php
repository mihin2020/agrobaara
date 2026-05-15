<div class="space-y-6">

    {{-- Flash --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">error</span>{{ session('error') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Configurateur Landing Page</h2>
            <p class="text-[#41493b] mt-1 text-sm">Modifiez les textes, les sections et leur ordre d'affichage.</p>
        </div>
        <a href="{{ url('/') }}" target="_blank"
           class="flex items-center gap-2 px-4 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors w-fit">
            <span class="material-symbols-outlined text-base">open_in_new</span>
            Voir la page publique
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ── Liste des sections ── --}}
        <div class="lg:col-span-2 space-y-3">
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Sections ({{ $sections->count() }})</h3>

            @forelse($sections as $index => $section)
                <div class="bg-white rounded-2xl border transition-all overflow-hidden
                    {{ $editingSlug === $section->slug ? 'border-[#2c6904] shadow-md' : 'border-[#c1c9b6]' }}">
                    <div class="flex items-center gap-3 p-3.5">

                        {{-- Ordre --}}
                        <div class="flex flex-col gap-0.5 flex-shrink-0">
                            <button type="button" wire:click="moveSectionUp('{{ $section->id }}')"
                                    class="p-0.5 rounded hover:bg-[#f5ece7] {{ $index === 0 ? 'opacity-20 pointer-events-none' : 'text-[#41493b]' }}">
                                <span class="material-symbols-outlined text-sm">keyboard_arrow_up</span>
                            </button>
                            <button type="button" wire:click="moveSectionDown('{{ $section->id }}')"
                                    class="p-0.5 rounded hover:bg-[#f5ece7] {{ $index === $sections->count() - 1 ? 'opacity-20 pointer-events-none' : 'text-[#41493b]' }}">
                                <span class="material-symbols-outlined text-sm">keyboard_arrow_down</span>
                            </button>
                        </div>

                        {{-- Icône section --}}
                        @php
                            $sectionIcons = [
                                'hero'        => 'rocket_launch',
                                'pour_qui'    => 'group',
                                'services'    => 'widgets',
                                'comment'     => 'route',
                                'partenaires' => 'handshake',
                                'contact'     => 'mail',
                            ];
                        @endphp
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $section->is_active ? 'bg-[#aef585]/20' : 'bg-gray-100' }}">
                            <span class="material-symbols-outlined text-base
                                {{ $section->is_active ? 'text-[#2c6904]' : 'text-gray-400' }}">
                                {{ $sectionIcons[$section->slug] ?? 'web' }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-[#1e1b18] truncate">
                                {{ $section->title }}
                                @if($section->always_visible)
                                    <span class="text-[10px] px-1.5 py-0.5 bg-[#f5ece7] text-[#717a69] rounded font-semibold ml-1">Fixe</span>
                                @endif
                            </p>
                            <p class="text-[11px] font-semibold {{ $section->is_active ? 'text-green-700' : 'text-gray-400' }}">
                                {{ $section->is_active ? '● Visible' : '○ Masquée' }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            @if(!$section->always_visible)
                                <button type="button" wire:click="toggleSection('{{ $section->id }}')"
                                        title="{{ $section->is_active ? 'Masquer' : 'Afficher' }}"
                                        class="p-2 rounded-lg hover:bg-[#f5ece7] transition-colors
                                            {{ $section->is_active ? 'text-[#2c6904]' : 'text-gray-400' }}">
                                    <span class="material-symbols-outlined text-base">{{ $section->is_active ? 'visibility' : 'visibility_off' }}</span>
                                </button>
                            @endif
                            <button type="button" wire:click="editSection('{{ $section->slug }}')"
                                    title="Modifier le contenu"
                                    class="p-2 rounded-lg hover:bg-[#f5ece7] transition-colors text-[#41493b]
                                        {{ $editingSlug === $section->slug ? 'bg-[#aef585]/20 text-[#2c6904]' : '' }}">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-[#c1c9b6] p-8 text-center">
                    <p class="text-sm text-[#717a69]">Aucune section trouvée.</p>
                    <p class="text-xs text-[#717a69] mt-1">Lancez : <code>php artisan db:seed --class=LandingSectionSeeder</code></p>
                </div>
            @endforelse
        </div>

        {{-- ── Éditeur ── --}}
        <div class="lg:col-span-3">
            @if($editingSlug)
                @php $editingSection = $sections->firstWhere('slug', $editingSlug); @endphp
                <div class="bg-white rounded-2xl border border-[#2c6904] shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-[#c1c9b6] bg-[#aef585]/10 flex items-center justify-between">
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18] flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">edit</span>
                            Modifier : {{ $editingSection?->title }}
                        </h3>
                        <button type="button" wire:click="cancelEdit" class="p-1.5 text-[#717a69] hover:bg-[#f5ece7] rounded-lg">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>

                    <div class="p-5 space-y-4 max-h-[65vh] overflow-y-auto">

                        @if($saved)
                            <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium">
                                <span class="material-symbols-outlined text-base text-green-600">check_circle</span>
                                Section enregistrée avec succès.
                            </div>
                        @endif

                        {{-- ────────────────── HERO ────────────────── --}}
                        @if($editingSlug === 'hero')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre principal</label>
                                <textarea wire:model="formData.title" rows="2"
                                          class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Sous-titre / Description</label>
                                <textarea wire:model="formData.subtitle" rows="3"
                                          class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Texte bouton 1</label>
                                    <input type="text" wire:model="formData.cta_primary_text"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Lien bouton 1</label>
                                    <input type="text" wire:model="formData.cta_primary_link"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Texte bouton 2</label>
                                    <input type="text" wire:model="formData.cta_secondary_text"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Lien bouton 2</label>
                                    <input type="text" wire:model="formData.cta_secondary_link"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">URL de l'image de fond</label>
                                <input type="text" wire:model="formData.image_url"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                        @endif

                        {{-- ────────────────── POUR QUI ────────────────── --}}
                        @if($editingSlug === 'pour_qui')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre de section</label>
                                <input type="text" wire:model="formData.title"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            @foreach($pourQuiCards as $ci => $card)
                                <div class="border border-[#c1c9b6] rounded-xl p-4 space-y-3">
                                    <p class="text-xs font-bold text-[#717a69] uppercase tracking-wider">
                                        Carte {{ $ci + 1 }} — {{ $card['key'] ?? '' }}
                                    </p>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Titre</label>
                                        <input type="text" wire:model="pourQuiCards.{{ $ci }}.title"
                                               class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Description</label>
                                        <textarea wire:model="pourQuiCards.{{ $ci }}.description" rows="3"
                                                  class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Texte CTA</label>
                                        <input type="text" wire:model="pourQuiCards.{{ $ci }}.cta_text"
                                               class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- ────────────────── SERVICES ────────────────── --}}
                        @if($editingSlug === 'services')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre de section</label>
                                <input type="text" wire:model="formData.title"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Sous-titre</label>
                                <textarea wire:model="formData.subtitle" rows="2"
                                          class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                            </div>
                            @foreach(($formData['cards'] ?? []) as $ci => $card)
                                <div class="border border-[#c1c9b6] rounded-xl p-4 space-y-3">
                                    <p class="text-xs font-bold text-[#717a69] uppercase tracking-wider">Carte {{ $ci + 1 }}</p>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Titre</label>
                                        <input type="text" wire:model="formData.cards.{{ $ci }}.title"
                                               class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Description</label>
                                        <textarea wire:model="formData.cards.{{ $ci }}.description" rows="2"
                                                  class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                                    </div>
                                    @if(isset($card['stat']))
                                        <div>
                                            <label class="block text-xs font-semibold text-[#41493b] mb-1">Statistique</label>
                                            <input type="text" wire:model="formData.cards.{{ $ci }}.stat"
                                                   class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        {{-- ────────────────── COMMENT ────────────────── --}}
                        @if($editingSlug === 'comment')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre de section</label>
                                <input type="text" wire:model="formData.title"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            @foreach($steps as $si => $step)
                                <div class="border border-[#c1c9b6] rounded-xl p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-[#717a69] uppercase tracking-wider">Étape {{ $si + 1 }}</p>
                                        @if(count($steps) > 1)
                                            <button type="button" wire:click="removeStep({{ $si }})"
                                                    class="p-1 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                                                <span class="material-symbols-outlined text-sm">remove_circle</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Titre</label>
                                        <input type="text" wire:model="steps.{{ $si }}.title"
                                               class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-[#41493b] mb-1">Description</label>
                                        <textarea wire:model="steps.{{ $si }}.description" rows="2"
                                                  class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addStep"
                                    class="flex items-center gap-2 text-sm text-[#2c6904] font-semibold hover:underline">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                Ajouter une étape
                            </button>
                        @endif

                        {{-- ────────────────── PARTENAIRES ────────────────── --}}
                        @if($editingSlug === 'partenaires')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre de section</label>
                                <input type="text" wire:model="formData.title"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-2">Partenaires</label>
                                <div class="space-y-2">
                                    @foreach($partnerItems as $pi => $partner)
                                        <div class="flex items-center gap-2">
                                            <input type="text" wire:model="partnerItems.{{ $pi }}"
                                                   placeholder="Nom du partenaire"
                                                   class="flex-1 px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                            <button type="button" wire:click="removePartner({{ $pi }})"
                                                    class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                                                <span class="material-symbols-outlined text-sm">remove_circle</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addPartner"
                                        class="mt-3 flex items-center gap-2 text-sm text-[#2c6904] font-semibold hover:underline">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    Ajouter un partenaire
                                </button>
                            </div>
                        @endif

                        {{-- ────────────────── CONTACT ────────────────── --}}
                        @if($editingSlug === 'contact')
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Titre</label>
                                <input type="text" wire:model="formData.title"
                                       class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Sous-titre</label>
                                <textarea wire:model="formData.subtitle" rows="2"
                                          class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Adresse</label>
                                    <input type="text" wire:model="formData.address"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Téléphone</label>
                                    <input type="text" wire:model="formData.phone"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Email public</label>
                                    <input type="text" wire:model="formData.email"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Horaires</label>
                                    <input type="text" wire:model="formData.hours"
                                           class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="px-5 py-4 border-t border-[#c1c9b6] bg-[#fbf2ed] flex justify-end gap-3">
                        <button type="button" wire:click="cancelEdit"
                                class="px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-white transition-colors text-sm">
                            Fermer
                        </button>
                        <button type="button" wire:click="saveSection"
                                wire:loading.attr="disabled" wire:target="saveSection"
                                class="flex items-center gap-2 px-5 py-2 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                            <span wire:loading.remove wire:target="saveSection" class="material-symbols-outlined text-base">save</span>
                            <span wire:loading wire:target="saveSection" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                            Enregistrer
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-dashed border-[#c1c9b6] p-12 text-center">
                    <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">web</span>
                    <p class="text-sm font-semibold text-[#717a69]">Cliquez sur <span class="material-symbols-outlined text-sm align-middle">edit</span> en face d'une section pour la modifier.</p>
                </div>
            @endif
        </div>
    </div>
</div>