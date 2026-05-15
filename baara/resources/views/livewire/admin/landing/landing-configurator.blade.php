<div class="space-y-6" x-data="{ activeSlideTab: 0 }">

    {{-- Inputs fichier hors @teleport (wire:model fonctionne uniquement dans le DOM Livewire normal) --}}
    <input type="file" id="global-image-upload" wire:model="imageUploadFile" accept="image/*" class="hidden" />
    <input type="file" id="guichet-file-upload" wire:model="guichetImageFile" accept="image/*" class="hidden"
           x-on:change="
               const f = $event.target.files[0];
               if (f) window.dispatchEvent(new CustomEvent('guichet:picked', { detail: URL.createObjectURL(f) }));
           " />

    <div wire:loading wire:target="imageUploadFile" class="fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 bg-[#2c6904] text-white text-sm font-semibold rounded-xl shadow-xl">
        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Téléversement en cours…
    </div>
    <div wire:loading wire:target="guichetImageFile" class="fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 bg-[#2c6904] text-white text-sm font-semibold rounded-xl shadow-xl">
        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Image guichet en cours…
    </div>

    {{-- Flash --}}
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <span class="material-symbols-outlined text-base flex-shrink-0">error</span>{{ session('error') }}
        </div>
    @endif

    {{-- ══ EN-TÊTE PAGE ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#717a69] font-medium mb-1">
                <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                Administration
                <span class="material-symbols-outlined text-sm">chevron_right</span>
                <span class="text-[#2c6904] font-semibold">Landing Page</span>
            </div>
            <h1 class="font-sora text-2xl font-bold text-[#1e1b18]">Éditeur de contenu</h1>
            <p class="text-[#41493b] text-sm mt-0.5">Gérez tout le contenu de la page publique · {{ $sections->count() }} sections</p>
        </div>
        <div class="flex items-center gap-3">
            @if($saved && $editingSlug)
                <span class="flex items-center gap-1.5 text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-full text-xs font-semibold">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">check_circle</span>
                    Enregistré
                </span>
            @endif
            <a href="{{ url('/') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors shadow-sm">
                <span class="material-symbols-outlined text-base">open_in_new</span>
                Voir la page publique
            </a>
        </div>
    </div>

    {{-- ══ GRILLE DES SECTIONS ══════════════════════════════════════════════ --}}
    @php
    $sectionMeta = [
        'hero'                       => ['icon' => 'slideshow',      'color' => 'bg-[#e8f5e9] text-[#2c6904]',   'border' => 'border-[#4caf50]', 'label' => 'Slider principal'],
        'le_projet'                  => ['icon' => 'eco',            'color' => 'bg-[#e0f2f1] text-[#00695c]',   'border' => 'border-[#26a69a]', 'label' => 'Description projet'],
        'audiences'                  => ['icon' => 'group',          'color' => 'bg-[#e3f2fd] text-[#1565c0]',   'border' => 'border-[#42a5f5]', 'label' => '3 cartes audiences'],
        'guichet'                    => ['icon' => 'meeting_room',   'color' => 'bg-[#aef585]/20 text-[#2c6904]', 'border' => 'border-[#2c6904]', 'label' => 'Infos guichet'],
        'ce_que_vous_pouvez_trouver' => ['icon' => 'checklist',      'color' => 'bg-[#f3e5f5] text-[#6a1b9a]',   'border' => 'border-[#ab47bc]', 'label' => 'Listes services'],
        'comment'                    => ['icon' => 'route',          'color' => 'bg-[#fff3e0] text-[#e65100]',   'border' => 'border-[#ffa726]', 'label' => 'Étapes du processus'],
        'autres_services'            => ['icon' => 'widgets',        'color' => 'bg-[#fbe9e7] text-[#bf360c]',   'border' => 'border-[#ff7043]', 'label' => 'Ateliers & événements'],
        'partenaires'                => ['icon' => 'handshake',      'color' => 'bg-[#fff8e1] text-[#f57f17]',   'border' => 'border-[#ffca28]', 'label' => '8 logos partenaires'],
        'temoignages'                => ['icon' => 'format_quote',   'color' => 'bg-[#fce4ec] text-[#880e4f]',   'border' => 'border-[#f48fb1]', 'label' => 'Avis & citations'],
        'mediatheque'                => ['icon' => 'photo_library',  'color' => 'bg-[#e8eaf6] text-[#283593]',   'border' => 'border-[#5c6bc0]', 'label' => 'Photos & médias'],
        'contact'                    => ['icon' => 'mail',           'color' => 'bg-[#e8f5e9] text-[#2c6904]',   'border' => 'border-[#66bb6a]', 'label' => 'Coordonnées'],
    ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @foreach($sections as $index => $section)
            @php $meta = $sectionMeta[$section->slug] ?? ['icon'=>'web','color'=>'bg-gray-100 text-gray-500','border'=>'border-gray-300','label'=>'Section']; @endphp
            <div class="group bg-white rounded-2xl border-2 transition-all duration-200 cursor-pointer overflow-hidden
                {{ $editingSlug === $section->slug
                    ? 'border-[#2c6904] shadow-lg shadow-[#2c6904]/10 ring-2 ring-[#2c6904]/20'
                    : 'border-[#e9e1dc] hover:border-[#c1c9b6] hover:shadow-md' }}"
                 wire:click="editSection('{{ $section->slug }}')">

                {{-- Barre colorée en haut --}}
                <div class="h-1.5 w-full {{ $section->is_active ? $meta['border'] : 'bg-gray-200' }} bg-current opacity-60"></div>

                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        {{-- Icône --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $meta['color'] }}">
                            <span class="material-symbols-outlined text-xl">{{ $meta['icon'] }}</span>
                        </div>

                        {{-- Statut + Ordre --}}
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                {{ $section->is_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-500' }}">
                                {{ $section->is_active ? '● Visible' : '○ Masquée' }}
                            </span>
                            <span class="text-[10px] text-[#717a69] font-medium">#{{ $index + 1 }}</span>
                        </div>
                    </div>

                    <p class="font-sora font-bold text-sm text-[#1e1b18] mb-0.5 truncate">{{ $section->title }}</p>
                    <p class="text-[11px] text-[#717a69]">{{ $meta['label'] }}</p>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#f5ece7]">
                        <div class="flex items-center gap-1">
                            <button type="button" wire:click.stop="moveSectionUp('{{ $section->id }}')"
                                    class="p-1.5 rounded-lg hover:bg-[#f5ece7] transition-colors
                                        {{ $index === 0 ? 'opacity-20 pointer-events-none' : 'text-[#41493b]' }}">
                                <span class="material-symbols-outlined text-sm">keyboard_arrow_up</span>
                            </button>
                            <button type="button" wire:click.stop="moveSectionDown('{{ $section->id }}')"
                                    class="p-1.5 rounded-lg hover:bg-[#f5ece7] transition-colors
                                        {{ $index === $sections->count() - 1 ? 'opacity-20 pointer-events-none' : 'text-[#41493b]' }}">
                                <span class="material-symbols-outlined text-sm">keyboard_arrow_down</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1">
                            @if(!$section->always_visible)
                                <button type="button" wire:click.stop="toggleSection('{{ $section->id }}')"
                                        title="{{ $section->is_active ? 'Masquer' : 'Afficher' }}"
                                        class="p-1.5 rounded-lg hover:bg-[#f5ece7] transition-colors
                                            {{ $section->is_active ? 'text-[#2c6904]' : 'text-gray-400' }}">
                                    <span class="material-symbols-outlined text-base">
                                        {{ $section->is_active ? 'visibility' : 'visibility_off' }}
                                    </span>
                                </button>
                            @else
                                <span class="text-[10px] px-1.5 py-0.5 bg-[#f5ece7] text-[#717a69] rounded font-semibold">Fixe</span>
                            @endif
                            <button type="button" wire:click.stop="editSection('{{ $section->slug }}')"
                                    class="p-1.5 rounded-lg transition-colors
                                        {{ $editingSlug === $section->slug
                                            ? 'bg-[#2c6904] text-white'
                                            : 'hover:bg-[#f5ece7] text-[#41493b]' }}">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Placeholder si vide --}}
        @if($sections->isEmpty())
            <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-[#c1c9b6] p-12 text-center">
                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">web</span>
                <p class="text-sm font-semibold text-[#717a69]">Aucune section trouvée.</p>
                <code class="text-xs text-[#717a69] mt-2 block">php artisan db:seed --class=LandingSectionSeeder</code>
            </div>
        @endif
    </div>

    {{-- ══ MODAL ÉDITEUR ══════════════════════════════════════════════════ --}}
    @if($editingSlug)
        @php
            $editingSection = $sections->firstWhere('slug', $editingSlug);
            $meta = $sectionMeta[$editingSlug] ?? ['icon'=>'web','color'=>'bg-gray-100 text-gray-500','label'=>'Section'];
            $inputCls = 'w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-colors';
            $taCls    = $inputCls . ' resize-none';
            $labelCls = 'block text-xs font-semibold text-[#41493b] mb-1.5';
            $cardCls  = 'bg-[#fbf2ed]/60 border border-[#e9e1dc] rounded-2xl p-5 space-y-4';
            $subLbl   = 'text-[11px] font-bold text-[#717a69] uppercase tracking-wider';
        @endphp

        @teleport('body')
        {{-- Backdrop blur --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             style="background:rgba(30,27,24,0.5);backdrop-filter:blur(6px);"
             wire:click="cancelEdit">

        {{-- Panneau modal --}}
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col"
             style="max-height:90vh;"
             @click.stop>

            {{-- Header éditeur --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#e9e1dc] bg-gradient-to-r from-[#aef585]/20 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $meta['color'] }} flex-shrink-0">
                        <span class="material-symbols-outlined text-lg">{{ $meta['icon'] }}</span>
                    </div>
                    <div>
                        <h2 class="font-sora font-bold text-base text-[#1e1b18]">{{ $editingSection?->title }}</h2>
                        <p class="text-xs text-[#717a69]">{{ $meta['label'] }}</p>
                    </div>
                </div>
                <button type="button" wire:click="cancelEdit"
                        class="p-2 text-[#717a69] hover:bg-[#f5ece7] hover:text-[#1e1b18] rounded-xl transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Corps de l'éditeur --}}
            <div class="p-6 overflow-y-auto flex-1">

                @if($saved)
                    <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium mb-5">
                        <span class="material-symbols-outlined text-base text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span>
                        Section enregistrée avec succès.
                    </div>
                @endif

                {{-- ════════════════════ HERO ════════════════════ --}}
                @if($editingSlug === 'hero')
                    <div class="mb-5 flex items-center justify-between">
                        <p class="text-sm text-[#41493b]">
                            <span class="font-semibold">{{ count($heroSlides) }}</span> slide(s) · maximum 4 recommandé
                        </p>
                        @if(count($heroSlides) < 4)
                        <button type="button" wire:click="addHeroSlide"
                                class="flex items-center gap-1.5 px-3.5 py-2 bg-[#2c6904] text-white text-xs font-bold rounded-xl hover:bg-[#448322] transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span> Ajouter un slide
                        </button>
                        @endif
                    </div>

                    {{-- Tabs slides --}}
                    <div x-data="{ tab: 0 }" class="space-y-4">
                        {{-- Onglets --}}
                        <div class="flex items-center gap-2 border-b border-[#e9e1dc] pb-0">
                            @foreach($heroSlides as $si => $slide)
                                <button type="button" @click="tab = {{ $si }}"
                                        :class="tab === {{ $si }}
                                            ? 'border-b-2 border-[#2c6904] text-[#2c6904] font-bold'
                                            : 'text-[#717a69] hover:text-[#41493b]'"
                                        class="px-4 py-2.5 text-sm transition-colors -mb-px whitespace-nowrap">
                                    Slide {{ $si + 1 }}
                                    @if(!empty($slide['title']))
                                        <span class="hidden sm:inline text-xs opacity-60 ml-1">— {{ Str::limit($slide['title'], 18) }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- Contenu tab --}}
                        @foreach($heroSlides as $si => $slide)
                        <div x-show="tab === {{ $si }}" x-cloak class="space-y-4">
                            {{-- Prévisualisation image --}}
                            @if(!empty($slide['image_url']))
                            <div class="relative w-full h-32 rounded-2xl overflow-hidden bg-gray-100 border border-[#e9e1dc]">
                                <img src="{{ $slide['image_url'] }}" alt="Aperçu slide {{ $si+1 }}"
                                     class="w-full h-full object-cover" onerror="this.parentElement.classList.add('hidden')" />
                                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-end p-3">
                                    <span class="text-white text-xs font-bold">{{ $slide['title'] ?? '—' }}</span>
                                </div>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Titre principal</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.title" class="{{ $inputCls }}" placeholder="Ex: Agro Eco BAARA" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Sous-titre</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.subtitle" class="{{ $inputCls }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Description</label>
                                    <textarea wire:model="heroSlides.{{ $si }}.description" rows="2" class="{{ $taCls }}"></textarea>
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 1 — Texte</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_primary_text" class="{{ $inputCls }}" placeholder="Nous contacter" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 1 — Lien</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_primary_link" class="{{ $inputCls }}" placeholder="#contact" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 2 — Texte <span class="font-normal text-[#717a69]">(optionnel)</span></label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_secondary_text" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 2 — Lien</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_secondary_link" class="{{ $inputCls }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Image de fond <span class="font-normal text-[#717a69]">(chemin /images/medias/... ou URL)</span></label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model.live="heroSlides.{{ $si }}.image_url" class="{{ $inputCls }} flex-1" placeholder="/images/medias/photo.jpg" />
                                        <button type="button"
                                                x-on:click="$wire.set('imageUploadSlot', 'hero.{{ $si }}').then(() => document.getElementById('global-image-upload').click())"
                                                title="Importer depuis l'ordinateur"
                                                class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] text-[#41493b] text-xs font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors">
                                            <span class="material-symbols-outlined text-base">upload_file</span>
                                            <span class="hidden sm:inline">Importer</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if(count($heroSlides) > 1)
                            <div class="pt-2 border-t border-[#e9e1dc]">
                                <button type="button" wire:click="removeHeroSlide({{ $si }})"
                                        class="flex items-center gap-2 text-xs text-red-500 hover:text-red-700 font-semibold">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Supprimer ce slide
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- ════════════════════ LE PROJET ════════════════════ --}}
                @if($editingSlug === 'le_projet')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelCls }}">Badge</label>
                            <input type="text" wire:model="formData.badge" class="{{ $inputCls }}" placeholder="NOTRE MISSION" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Titre de la section</label>
                            <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="{{ $labelCls }}">Paragraphes <span class="font-normal text-[#717a69]">(HTML autorisé : &lt;strong&gt;, &lt;em&gt;)</span></label>
                        <div class="space-y-3">
                            @foreach($formData['paragraphs'] ?? [] as $pi => $para)
                                <div class="flex gap-2">
                                    <textarea wire:model="formData.paragraphs.{{ $pi }}" rows="3"
                                              class="{{ $taCls }} flex-1"></textarea>
                                    <button type="button"
                                            wire:click="$set('formData.paragraphs', array_values(array_filter({{ json_encode($formData['paragraphs'] ?? []) }}, fn($k) => $k !== {{ $pi }}, ARRAY_FILTER_USE_KEY)))"
                                            class="self-start mt-1 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="$push('formData.paragraphs', '')"
                                class="mt-3 flex items-center gap-1.5 text-sm text-[#2c6904] font-semibold hover:underline">
                            <span class="material-symbols-outlined text-base">add_circle</span> Ajouter un paragraphe
                        </button>
                    </div>
                @endif

                {{-- ════════════════════ AUDIENCES ════════════════════ --}}
                @if($editingSlug === 'audiences')
                    <div class="mb-4">
                        <label class="{{ $labelCls }}">Titre de la section</label>
                        <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                    </div>
                    <div class="space-y-4">
                        @foreach($audCards as $ci => $card)
                        <div class="{{ $cardCls }}">
                            <p class="{{ $subLbl }}">Carte {{ $ci + 1 }} — {{ $card['key'] ?? '' }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="{{ $labelCls }}">Titre</label>
                                    <input type="text" wire:model="audCards.{{ $ci }}.title" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Texte du bouton</label>
                                    <input type="text" wire:model="audCards.{{ $ci }}.cta_text" class="{{ $inputCls }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Description</label>
                                    <textarea wire:model="audCards.{{ $ci }}.description" rows="2" class="{{ $taCls }}"></textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- ════════════════════ GUICHET ════════════════════ --}}
                @if($editingSlug === 'guichet')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Titre</label>
                            <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Description</label>
                            <textarea wire:model="formData.description" rows="3" class="{{ $taCls }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Localisation</label>
                            <input type="text" wire:model="formData.localisation" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Horaires d'ouverture</label>
                            <input type="text" wire:model="formData.horaires" class="{{ $inputCls }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Contacts (Tél / Email)</label>
                            <input type="text" wire:model="formData.contacts" class="{{ $inputCls }}" />
                        </div>
                        <div class="md:col-span-2"
                             x-data="{
                                 preview: '{{ addslashes($formData['image_url'] ?? '') }}',
                                 uploading: false,
                                 success: false,
                                 init() {
                                     window.addEventListener('guichet:picked', (e) => {
                                         this.preview = e.detail;
                                         this.uploading = true;
                                         this.success = false;
                                     });
                                     window.addEventListener('guichetUploaded', (e) => {
                                         this.preview = e.detail.url ?? e.detail;
                                         this.uploading = false;
                                         this.success = true;
                                         setTimeout(() => this.success = false, 4000);
                                     });
                                 }
                             }">

                            <label class="{{ $labelCls }}">Image</label>

                            {{-- Aperçu (blob immédiat → URL finale après upload) --}}
                            <div x-show="preview"
                                 class="relative w-full h-44 rounded-2xl overflow-hidden bg-gray-100 border border-[#e9e1dc] mb-3">
                                <img :src="preview" alt="Aperçu guichet" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <div x-show="uploading"
                                     class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-white text-4xl animate-spin">progress_activity</span>
                                    <span class="text-white text-sm font-semibold">Téléversement…</span>
                                </div>
                                <div x-show="!uploading && preview"
                                     class="absolute top-2 right-2 flex items-center gap-1 bg-green-500/90 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    <span class="material-symbols-outlined text-xs">check_circle</span> OK
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <input type="text" wire:model.live="formData.image_url" class="{{ $inputCls }} flex-1"
                                       placeholder="/images/medias/..."
                                       x-on:input="preview = $event.target.value" />
                                <button type="button"
                                        onclick="document.getElementById('guichet-file-upload').click()"
                                        class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2.5 bg-[#2c6904] text-white text-xs font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                                    <span class="material-symbols-outlined text-base">upload_file</span>
                                    Importer
                                </button>
                            </div>

                            {{-- Message de succès --}}
                            <div x-show="success"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="flex items-center gap-2 mt-2 px-3 py-2 bg-green-50 border border-green-200 rounded-xl text-xs text-green-700 font-semibold">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">check_circle</span>
                                Image téléversée avec succès — pensez à enregistrer.
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Légende de l'image</label>
                            <input type="text" wire:model="formData.image_caption" class="{{ $inputCls }}" />
                        </div>
                    </div>
                @endif

                {{-- ════════════════════ CE QUE VOUS POUVEZ TROUVER ════════════════════ --}}
                @if($editingSlug === 'ce_que_vous_pouvez_trouver')
                    <div class="mb-5">
                        <label class="{{ $labelCls }}">Titre de la section</label>
                        <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($ceQueColumns as $ci => $col)
                        <div class="{{ $cardCls }}">
                            <p class="{{ $subLbl }}">Colonne {{ $ci + 1 }}</p>
                            <div>
                                <label class="{{ $labelCls }}">Titre de la colonne</label>
                                <input type="text" wire:model="ceQueColumns.{{ $ci }}.title" class="{{ $inputCls }}" />
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Items</label>
                                <div class="space-y-2">
                                    @foreach($col['items'] ?? [] as $ii => $item)
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="ceQueColumns.{{ $ci }}.items.{{ $ii }}"
                                               class="{{ $inputCls }} flex-1" />
                                        <button type="button" wire:click="removeCeQueItem({{ $ci }}, {{ $ii }})"
                                                class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg flex-shrink-0">
                                            <span class="material-symbols-outlined text-sm">remove_circle</span>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addCeQueItem({{ $ci }})"
                                        class="mt-2 flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline">
                                    <span class="material-symbols-outlined text-sm">add_circle</span> Ajouter un item
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- ════════════════════ COMMENT ════════════════════ --}}
                @if($editingSlug === 'comment')
                    <div class="mb-5">
                        <label class="{{ $labelCls }}">Titre de la section</label>
                        <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                    </div>
                    <div class="space-y-4">
                        @foreach($commentSteps as $si => $step)
                        <div class="{{ $cardCls }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-[#2c6904] text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {{ $step['number'] ?? $si + 1 }}
                                    </div>
                                    <p class="{{ $subLbl }}">Étape {{ $si + 1 }}</p>
                                </div>
                                @if(count($commentSteps) > 1)
                                <button type="button" wire:click="removeStep({{ $si }})"
                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <span class="material-symbols-outlined text-sm">remove_circle</span>
                                </button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $labelCls }}">Titre</label>
                                    <input type="text" wire:model="commentSteps.{{ $si }}.title" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Numéro affiché</label>
                                    <input type="text" wire:model="commentSteps.{{ $si }}.number" class="{{ $inputCls }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Description</label>
                                    <textarea wire:model="commentSteps.{{ $si }}.description" rows="2" class="{{ $taCls }}"></textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <button type="button" wire:click="addStep"
                                class="flex items-center gap-2 text-sm text-[#2c6904] font-semibold hover:underline">
                            <span class="material-symbols-outlined text-base">add_circle</span> Ajouter une étape
                        </button>
                    </div>
                @endif

                {{-- ════════════════════ AUTRES SERVICES ════════════════════ --}}
                @if($editingSlug === 'autres_services')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Titre</label>
                            <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Description</label>
                            <textarea wire:model="formData.description" rows="2" class="{{ $taCls }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Lien Facebook</label>
                            <input type="text" wire:model="formData.facebook_link" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Texte du bouton Facebook</label>
                            <input type="text" wire:model="formData.facebook_text" class="{{ $inputCls }}" />
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="{{ $labelCls }} mb-0">Services affichés</label>
                            <button type="button" wire:click="addService"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-[#2c6904] text-white text-xs font-bold rounded-xl hover:bg-[#448322]">
                                <span class="material-symbols-outlined text-sm">add</span> Ajouter
                            </button>
                        </div>
                        <div class="space-y-2">
                            @foreach($autresServices as $si => $svc)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#41493b] text-lg flex-shrink-0">{{ $svc['icon'] ?? 'star' }}</span>
                                <input type="text" wire:model="autresServices.{{ $si }}.icon"
                                       class="{{ $inputCls }} w-36" placeholder="Icône material" />
                                <input type="text" wire:model="autresServices.{{ $si }}.label"
                                       class="{{ $inputCls }} flex-1" placeholder="Libellé" />
                                <button type="button" wire:click="removeService({{ $si }})"
                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg flex-shrink-0">
                                    <span class="material-symbols-outlined text-sm">remove_circle</span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ════════════════════ PARTENAIRES ════════════════════ --}}
                @if($editingSlug === 'partenaires')
                    <div class="mb-5">
                        <label class="{{ $labelCls }}">Titre de la section</label>
                        <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($partnerItems as $pi => $partner)
                        <div class="{{ $cardCls }}">
                            <div class="flex items-center justify-between">
                                {{-- Prévisualisation logo --}}
                                <div class="flex items-center gap-3">
                                    @if(!empty($partner['logo']))
                                    <div class="w-10 h-10 bg-white border border-[#e9e1dc] rounded-xl flex items-center justify-center overflow-hidden">
                                        <img src="{{ $partner['logo'] }}" alt="" class="max-h-8 max-w-[2.5rem] object-contain" onerror="this.style.display='none'" />
                                    </div>
                                    @endif
                                    <p class="{{ $subLbl }}">{{ $partner['name'] ?: 'Partenaire ' . ($pi+1) }}</p>
                                </div>
                                <button type="button" wire:click="removePartner({{ $pi }})"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="col-span-2">
                                    <label class="{{ $labelCls }}">Nom</label>
                                    <input type="text" wire:model="partnerItems.{{ $pi }}.name" class="{{ $inputCls }}" />
                                </div>
                                <div class="col-span-2">
                                    <label class="{{ $labelCls }}">Logo <span class="font-normal text-[#717a69]">(/images/partners/...)</span></label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="partnerItems.{{ $pi }}.logo" class="{{ $inputCls }} flex-1" />
                                        <button type="button"
                                                x-on:click="$wire.set('imageUploadSlot', 'partner.{{ $pi }}').then(() => document.getElementById('global-image-upload').click())"
                                                title="Importer depuis l'ordinateur"
                                                class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] text-[#41493b] text-xs font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors">
                                            <span class="material-symbols-outlined text-base">upload_file</span>
                                            <span class="hidden sm:inline">Importer</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="{{ $labelCls }}">Description courte</label>
                                    <input type="text" wire:model="partnerItems.{{ $pi }}.description" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Site / Réseaux</label>
                                    <input type="text" wire:model="partnerItems.{{ $pi }}.website" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Libellé du lien</label>
                                    <input type="text" wire:model="partnerItems.{{ $pi }}.social_label" class="{{ $inputCls }}" />
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" wire:click="addPartner"
                            class="mt-4 flex items-center gap-2 text-sm text-[#2c6904] font-semibold hover:underline">
                        <span class="material-symbols-outlined text-base">add_circle</span> Ajouter un partenaire
                    </button>
                @endif

                {{-- ════════════════════ TÉMOIGNAGES ════════════════════ --}}
                @if($editingSlug === 'temoignages')
                    <div class="mb-5">
                        <label class="{{ $labelCls }}">Titre de la section</label>
                        <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                    </div>
                    <div class="space-y-4">
                        @foreach($temoItems as $ti => $temo)
                        <div class="{{ $cardCls }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @php
                                    $avatarBg = ['primary'=>'bg-[#aef585]/30 text-[#2c6904]','secondary'=>'bg-[#ffdcbd]/40 text-[#875212]','tertiary'=>'bg-[#ebe2c8]/40 text-[#615c47]'];
                                    @endphp
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                                        {{ $avatarBg[$temo['avatar_color'] ?? 'primary'] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ strtoupper(substr($temo['name'] ?? 'T', 0, 1)) }}
                                    </div>
                                    <p class="{{ $subLbl }}">{{ $temo['name'] ?: 'Témoignage ' . ($ti+1) }}</p>
                                </div>
                                @if(count($temoItems) > 1)
                                <button type="button" wire:click="removeTemo({{ $ti }})"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $labelCls }}">Nom</label>
                                    <input type="text" wire:model="temoItems.{{ $ti }}.name" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Rôle</label>
                                    <input type="text" wire:model="temoItems.{{ $ti }}.role" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Couleur avatar</label>
                                    <select wire:model="temoItems.{{ $ti }}.avatar_color" class="{{ $inputCls }}">
                                        <option value="primary">● Vert (primary)</option>
                                        <option value="secondary">● Orange (secondary)</option>
                                        <option value="tertiary">● Beige (tertiary)</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="{{ $labelCls }}">Citation</label>
                                    <textarea wire:model="temoItems.{{ $ti }}.text" rows="2" class="{{ $taCls }}"></textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <button type="button" wire:click="addTemo"
                                class="flex items-center gap-2 text-sm text-[#2c6904] font-semibold hover:underline">
                            <span class="material-symbols-outlined text-base">add_circle</span> Ajouter un témoignage
                        </button>
                    </div>
                @endif

                {{-- ════════════════════ MÉDIATHÈQUE ════════════════════ --}}
                @if($editingSlug === 'mediatheque')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="{{ $labelCls }}">Titre</label>
                            <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Description</label>
                            <textarea wire:model="formData.description" rows="1" class="{{ $taCls }}"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-[#41493b]"><span class="font-semibold">{{ count($mediaPhotos) }}</span> photo(s)</p>
                        <button type="button" wire:click="addPhoto"
                                class="flex items-center gap-1.5 px-3.5 py-2 bg-[#2c6904] text-white text-xs font-bold rounded-xl hover:bg-[#448322]">
                            <span class="material-symbols-outlined text-sm">add_photo_alternate</span> Ajouter une photo
                        </button>
                    </div>

                    {{-- Grille photos --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($mediaPhotos as $mi => $photo)
                        <div class="relative group rounded-2xl overflow-hidden border border-[#e9e1dc] bg-[#fbf2ed]">
                            {{-- Aperçu --}}
                            <div class="h-24 bg-gray-100">
                                @if(!empty($photo['src']))
                                <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] ?? '' }}"
                                     class="w-full h-full object-cover" onerror="this.parentElement.style.background='#f5ece7'" />
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#c1c9b6] text-3xl">image</span>
                                </div>
                                @endif
                            </div>
                            {{-- Badge catégorie --}}
                            @php $catC=['terrain'=>'bg-[#2c6904] text-white','formation'=>'bg-[#875212] text-white','evenement'=>'bg-[#615c47] text-white']; @endphp
                            <span class="absolute top-1.5 left-1.5 text-[9px] font-bold px-1.5 py-0.5 rounded-full {{ $catC[$photo['category']??'terrain'] ?? 'bg-gray-500 text-white' }}">
                                {{ ucfirst($photo['category'] ?? 'terrain') }}
                            </span>
                            {{-- Supprimer --}}
                            <button type="button" wire:click="removePhoto({{ $mi }})"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="material-symbols-outlined text-xs">close</span>
                            </button>
                            {{-- Champs --}}
                            <div class="p-2 space-y-1.5">
                                <div class="flex gap-1">
                                    <input type="text" wire:model="mediaPhotos.{{ $mi }}.src"
                                           class="flex-1 w-0 px-2 py-1.5 bg-white border border-[#e9e1dc] rounded-lg text-[10px] focus:outline-none focus:border-[#2c6904]"
                                           placeholder="/images/medias/..." />
                                    <button type="button"
                                            x-on:click="$wire.set('imageUploadSlot', 'media.{{ $mi }}').then(() => document.getElementById('global-image-upload').click())"
                                            title="Importer une image"
                                            class="flex-shrink-0 p-1.5 bg-[#2c6904] text-white rounded-lg hover:bg-[#448322] transition-colors">
                                        <span class="material-symbols-outlined text-xs leading-none" style="font-size:14px">upload_file</span>
                                    </button>
                                </div>
                                <select wire:model="mediaPhotos.{{ $mi }}.category"
                                        class="w-full px-2 py-1.5 bg-white border border-[#e9e1dc] rounded-lg text-[10px] focus:outline-none focus:border-[#2c6904]">
                                    <option value="terrain">Terrain</option>
                                    <option value="formation">Formation</option>
                                    <option value="evenement">Événement</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- ════════════════════ CONTACT ════════════════════ --}}
                @if($editingSlug === 'contact')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelCls }}">Titre</label>
                            <input type="text" wire:model="formData.title" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Sous-titre</label>
                            <textarea wire:model="formData.subtitle" rows="2" class="{{ $taCls }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Adresse</label>
                            <input type="text" wire:model="formData.address" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Téléphone</label>
                            <input type="text" wire:model="formData.phone" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Email public</label>
                            <input type="email" wire:model="formData.email" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Horaires</label>
                            <input type="text" wire:model="formData.hours" class="{{ $inputCls }}" />
                        </div>
                    </div>
                @endif

            </div>{{-- fin corps --}}

            {{-- Footer éditeur --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-[#e9e1dc] bg-[#fff8f5]">
                <p class="text-xs text-[#717a69]">
                    Les modifications sont enregistrées en base de données et reflétées immédiatement sur la page publique.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="cancelEdit"
                            class="px-4 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-white transition-colors text-sm">
                        Fermer
                    </button>
                    <button type="button" wire:click="saveSection"
                            wire:loading.attr="disabled" wire:target="saveSection"
                            class="flex items-center gap-2 px-6 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm shadow-sm shadow-[#2c6904]/30 disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveSection" class="material-symbols-outlined text-base">save</span>
                        <span wire:loading wire:target="saveSection" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="saveSection">Enregistrer</span>
                        <span wire:loading wire:target="saveSection">Enregistrement...</span>
                    </button>
                </div>
            </div>
        </div>{{-- fin panneau modal --}}
        </div>{{-- fin backdrop --}}
        @endteleport
    @endif

    {{-- État vide éditeur --}}
    @if(!$editingSlug)
        <div class="bg-white rounded-2xl border-2 border-dashed border-[#c1c9b6] p-10 text-center">
            <div class="w-16 h-16 bg-[#aef585]/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl text-[#2c6904]">web</span>
            </div>
            <h3 class="font-sora font-bold text-[#1e1b18] text-base mb-2">Sélectionnez une section à modifier</h3>
            <p class="text-sm text-[#717a69]">Cliquez sur l'icône <span class="material-symbols-outlined text-sm align-middle">edit</span> d'une section pour ouvrir son éditeur.</p>
        </div>
    @endif

</div>
