<div class="space-y-6 max-w-4xl mx-auto">

    {{-- ── Modal Nationalité ── --}}
    @if($showNationalityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-init="document.body.style.overflow='hidden'"
             x-destroy="document.body.style.overflow=''"
             wire:keydown.escape="closeNationalityModal">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeNationalityModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#2c6904]/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#2c6904] text-base">flag</span>
                        </div>
                        <h3 class="font-sora font-bold text-base text-[#1e1b18]">Nouvelle nationalité</h3>
                    </div>
                    <button wire:click="closeNationalityModal"
                            class="p-1.5 text-[#717a69] hover:text-[#1e1b18] hover:bg-[#f5ece7] rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nationalité *</label>
                    <input wire:model="newNationality" type="text" placeholder="Ex: Mauritanien(ne)" autofocus
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('newNationality') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]"
                           wire:keydown.enter="saveNationality" />
                    @error('newNationality')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">error</span>{{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="flex gap-3 pt-1">
                    <button wire:click="closeNationalityModal"
                            class="flex-1 px-4 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                        Annuler
                    </button>
                    <button wire:click="saveNationality"
                            wire:loading.attr="disabled" wire:target="saveNationality"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2c6904] text-white font-semibold text-sm rounded-xl hover:bg-[#448322] transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveNationality" class="material-symbols-outlined text-base">add</span>
                        <span wire:loading wire:target="saveNationality" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                        Ajouter
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.candidates.show', $candidate) }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Modifier le candidat</h2>
            <p class="text-[#41493b] text-sm mt-0.5">{{ $candidate->full_name }} · <span class="font-mono text-xs">{{ $candidate->reference }}</span></p>
        </div>
    </div>

    {{-- Progression --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4">
        <div class="flex items-center justify-between">
            @php
                $sections = [
                    1 => ['label' => 'Identité', 'icon' => 'badge'],
                    2 => ['label' => 'Contacts', 'icon' => 'contact_phone'],
                    3 => ['label' => 'Formation', 'icon' => 'school'],
                    4 => ['label' => 'Expériences', 'icon' => 'work_history'],
                    5 => ['label' => 'Besoins', 'icon' => 'checklist'],
                ];
            @endphp
            @foreach($sections as $num => $section)
                <button wire:click="$set('currentSection', {{ $num }})"
                        class="flex flex-col items-center gap-1 flex-1 py-2 rounded-xl transition-all cursor-pointer">
                    <span @class([
                        'w-8 h-8 rounded-full flex items-center justify-center material-symbols-outlined text-base transition-all',
                        'bg-[#2c6904] text-white' => $currentSection === $num,
                        'bg-[#aef585] text-[#2c6904]' => $num < $currentSection,
                        'bg-[#f5ece7] text-[#717a69]' => $num > $currentSection,
                    ])>{{ $section['icon'] }}</span>
                    <span @class([
                        'text-[10px] font-semibold hidden sm:block',
                        'text-[#2c6904]' => $currentSection === $num || $num < $currentSection,
                        'text-[#717a69]' => $num > $currentSection,
                    ])>{{ $section['label'] }}</span>
                </button>
                @if($num < 5)
                    <div @class(['flex-1 h-0.5 mx-1', 'bg-[#2c6904]' => $num < $currentSection, 'bg-[#c1c9b6]' => $num >= $currentSection])></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Sections --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm">

        @if($currentSection === 1)
            <div class="p-6 space-y-5">
                <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2c6904]">badge</span>
                    Section A — Identité & Localisation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form.input wire:model="first_name" label="Prénom *" placeholder="Ex: Moussa" :error="$errors->first('first_name')" />
                    <x-form.input wire:model="last_name" label="Nom *" placeholder="Ex: SAWADOGO" :error="$errors->first('last_name')" />
                    <x-form.select wire:model="gender" label="Sexe *" :error="$errors->first('gender')">
                        <option value="">Sélectionner...</option>
                        @foreach($genders as $g)
                            <option value="{{ $g->value }}">{{ $g->label() }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select wire:model="marital_status" label="Situation matrimoniale">
                        <option value="">Sélectionner...</option>
                        <option value="celibataire">Célibataire</option>
                        <option value="marie">Marié(e)</option>
                        <option value="veuf">Veuf/Veuve</option>
                    </x-form.select>
                    <x-form.input wire:model="birth_date" label="Date de naissance *" type="date" :error="$errors->first('birth_date')" />
                    <x-form.input wire:model="birth_place" label="Lieu de naissance" placeholder="Ex: Ouagadougou" />

                    {{-- Nationalité --}}
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-[#1e1b18]">Nationalité *</label>
                            <button type="button" wire:click="openNationalityModal"
                                    class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline">
                                <span class="material-symbols-outlined text-sm">add_circle</span>
                                Ajouter une nationalité
                            </button>
                        </div>
                        <select wire:model="nationality"
                                class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('nationality') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                            <option value="">Sélectionner une nationalité...</option>
                            @foreach($nationalities as $nat)
                                <option value="{{ $nat->name }}">{{ $nat->name }}</option>
                            @endforeach
                        </select>
                        @error('nationality') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <x-form.select wire:model="commune_id" label="Commune de résidence *" :error="$errors->first('commune_id')">
                        <option value="">Sélectionner une commune...</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input wire:model="address" label="Adresse / Quartier" placeholder="Ex: Secteur 15, rue de l'église" />
                    <x-form.select wire:model="transport_mode" label="Moyen de déplacement">
                        <option value="">Sélectionner...</option>
                        @foreach($transports as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </x-form.select>
                    <div>
                        <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Permis de conduire</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($licenses as $license)
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" wire:model="license_ids" value="{{ $license->id }}"
                                           class="w-4 h-4 text-[#2c6904] border-[#c1c9b6] rounded focus:ring-[#2c6904]/20" />
                                    <span class="text-sm font-semibold text-[#1e1b18]">{{ $license->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($currentSection === 2)
            <div class="p-6 space-y-5">
                <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2c6904]">contact_phone</span>
                    Section B — Contacts & Langues
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form.input wire:model="phone" label="Téléphone principal *" placeholder="Ex: +226 70 00 00 00" :error="$errors->first('phone')" />
                    <x-form.input wire:model="phone_secondary" label="Téléphone secondaire" placeholder="Ex: +226 71 00 00 00" />
                    <x-form.input wire:model="email" label="Adresse e-mail" type="email" placeholder="Ex: moussa@email.com" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-2">
                        Langues parlées * <span class="text-xs font-normal text-[#717a69]">(au moins une)</span>
                    </label>
                    @error('language_ids') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
                    <div class="flex flex-wrap gap-3">
                        @foreach($languages as $language)
                            <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl border transition-all
                                {{ in_array($language->id, $language_ids) ? 'border-[#2c6904] bg-[#aef585]/20 text-[#2c6904]' : 'border-[#c1c9b6] text-[#41493b] hover:border-[#2c6904]/50' }}">
                                <input type="checkbox" wire:model="language_ids" value="{{ $language->id }}" class="sr-only" />
                                <span class="text-sm font-semibold">{{ $language->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if($currentSection === 3)
            <div class="p-6 space-y-5">
                <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2c6904]">school</span>
                    Section C — Formation & Éducation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form.select wire:model="education_level" label="Niveau d'étude *" :error="$errors->first('education_level')">
                        <option value="">Sélectionner...</option>
                        @foreach($educations as $e)
                            <option value="{{ $e->code }}">{{ $e->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input wire:model="agro_training_place" label="Lieu de formation agroécologique" placeholder="Ex: ENEF, Dindéresso" />
                </div>
                <x-form.textarea wire:model="agro_training_text" label="Formation agroécologique reçue" placeholder="Décrivez les formations reçues en lien avec l'agroécologie..." rows="4" />
            </div>
        @endif

        @if($currentSection === 4)
            <div class="p-6 space-y-6">
                <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2c6904]">work_history</span>
                    Section D — Compétences & Expériences
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Compétences agroécologiques</label>
                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-3 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6]">
                        @foreach($skills as $skill)
                            <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1.5 rounded-lg border transition-all
                                {{ in_array($skill->id, $skill_ids) ? 'border-[#2c6904] bg-[#aef585]/20 text-[#2c6904]' : 'border-[#c1c9b6] bg-white text-[#41493b] hover:border-[#2c6904]/50' }}">
                                <input type="checkbox" wire:model="skill_ids" value="{{ $skill->id }}" class="sr-only" />
                                <span class="text-xs font-semibold">{{ $skill->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <x-form.textarea wire:model="other_skills_text" label="Autres compétences" placeholder="Mentionner d'autres compétences non listées..." rows="3" />
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-[#1e1b18]">Expériences professionnelles</label>
                        <button wire:click="addExperience" type="button"
                                class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline">
                            <span class="material-symbols-outlined text-base">add_circle</span>
                            Ajouter
                        </button>
                    </div>
                    @foreach($experiences as $i => $exp)
                        <div class="bg-[#fbf2ed] rounded-xl p-4 mb-3 border border-[#c1c9b6] space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-[#41493b] uppercase">Expérience {{ $i + 1 }}</span>
                                <button wire:click="removeExperience({{ $i }})" type="button" class="text-red-500 hover:text-red-700">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <x-form.input wire:model="experiences.{{ $i }}.year" label="Année" placeholder="Ex: 2022" type="number" />
                                <x-form.input wire:model="experiences.{{ $i }}.position" label="Poste occupé" placeholder="Ex: Agent maraîcher" />
                                <x-form.input wire:model="experiences.{{ $i }}.location" label="Lieu" placeholder="Ex: Ouagadougou" />
                                <x-form.input wire:model="experiences.{{ $i }}.employer_contacts" label="Contact employeur" placeholder="Ex: 70 00 00 00" />
                            </div>
                        </div>
                    @endforeach
                    @if(empty($experiences))
                        <div class="border-2 border-dashed border-[#c1c9b6] rounded-xl p-6 text-center">
                            <span class="material-symbols-outlined text-[#c1c9b6] text-3xl block mb-1">work</span>
                            <p class="text-sm text-[#717a69]">Aucune expérience ajoutée.</p>
                            <button wire:click="addExperience" type="button" class="mt-2 text-sm text-[#2c6904] font-semibold hover:underline">
                                Ajouter une expérience
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($currentSection === 5)
            <div class="p-6 space-y-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600">admin_panel_settings</span>
                    <h3 class="font-sora font-bold text-base text-[#1e1b18]">Section E — Besoins exprimés</h3>
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-normal ml-1">Interne BAARA</span>
                </div>
                <p class="text-xs text-[#717a69] -mt-2">Sélectionnez les besoins du candidat. Plusieurs choix possibles par catégorie.</p>

                <div class="space-y-3">
                    {{-- Carte Emploi --}}
                    @php $emploiActive = count($need_employment_types) > 0; @endphp
                    <div @class(['rounded-2xl border-2 overflow-hidden transition-all duration-200', 'border-[#2c6904]/40 bg-[#aef585]/10' => $emploiActive, 'border-[#c1c9b6] bg-white' => !$emploiActive])>
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div @class(['w-8 h-8 rounded-lg flex items-center justify-center transition-colors', 'bg-[#aef585]/40' => $emploiActive, 'bg-[#f5ece7]' => !$emploiActive])>
                                    <span @class(['material-symbols-outlined text-base', 'text-[#2c6904]' => $emploiActive, 'text-[#717a69]' => !$emploiActive])>work</span>
                                </div>
                                <div>
                                    <p @class(['text-sm font-bold', 'text-[#2c6904]' => $emploiActive, 'text-[#1e1b18]' => !$emploiActive])>Emploi</p>
                                    <p class="text-[11px] text-[#717a69]">Type d'emploi recherché</p>
                                </div>
                            </div>
                            @if($emploiActive)
                                <span class="text-[11px] font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-0.5 rounded-full">{{ count($need_employment_types) }} sélectionné(s)</span>
                            @endif
                        </div>
                        <div class="px-5 pb-4 flex flex-wrap gap-2">
                            @foreach(['emploi_salarie' => 'Emploi salarié', 'contrats_saisonniers' => 'Contrats saisonniers', 'missions_ponctuelles' => 'Missions ponctuelles', 'apprentissage' => 'Apprentissage', 'entrepreneuriat' => 'Entrepreneuriat'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" wire:model.live="need_employment_types" value="{{ $value }}" class="sr-only" />
                                    <span @class(['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all', 'bg-[#2c6904] text-white border-[#2c6904] shadow-sm' => in_array($value, $need_employment_types), 'bg-white text-[#41493b] border-[#c1c9b6] hover:border-[#2c6904]/40 hover:text-[#2c6904]' => !in_array($value, $need_employment_types)])>
                                        @if(in_array($value, $need_employment_types))<span class="material-symbols-outlined" style="font-size:11px">check</span>@endif
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Carte Formation --}}
                    @php $formationActive = count($need_formation_types) > 0; @endphp
                    <div @class(['rounded-2xl border-2 overflow-hidden transition-all duration-200', 'border-[#2c6904]/40 bg-[#aef585]/10' => $formationActive, 'border-[#c1c9b6] bg-white' => !$formationActive])>
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div @class(['w-8 h-8 rounded-lg flex items-center justify-center transition-colors', 'bg-[#aef585]/40' => $formationActive, 'bg-[#f5ece7]' => !$formationActive])>
                                    <span @class(['material-symbols-outlined text-base', 'text-[#2c6904]' => $formationActive, 'text-[#717a69]' => !$formationActive])>school</span>
                                </div>
                                <div>
                                    <p @class(['text-sm font-bold', 'text-[#2c6904]' => $formationActive, 'text-[#1e1b18]' => !$formationActive])>Formation</p>
                                    <p class="text-[11px] text-[#717a69]">Opportunités de formation en agroécologie</p>
                                </div>
                            </div>
                            @if($formationActive)
                                <span class="text-[11px] font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-0.5 rounded-full">{{ count($need_formation_types) }} sélectionné(s)</span>
                            @endif
                        </div>
                        <div class="px-5 pb-4 flex flex-wrap gap-2">
                            @foreach(['cours' => 'Cours', 'master' => 'Master', 'atelier' => 'Atelier', 'stage' => 'Stage', 'voyage_echange' => 'Voyage d\'échange'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" wire:model.live="need_formation_types" value="{{ $value }}" class="sr-only" />
                                    <span @class(['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all', 'bg-[#2c6904] text-white border-[#2c6904] shadow-sm' => in_array($value, $need_formation_types), 'bg-white text-[#41493b] border-[#c1c9b6] hover:border-[#2c6904]/40 hover:text-[#2c6904]' => !in_array($value, $need_formation_types)])>
                                        @if(in_array($value, $need_formation_types))<span class="material-symbols-outlined" style="font-size:11px">check</span>@endif
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Financement + Appui CV --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button type="button" wire:click="$toggle('need_financing')"
                                @class(['rounded-2xl border-2 p-4 text-left transition-all duration-200 w-full', 'border-amber-400 bg-amber-50' => $need_financing, 'border-[#c1c9b6] bg-white hover:border-amber-300/60' => !$need_financing])>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div @class(['w-8 h-8 rounded-lg flex items-center justify-center', 'bg-amber-100' => $need_financing, 'bg-[#f5ece7]' => !$need_financing])>
                                        <span @class(['material-symbols-outlined text-base', 'text-amber-600' => $need_financing, 'text-[#717a69]' => !$need_financing])>payments</span>
                                    </div>
                                    <div>
                                        <p @class(['text-sm font-bold', 'text-amber-700' => $need_financing, 'text-[#1e1b18]' => !$need_financing])>Financement</p>
                                        <p class="text-[11px] text-[#717a69] leading-tight">Opportunités de financement<br>& services entrepreneuriat</p>
                                    </div>
                                </div>
                                <div @class(['w-11 h-6 rounded-full transition-colors flex-shrink-0 relative', 'bg-amber-400' => $need_financing, 'bg-gray-200' => !$need_financing])>
                                    <div @class(['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200', 'translate-x-5' => $need_financing, 'translate-x-0.5' => !$need_financing])></div>
                                </div>
                            </div>
                        </button>
                        <button type="button" wire:click="$toggle('need_cv_support')"
                                @class(['rounded-2xl border-2 p-4 text-left transition-all duration-200 w-full', 'border-purple-400 bg-purple-50' => $need_cv_support, 'border-[#c1c9b6] bg-white hover:border-purple-300/60' => !$need_cv_support])>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div @class(['w-8 h-8 rounded-lg flex items-center justify-center', 'bg-purple-100' => $need_cv_support, 'bg-[#f5ece7]' => !$need_cv_support])>
                                        <span @class(['material-symbols-outlined text-base', 'text-purple-600' => $need_cv_support, 'text-[#717a69]' => !$need_cv_support])>description</span>
                                    </div>
                                    <div>
                                        <p @class(['text-sm font-bold', 'text-purple-700' => $need_cv_support, 'text-[#1e1b18]' => !$need_cv_support])>Appui rédaction CV</p>
                                        <p class="text-[11px] text-[#717a69] leading-tight">Accompagnement à la<br>rédaction de CV</p>
                                    </div>
                                </div>
                                <div @class(['w-11 h-6 rounded-full transition-colors flex-shrink-0 relative', 'bg-purple-400' => $need_cv_support, 'bg-gray-200' => !$need_cv_support])>
                                    <div @class(['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200', 'translate-x-5' => $need_cv_support, 'translate-x-0.5' => !$need_cv_support])></div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <x-form.textarea wire:model="operator_notes" label="Notes de l'opérateur" placeholder="Observations internes, remarques importantes..." rows="4" />
            </div>
        @endif

        <div class="px-6 pb-6 flex justify-between items-center border-t border-[#c1c9b6]/50 pt-5">
            @if($currentSection > 1)
                <button wire:click="prevSection" type="button"
                        class="flex items-center gap-2 px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Précédent
                </button>
            @else
                <div></div>
            @endif

            @if($currentSection < $totalSections)
                <button wire:click="nextSection" type="button"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#2c6904] text-white font-semibold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                    Suivant
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </button>
            @else
                <button wire:click="save" type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75"
                        class="flex items-center gap-2 px-6 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm shadow-lg shadow-[#2c6904]/20">
                    <span wire:loading.remove class="material-symbols-outlined text-base">save</span>
                    <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                    <span wire:loading.remove>Enregistrer les modifications</span>
                    <span wire:loading>Enregistrement...</span>
                </button>
            @endif
        </div>
    </div>
</div>