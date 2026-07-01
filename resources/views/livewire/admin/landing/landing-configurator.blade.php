<div class="space-y-6" x-data="{ activeSlideTab: 0 }">

    {{-- Inputs fichier hors @teleport (wire:model fonctionne uniquement dans le DOM Livewire normal) --}}
    <input type="file" id="global-image-upload" wire:model="imageUploadFile" accept="image/*" class="hidden" />
    <input type="file" id="global-photo-upload" wire:model="photoUploadFile" accept="image/*" class="hidden" />
    <input type="file" id="global-video-upload" wire:model="videoUploadFile" accept="video/*,.mp4,.mov,.webm,.m4v" class="hidden" />
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

    <div wire:loading wire:target="photoUploadFile" class="fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 bg-[#2c6904] text-white text-sm font-semibold rounded-xl shadow-xl">
        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Import photo…
    </div>
    <div wire:loading wire:target="videoUploadFile" class="fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 bg-[#283593] text-white text-sm font-semibold rounded-xl shadow-xl">
        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Import vidéo…
    </div>

    {{-- Flash --}}
    @if(session('upload_error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <span class="material-symbols-outlined text-base flex-shrink-0">error</span>{{ session('upload_error') }}
        </div>
    @endif
    @if(session('upload_success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <span class="material-symbols-outlined text-base flex-shrink-0">check_circle</span>{{ session('upload_success') }}
        </div>
    @endif

    {{-- Toast enregistrement (fixe, visible même dans le modal) --}}
    @if($saveNotice)
    <div class="fixed bottom-4 right-4 z-[10050] flex items-center gap-2.5 px-4 py-3 bg-[#2c6904] text-white text-sm font-semibold rounded-xl shadow-2xl shadow-[#2c6904]/30"
         x-data="{ show: true }"
         x-init="setTimeout(() => { show = false; $wire.set('saveNotice', ''); }, 4000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">check_circle</span>
        {{ $saveNotice }}
    </div>
    @endif
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
        'hero'                       => ['icon' => 'slideshow',      'color' => 'bg-[#e8f5e9] text-[#2c6904]',   'border' => 'border-[#4caf50]', 'label' => 'Slider + logo en-tête'],
        'le_projet'                  => ['icon' => 'eco',            'color' => 'bg-[#e0f2f1] text-[#00695c]',   'border' => 'border-[#26a69a]', 'label' => 'Description projet + logo'],
        'audiences'                  => ['icon' => 'group',          'color' => 'bg-[#e3f2fd] text-[#1565c0]',   'border' => 'border-[#42a5f5]', 'label' => '3 cartes audiences'],
        'guichet'                    => ['icon' => 'meeting_room',   'color' => 'bg-[#aef585]/20 text-[#2c6904]', 'border' => 'border-[#2c6904]', 'label' => 'Infos guichet'],
        'ce_que_vous_pouvez_trouver' => ['icon' => 'checklist',      'color' => 'bg-[#f3e5f5] text-[#6a1b9a]',   'border' => 'border-[#ab47bc]', 'label' => 'Listes services'],
        'comment'                    => ['icon' => 'route',          'color' => 'bg-[#fff3e0] text-[#e65100]',   'border' => 'border-[#ffa726]', 'label' => 'Étapes du processus'],
        'autres_services'            => ['icon' => 'widgets',        'color' => 'bg-[#fbe9e7] text-[#bf360c]',   'border' => 'border-[#ff7043]', 'label' => 'Ateliers & événements'],
        'partenaires'                => ['icon' => 'handshake',      'color' => 'bg-[#fff8e1] text-[#f57f17]',   'border' => 'border-[#ffca28]', 'label' => '8 logos partenaires'],
        'temoignages'                => ['icon' => 'format_quote',   'color' => 'bg-[#fce4ec] text-[#880e4f]',   'border' => 'border-[#f48fb1]', 'label' => 'Avis & citations'],
        'mediatheque'                => ['icon' => 'photo_library',  'color' => 'bg-[#e8eaf6] text-[#283593]',   'border' => 'border-[#5c6bc0]', 'label' => 'Photos, vidéos & médias'],
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
                @if(session('upload_success'))
                    <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium mb-5">
                        <span class="material-symbols-outlined text-base text-green-600">check_circle</span>
                        {{ session('upload_success') }}
                    </div>
                @endif
                @if(session('upload_error'))
                    <div class="flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 font-medium mb-5">
                        <span class="material-symbols-outlined text-base text-red-600">error</span>
                        {{ session('upload_error') }}
                    </div>
                @endif

                {{-- ════════════════════ HERO ════════════════════ --}}
                @if($editingSlug === 'hero')
                    {{-- Logo en-tête --}}
                    <div class="mb-6 p-4 bg-[#fbf2ed]/60 border border-[#e9e1dc] rounded-2xl space-y-3">
                        <p class="{{ $subLbl }}">Logo de l'en-tête (navigation)</p>
                        @if(!empty($formData['logo_url']))
                        <div class="flex items-center justify-center h-24 rounded-xl bg-white border border-[#e9e1dc] p-3">
                            <img src="{{ $formData['logo_url'] }}" alt="Logo en-tête" class="max-h-full max-w-full object-contain"
                                 onerror="this.parentElement.classList.add('hidden')" />
                        </div>
                        @endif
                        <div class="flex gap-2">
                            <input type="text" wire:model.blur="formData.logo_url" class="{{ $inputCls }} flex-1"
                                   placeholder="/images/logo.jpeg" />
                            <button type="button"
                                    x-on:click="$wire.set('imageUploadSlot', 'header_logo.0').then(() => document.getElementById('global-image-upload').click())"
                                    class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2.5 bg-[#2c6904] text-white text-xs font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                                <span class="material-symbols-outlined text-base">upload_file</span>
                                Importer
                            </button>
                        </div>
                    </div>

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
                                        <span class="hidden sm:inline text-xs opacity-60 ml-1">- {{ Str::limit($slide['title'], 18) }}</span>
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
                                    <span class="text-white text-xs font-bold">{{ $slide['title'] ?? '-' }}</span>
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
                                    <label class="{{ $labelCls }}">Bouton 1 - Texte</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_primary_text" class="{{ $inputCls }}" placeholder="Nous contacter" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 1 - Lien</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_primary_link" class="{{ $inputCls }}" placeholder="#contact" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 2 - Texte <span class="font-normal text-[#717a69]">(optionnel)</span></label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_secondary_text" class="{{ $inputCls }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">Bouton 2 - Lien</label>
                                    <input type="text" wire:model="heroSlides.{{ $si }}.cta_secondary_link" class="{{ $inputCls }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="{{ $labelCls }}">Image de fond <span class="font-normal text-[#717a69]">(chemin /images/medias/... ou URL)</span></label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model.blur="heroSlides.{{ $si }}.image_url" class="{{ $inputCls }} flex-1" placeholder="/images/medias/photo.jpg" />
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

                    <div class="mt-6 pt-6 border-t border-[#e9e1dc] space-y-3">
                        <p class="{{ $subLbl }}">Logo / image à droite</p>
                        @if(!empty($formData['image_url']))
                        <div class="flex items-center justify-center h-40 rounded-xl bg-white border border-[#e9e1dc] p-4">
                            <img src="{{ $formData['image_url'] }}" alt="Aperçu" class="max-h-full max-w-full object-contain"
                                 onerror="this.parentElement.classList.add('hidden')" />
                        </div>
                        @endif
                        <div class="flex gap-2">
                            <input type="text" wire:model.blur="formData.image_url" class="{{ $inputCls }} flex-1"
                                   placeholder="/images/logo.jpeg ou /images/uploads/..." />
                            <button type="button"
                                    x-on:click="$wire.set('imageUploadSlot', 'projet.0').then(() => document.getElementById('global-image-upload').click())"
                                    class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2.5 bg-[#2c6904] text-white text-xs font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                                <span class="material-symbols-outlined text-base">upload_file</span>
                                Importer
                            </button>
                        </div>
                        <p class="text-[11px] text-[#717a69]">Si aucune image n'est définie, l'icône « eco » s'affiche par défaut.</p>
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
                            <p class="{{ $subLbl }}">Carte {{ $ci + 1 }} - {{ $card['key'] ?? '' }}</p>
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
                                <input type="text" wire:model.blur="formData.image_url" class="{{ $inputCls }} flex-1"
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
                                Image téléversée avec succès - pensez à enregistrer.
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
                            <input type="text" wire:model.blur="formData.title" class="{{ $inputCls }}" />
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Description</label>
                            <textarea wire:model.blur="formData.description" rows="1" class="{{ $taCls }}"></textarea>
                        </div>
                    </div>

                    {{-- Actions rapides (masquées si liste vide - voir état vide ci-dessous) --}}
                    @php
                        $filteredMediaPreview = collect($mediaPhotos)->filter(fn($p) => $mediaTab === 'video'
                            ? ($p['type'] ?? 'image') === 'video'
                            : ($p['type'] ?? 'image') !== 'video');
                    @endphp
                    @if($filteredMediaPreview->isNotEmpty() && $mediaTab === 'video')
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" onclick="pickMediaUpload('video')"
                                class="inline-flex items-center gap-1.5 py-2 px-3 rounded-lg border border-[#283593]/30 bg-[#e8eaf6]/60 hover:bg-[#e8eaf6] text-xs font-semibold text-[#283593] transition-colors">
                            <span class="material-symbols-outlined text-base">videocam</span>
                            Importer une vidéo
                        </button>
                        <button type="button" wire:click="openVideoLinkForm"
                                class="inline-flex items-center gap-1.5 py-2 px-3 rounded-lg border border-[#283593]/20 bg-white hover:bg-[#e8eaf6]/30 text-xs font-semibold text-[#283593] transition-colors">
                            <span class="material-symbols-outlined text-base">link</span>
                            Lien vidéo
                        </button>
                    </div>
                    @endif

                    @if($videoLinkFormOpen && $filteredMediaPreview->isNotEmpty())
                    <div class="mb-4 p-3 rounded-xl border border-[#283593]/25 bg-gradient-to-br from-[#e8eaf6]/80 to-white space-y-2">
                        <p class="text-xs font-semibold text-[#283593]">Coller un lien YouTube, Vimeo ou MP4</p>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="url" wire:model="videoLinkInput"
                                   wire:keydown.enter="addVideoFromLink"
                                   class="{{ $inputCls }} flex-1 text-sm"
                                   placeholder="https://www.youtube.com/watch?v=…" />
                            <button type="button" wire:click="addVideoFromLink"
                                    class="flex-shrink-0 px-4 py-2 bg-[#283593] text-white text-sm font-semibold rounded-xl hover:bg-[#3949ab]">
                                Ajouter
                            </button>
                            <button type="button" wire:click="cancelVideoLinkForm"
                                    class="flex-shrink-0 px-3 py-2 text-sm text-[#717a69] hover:bg-white rounded-xl">
                                Annuler
                            </button>
                        </div>
                        @error('videoLinkInput')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    @error('videoUploadFile')
                        <p class="text-xs text-red-600 mb-3">{{ $message }}</p>
                    @enderror

                    {{-- Catégories (état conservé après Livewire) --}}
                    <div class="mb-4 rounded-xl border border-[#e9e1dc] bg-[#fbf2ed]/60 overflow-hidden" wire:key="media-categories-panel">
                        <button type="button" wire:click="toggleCategoriesPanel"
                                class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-left hover:bg-[#f5ece7]/50 transition-colors">
                            <div>
                                <p class="{{ $subLbl }}">Catégories de filtre</p>
                                <p class="text-[10px] text-[#717a69] mt-0.5">{{ count($mediaCategories) }} catégorie(s)</p>
                            </div>
                            <span class="material-symbols-outlined text-[#717a69] text-lg transition-transform {{ $categoriesPanelOpen ? 'rotate-180' : '' }}">expand_more</span>
                        </button>
                        @if($categoriesPanelOpen)
                        <div class="px-3 pb-3 pt-1 space-y-2 border-t border-[#e9e1dc]">
                            @foreach($mediaCategories as $ci => $cat)
                            <div class="flex items-center gap-2" wire:key="media-cat-{{ $ci }}">
                                <input type="text" wire:model.blur="mediaCategories.{{ $ci }}.label"
                                       class="flex-1 px-2.5 py-1.5 bg-white border border-[#e9e1dc] rounded-lg text-xs focus:outline-none focus:border-[#2c6904]"
                                       placeholder="Ex: Ateliers" />
                                <button type="button" wire:click="removeMediaCategory({{ $ci }})"
                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg {{ count($mediaCategories) <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                            @endforeach
                            <button type="button" wire:click="addMediaCategory"
                                    class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline pt-1">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Ajouter une catégorie
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Onglets Photos / Vidéos --}}
                    <div class="flex items-center gap-1 mb-3 border-b border-[#e9e1dc]">
                        <button type="button" wire:click="$set('mediaTab', 'photo')"
                                class="px-3 py-1.5 text-xs font-semibold border-b-2 -mb-px transition-colors {{ $mediaTab === 'photo' ? 'border-[#2c6904] text-[#2c6904]' : 'border-transparent text-[#717a69] hover:text-[#41493b]' }}">
                            <span class="material-symbols-outlined text-sm align-middle mr-0.5">photo_library</span>
                            Photos ({{ $mediaPhotoCount }})
                        </button>
                        <button type="button" wire:click="$set('mediaTab', 'video')"
                                class="px-3 py-1.5 text-xs font-semibold border-b-2 -mb-px transition-colors {{ $mediaTab === 'video' ? 'border-[#283593] text-[#283593]' : 'border-transparent text-[#717a69] hover:text-[#41493b]' }}">
                            <span class="material-symbols-outlined text-sm align-middle mr-0.5">smart_display</span>
                            Vidéos ({{ $mediaVideoCount }})
                        </button>
                    </div>

                    @php
                        $filteredMedia = collect($mediaPhotos)->map(fn($p, $i) => array_merge($p, ['_idx' => $i]))
                            ->filter(fn($p) => $mediaTab === 'video'
                                ? ($p['type'] ?? 'image') === 'video'
                                : ($p['type'] ?? 'image') !== 'video');
                    @endphp

                    @if($filteredMedia->isEmpty())
                        @if($mediaTab === 'video')
                        {{-- État vide vidéos : deux cartes d'action --}}
                        <div class="rounded-2xl border border-[#e9e1dc] bg-gradient-to-b from-[#faf8f6] to-white overflow-hidden">
                            <div class="px-5 pt-6 pb-2 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#283593]/10 text-[#283593] mb-3">
                                    <span class="material-symbols-outlined text-2xl">smart_display</span>
                                </div>
                                <h3 class="font-sora font-bold text-[#1e1b18] text-base">Ajoutez votre première vidéo</h3>
                                <p class="text-xs text-[#717a69] mt-1 max-w-sm mx-auto">Choisissez comment alimenter la médiathèque : fichier local ou lien en ligne.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 pt-2">
                                {{-- Carte import fichier --}}
                                <button type="button" onclick="pickMediaUpload('video')"
                                        class="group text-left p-4 rounded-xl border-2 border-dashed border-[#283593]/25 bg-white hover:border-[#283593]/50 hover:bg-[#e8eaf6]/20 transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-[#283593] text-white flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                            <span class="material-symbols-outlined text-xl">upload_file</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-sm text-[#1e1b18]">Importer un fichier</p>
                                            <p class="text-[11px] text-[#717a69] mt-0.5 leading-relaxed">MP4, WebM ou MOV depuis votre ordinateur</p>
                                            <span class="inline-flex items-center gap-1 mt-2.5 text-[11px] font-bold text-[#283593] group-hover:underline">
                                                Parcourir
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </span>
                                        </div>
                                    </div>
                                </button>

                                {{-- Carte lien externe --}}
                                <div class="p-4 rounded-xl border-2 {{ $videoLinkFormOpen ? 'border-[#283593]/50 bg-[#e8eaf6]/15' : 'border-dashed border-[#283593]/25 bg-white' }} transition-all">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-[#e8eaf6] text-[#283593] flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-xl">link</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-sm text-[#1e1b18]">Lien en ligne</p>
                                            <p class="text-[11px] text-[#717a69] mt-0.5 leading-relaxed">YouTube, Vimeo ou URL directe MP4</p>
                                        </div>
                                    </div>

                                    @if($videoLinkFormOpen)
                                    <div class="space-y-2">
                                        <input type="url" wire:model="videoLinkInput"
                                               wire:keydown.enter="addVideoFromLink"
                                               class="{{ $inputCls }} text-sm py-2"
                                               placeholder="https://youtu.be/…" autofocus />
                                        @error('videoLinkInput')
                                            <p class="text-[11px] text-red-600">{{ $message }}</p>
                                        @enderror
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="addVideoFromLink"
                                                    class="flex-1 py-2 bg-[#283593] text-white text-xs font-bold rounded-lg hover:bg-[#3949ab]">
                                                Valider le lien
                                            </button>
                                            <button type="button" wire:click="cancelVideoLinkForm"
                                                    class="px-3 py-2 text-xs text-[#717a69] hover:bg-white rounded-lg">
                                                Annuler
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    <button type="button" wire:click="openVideoLinkForm"
                                            class="w-full py-2 px-3 rounded-lg border border-[#283593]/30 text-xs font-bold text-[#283593] hover:bg-[#e8eaf6]/40 transition-colors">
                                        Coller un lien
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="px-4 pb-4 flex flex-wrap items-center justify-center gap-3 text-[10px] text-[#717a69]">
                                <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> YouTube</span>
                                <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#1ab7ea]"></span> Vimeo</span>
                                <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#283593]"></span> MP4 direct</span>
                            </div>
                        </div>
                        @else
                        {{-- État vide photos --}}
                        <button type="button" onclick="pickMediaUpload('photo')"
                                class="group w-full text-left rounded-2xl border-2 border-dashed border-[#2c6904]/25 bg-gradient-to-b from-[#aef585]/10 to-white hover:border-[#2c6904]/45 hover:bg-[#aef585]/15 transition-all p-8">
                            <div class="flex flex-col items-center text-center max-w-xs mx-auto">
                                <div class="w-14 h-14 rounded-2xl bg-[#2c6904]/10 text-[#2c6904] flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                </div>
                                <p class="font-sora font-bold text-[#1e1b18] text-base">Aucune photo pour l'instant</p>
                                <p class="text-xs text-[#717a69] mt-1.5">JPG, PNG ou WebP - cliquez pour importer depuis votre ordinateur.</p>
                                <span class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-[#2c6904] text-white text-xs font-bold group-hover:bg-[#448322] transition-colors">
                                    <span class="material-symbols-outlined text-base">upload_file</span>
                                    Choisir une photo
                                </span>
                            </div>
                        </button>
                        @endif
                    @else
                        <div class="flex flex-wrap gap-1.5 mb-3 max-h-48 overflow-y-auto p-1">
                            @foreach($filteredMedia as $photo)
                            @php $mi = $photo['_idx']; $isVideo = ($photo['type'] ?? 'image') === 'video'; @endphp
                            <div wire:key="media-thumb-{{ $mi }}"
                                 class="relative group w-14 h-14 flex-shrink-0 rounded-lg overflow-hidden border transition-all cursor-pointer bg-[#f5ece7]
                                    {{ $editingMediaIndex === $mi ? 'border-[#2c6904] ring-2 ring-[#2c6904]/20' : 'border-[#e9e1dc] hover:border-[#2c6904]/50' }}"
                                 wire:click="editMediaItem({{ $mi }})"
                                 title="{{ $photo['alt'] ?? ($isVideo ? 'Vidéo' : 'Photo') }}">
                                @if(!empty($photo['src']))
                                    @if($isVideo)
                                    @php
                                        $isExtVideo = \App\Support\MediaVideoUrl::isExternal($photo['src']);
                                        $videoLabel = $isExtVideo ? \App\Support\MediaVideoUrl::previewLabel($photo['src']) : 'Fichier';
                                    @endphp
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-[#283593]/10 text-[#283593]">
                                        <span class="material-symbols-outlined text-lg leading-none">{{ $isExtVideo ? 'link' : 'play_circle' }}</span>
                                        <span class="text-[8px] font-bold mt-0.5 leading-none">{{ $videoLabel }}</span>
                                    </div>
                                    @else
                                    <img src="{{ $photo['src'] }}" alt="" loading="lazy" decoding="async"
                                         class="w-full h-full object-cover" />
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg text-[#c1c9b6]">{{ $isVideo ? 'videocam' : 'image' }}</span>
                                    </div>
                                @endif
                                <button type="button" wire:click.stop="removePhoto({{ $mi }})"
                                        class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                    <span class="material-symbols-outlined text-[10px] leading-none">close</span>
                                </button>
                            </div>
                            @endforeach
                            {{-- Bouton + pour ajouter photo / vidéo --}}
                            <button type="button"
                                    onclick="pickMediaUpload('{{ $mediaTab === 'video' ? 'video' : 'photo' }}')"
                                    title="{{ $mediaTab === 'video' ? 'Importer une vidéo' : 'Ajouter une photo' }}"
                                    class="w-14 h-14 flex-shrink-0 rounded-lg border-2 border-dashed flex items-center justify-center transition-all
                                        {{ $mediaTab === 'video'
                                            ? 'border-[#283593]/35 bg-[#e8eaf6]/30 hover:border-[#283593]/60 hover:bg-[#e8eaf6]/60 text-[#283593]'
                                            : 'border-[#2c6904]/35 bg-[#aef585]/10 hover:border-[#2c6904]/60 hover:bg-[#aef585]/25 text-[#2c6904]' }}">
                                <span class="material-symbols-outlined text-2xl">add</span>
                            </button>
                        </div>
                    @endif

                    {{-- Panneau d'édition (un seul média à la fois) --}}
                    @if($editingMediaIndex !== null && isset($mediaPhotos[$editingMediaIndex]))
                        @php
                            $mi = $editingMediaIndex;
                            $item = $mediaPhotos[$mi];
                            $isVideo = ($item['type'] ?? 'image') === 'video';
                            $isVideoLink = $isVideo && (
                                ($item['source'] ?? '') === 'url'
                                || \App\Support\MediaVideoUrl::isExternal($item['src'] ?? '')
                            );
                        @endphp
                        <div class="p-3 bg-white border border-[#2c6904]/30 rounded-xl space-y-3 shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-xs text-[#1e1b18] flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-[#2c6904]">edit</span>
                                    Modifier {{ $isVideo ? 'la vidéo' : 'la photo' }}
                                    @if($isVideoLink)
                                        <span class="text-[10px] font-normal text-[#283593] bg-[#e8eaf6] px-1.5 py-0.5 rounded">Lien externe</span>
                                    @endif
                                </p>
                                <button type="button" wire:click="closeMediaEdit" class="p-1 text-[#717a69] hover:bg-[#f5ece7] rounded-lg">
                                    <span class="material-symbols-outlined text-base">close</span>
                                </button>
                            </div>

                            <div class="flex gap-3">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-[#f5ece7] flex-shrink-0 flex items-center justify-center">
                                    @if(!empty($item['src']))
                                        @if($isVideo && $isVideoLink && \App\Support\MediaVideoUrl::isEmbeddable($item['src']))
                                        <iframe src="{{ \App\Support\MediaVideoUrl::embedUrl($item['src']) }}"
                                                class="w-full h-full pointer-events-none" title="Aperçu"></iframe>
                                        @elseif($isVideo)
                                        <video src="{{ $item['src'] }}" class="w-full h-full object-cover" controls preload="metadata"></video>
                                        @else
                                        <img src="{{ $item['src'] }}" alt="" class="w-full h-full object-cover" />
                                        @endif
                                    @else
                                        <span class="material-symbols-outlined text-2xl text-[#c1c9b6]">broken_image</span>
                                    @endif
                                </div>
                                <div class="flex-1 space-y-2 min-w-0">
                                    @if($isVideoLink)
                                    <div>
                                        <label class="text-[10px] font-semibold text-[#717a69] uppercase tracking-wide">Lien vidéo</label>
                                        <input type="url" wire:model.blur="mediaPhotos.{{ $mi }}.src"
                                               class="{{ $inputCls }} text-xs py-1.5 mt-0.5"
                                               placeholder="https://…" />
                                    </div>
                                    <button type="button" onclick="replaceMediaUpload({{ $mi }}, 'video')"
                                            class="text-[10px] text-[#283593] font-semibold hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">upload_file</span>
                                        Remplacer par un fichier importé
                                    </button>
                                    @else
                                    <div class="flex gap-1.5">
                                        <input type="text" wire:model.blur="mediaPhotos.{{ $mi }}.src" class="{{ $inputCls }} flex-1 text-xs py-1.5" readonly />
                                        <button type="button" onclick="replaceMediaUpload({{ $mi }}, '{{ $isVideo ? 'video' : 'photo' }}')"
                                                class="flex-shrink-0 px-2 py-1.5 bg-[#2c6904] text-white text-[10px] font-semibold rounded-lg hover:bg-[#448322]">
                                            Remplacer
                                        </button>
                                    </div>
                                    @endif
                                    <input type="text" wire:model.blur="mediaPhotos.{{ $mi }}.alt"
                                           class="{{ $inputCls }} text-xs py-1.5" placeholder="Légende (optionnel)" />
                                    <select wire:model.blur="mediaPhotos.{{ $mi }}.category" class="{{ $inputCls }} text-xs py-1.5">
                                        @foreach($mediaCategories as $cat)
                                            <option value="{{ $cat['key'] }}">{{ $cat['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <span wire:loading.remove wire:target="saveSection">Enregistrer et fermer</span>
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

@script
<script>
function pickMediaUpload(type) {
    const inputId = type === 'video' ? 'global-video-upload' : 'global-photo-upload';
    $wire.set('mediaUploadSlot', 'media.new')
        .then(function () {
            return $wire.set('pendingMediaType', type);
        })
        .then(function () {
            $wire.set('mediaTab', type === 'video' ? 'video' : 'photo');
            $wire.set('editingMediaIndex', null);
            if (type === 'video') {
                $wire.set('videoLinkFormOpen', false);
            }
            setTimeout(function () {
                const input = document.getElementById(inputId);
                if (input) {
                    input.value = '';
                    input.click();
                }
            }, 80);
        });
}

function replaceMediaUpload(index, type) {
    const inputId = type === 'video' ? 'global-video-upload' : 'global-photo-upload';
    $wire.set('mediaUploadSlot', 'media.' + index);
    setTimeout(function () {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = '';
            input.click();
        }
    }, 50);
}

window.pickMediaUpload = pickMediaUpload;
window.replaceMediaUpload = replaceMediaUpload;
</script>
@endscript
