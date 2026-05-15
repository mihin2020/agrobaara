<div class="space-y-6 max-w-4xl mx-auto">

    {{-- ── Modal Nationalité ── --}}
    @if($showNationalityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-init="document.body.style.overflow='hidden'"
             x-destroy="document.body.style.overflow=''"
             wire:keydown.escape="closeNationalityModal">

            {{-- Backdrop flou --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeNationalityModal"></div>

            {{-- Carte modale --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- En-tête --}}
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

                {{-- Champ --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nationalité *</label>
                    <input wire:model="newNationality"
                           type="text"
                           placeholder="Ex: Mauritanien(ne)"
                           autofocus
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('newNationality') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]"
                           wire:keydown.enter="saveNationality" />
                    @error('newNationality')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Actions --}}
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
        <a href="{{ route('admin.candidates.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Nouveau Candidat</h2>
            <p class="text-[#41493b] text-sm mt-0.5">Enregistrer un profil agroécologique</p>
        </div>
    </div>

    {{-- Progression --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    1 => ['label' => 'Identité',    'icon' => 'badge'],
                    2 => ['label' => 'Contacts',    'icon' => 'contact_phone'],
                    3 => ['label' => 'Formation',   'icon' => 'school'],
                    4 => ['label' => 'Expériences', 'icon' => 'work_history'],
                    5 => ['label' => 'Besoins',     'icon' => 'checklist'],
                ];
            @endphp
            @foreach($steps as $num => $step)
                <button type="button" wire:click="goToSection({{ $num }})"
                        class="flex flex-col items-center gap-1 flex-1 py-2 rounded-xl transition-all {{ $num < $currentSection ? 'cursor-pointer' : 'cursor-default' }}">
                    <span class="w-9 h-9 rounded-full flex items-center justify-center material-symbols-outlined text-lg transition-all
                        {{ $currentSection === $num ? 'bg-[#2c6904] text-white' : ($num < $currentSection ? 'bg-[#aef585] text-[#2c6904]' : 'bg-[#f5ece7] text-[#717a69]') }}">
                        {{ $step['icon'] }}
                    </span>
                    <span class="text-[10px] font-semibold hidden sm:block
                        {{ $currentSection === $num || $num < $currentSection ? 'text-[#2c6904]' : 'text-[#717a69]' }}">
                        {{ $step['label'] }}
                    </span>
                </button>
                @if($num < 5)
                    <div class="flex-1 h-0.5 mx-1 {{ $num < $currentSection ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Erreurs de validation --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
            <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-base text-red-500 mt-0.5 flex-shrink-0">error</span>
                <div>
                    <p class="font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Toutes les sections (toujours dans le DOM, visibilité CSS) --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm">

        {{-- ── Section 1 — Identité ── --}}
        <div class="{{ $currentSection !== 1 ? 'hidden' : '' }} p-6 space-y-5">
            <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#2c6904]">badge</span>
                Section A — Identité & Localisation
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Prénom *</label>
                    <input wire:model="first_name" type="text" placeholder="Ex: Moussa"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('first_name') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nom *</label>
                    <input wire:model="last_name" type="text" placeholder="Ex: SAWADOGO"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('last_name') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Sexe *</label>
                    <select wire:model="gender"
                            class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('gender') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                        <option value="">Sélectionner...</option>
                        @foreach($genders as $g)
                            <option value="{{ $g->value }}">{{ $g->label() }}</option>
                        @endforeach
                    </select>
                    @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Situation matrimoniale</label>
                    <select wire:model="marital_status"
                            class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                        <option value="">Sélectionner...</option>
                        <option value="celibataire">Célibataire</option>
                        <option value="marie">Marié(e)</option>
                        <option value="veuf">Veuf/Veuve</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Date de naissance *</label>
                    <input wire:model="birth_date" type="date"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('birth_date') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    @error('birth_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Lieu de naissance</label>
                    <input wire:model="birth_place" type="text" placeholder="Ex: Ouagadougou"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                </div>
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
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Commune de résidence *</label>
                    <select wire:model="commune_id"
                            class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('commune_id') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                        <option value="">Sélectionner une commune...</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                        @endforeach
                    </select>
                    @error('commune_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Adresse / Quartier</label>
                    <input wire:model="address" type="text" placeholder="Ex: Secteur 15, rue de l'église"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Moyen de déplacement</label>
                    <select wire:model="transport_mode"
                            class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                        <option value="">Sélectionner...</option>
                        @foreach($transports as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Permis de conduire</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($licenses as $license)
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" wire:model="license_ids" value="{{ $license->id }}"
                                       class="w-4 h-4 text-[#2c6904] border-[#c1c9b6] rounded" />
                                <span class="text-sm font-semibold text-[#1e1b18]">{{ $license->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 2 — Contacts & Langues ── --}}
        <div class="{{ $currentSection !== 2 ? 'hidden' : '' }} p-6 space-y-5">
            <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#2c6904]">contact_phone</span>
                Section B — Contacts & Langues
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Téléphone principal *</label>
                    <input wire:model="phone" type="text" placeholder="Ex: +226 70 00 00 00"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('phone') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Téléphone secondaire</label>
                    <input wire:model="phone_secondary" type="text" placeholder="Ex: +226 71 00 00 00"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Adresse e-mail</label>
                    <input wire:model="email" type="email" placeholder="Ex: moussa@email.com"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('email') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
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
                            <input type="checkbox" wire:model.live="language_ids" value="{{ $language->id }}" class="sr-only" />
                            <span class="text-sm font-semibold">{{ $language->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Section 3 — Formation ── --}}
        <div class="{{ $currentSection !== 3 ? 'hidden' : '' }} p-6 space-y-5">
            <h3 class="font-sora font-bold text-base text-[#1e1b18] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#2c6904]">school</span>
                Section C — Formation & Éducation
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Niveau d'étude *</label>
                    <select wire:model="education_level"
                            class="w-full px-4 py-2.5 bg-[#fbf2ed] border {{ $errors->has('education_level') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                        <option value="">Sélectionner...</option>
                        @foreach($educations as $e)
                            <option value="{{ $e->code }}">{{ $e->name }}</option>
                        @endforeach
                    </select>
                    @error('education_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Lieu de formation agroécologique</label>
                    <input wire:model="agro_training_place" type="text" placeholder="Ex: ENEF, Dindéresso"
                           class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Formation agroécologique reçue</label>
                <textarea wire:model="agro_training_text" rows="4"
                          placeholder="Décrivez les formations reçues en lien avec l'agroécologie..."
                          class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
            </div>
        </div>

        {{-- ── Section 4 — Compétences & Expériences ── --}}
        <div class="{{ $currentSection !== 4 ? 'hidden' : '' }} p-6 space-y-6">
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
                            <input type="checkbox" wire:model.live="skill_ids" value="{{ $skill->id }}" class="sr-only" />
                            <span class="text-xs font-semibold">{{ $skill->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Autres compétences</label>
                <textarea wire:model="other_skills_text" rows="3"
                          placeholder="Mentionner d'autres compétences non listées..."
                          class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
            </div>
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-[#1e1b18]">Expériences professionnelles</label>
                    <button type="button" wire:click="addExperience"
                            class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline">
                        <span class="material-symbols-outlined text-base">add_circle</span> Ajouter
                    </button>
                </div>
                @foreach($experiences as $i => $exp)
                    <div class="bg-[#fbf2ed] rounded-xl p-4 mb-3 border border-[#c1c9b6] space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-[#41493b] uppercase">Expérience {{ $i + 1 }}</span>
                            <button type="button" wire:click="removeExperience({{ $i }})" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Année</label>
                                <input wire:model="experiences.{{ $i }}.year" type="number" placeholder="Ex: 2022"
                                       class="w-full px-3 py-2 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Poste occupé</label>
                                <input wire:model="experiences.{{ $i }}.position" type="text" placeholder="Ex: Agent maraîcher"
                                       class="w-full px-3 py-2 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Lieu</label>
                                <input wire:model="experiences.{{ $i }}.location" type="text" placeholder="Ex: Ouagadougou"
                                       class="w-full px-3 py-2 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Contact employeur</label>
                                <input wire:model="experiences.{{ $i }}.employer_contacts" type="text" placeholder="Ex: 70 00 00 00"
                                       class="w-full px-3 py-2 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:border-[#2c6904]" />
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(empty($experiences))
                    <div class="border-2 border-dashed border-[#c1c9b6] rounded-xl p-6 text-center">
                        <span class="material-symbols-outlined text-[#c1c9b6] text-3xl block mb-1">work</span>
                        <p class="text-sm text-[#717a69]">Aucune expérience ajoutée.</p>
                        <button type="button" wire:click="addExperience"
                                class="mt-2 text-sm text-[#2c6904] font-semibold hover:underline">
                            Ajouter une expérience
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Section 5 — Besoins internes ── --}}
        <div class="{{ $currentSection !== 5 ? 'hidden' : '' }} p-6 space-y-5">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600">admin_panel_settings</span>
                <h3 class="font-sora font-bold text-base text-[#1e1b18]">Section E — Besoins exprimés</h3>
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-normal ml-1">Interne BAARA</span>
            </div>
            <p class="text-xs text-[#717a69] -mt-2">Sélectionnez les besoins du candidat. Plusieurs choix possibles par catégorie.</p>

            <div class="space-y-3">

                {{-- Carte 1 — Emploi --}}
                @php $emploiActive = count($need_employment_types) > 0; @endphp
                <div @class([
                    'rounded-2xl border-2 overflow-hidden transition-all duration-200',
                    'border-[#2c6904]/40 bg-[#aef585]/10' => $emploiActive,
                    'border-[#c1c9b6] bg-white' => !$emploiActive,
                ])>
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div @class([
                                'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                                'bg-[#aef585]/40' => $emploiActive,
                                'bg-[#f5ece7]' => !$emploiActive,
                            ])>
                                <span @class(['material-symbols-outlined text-base', 'text-[#2c6904]' => $emploiActive, 'text-[#717a69]' => !$emploiActive])>work</span>
                            </div>
                            <div>
                                <p @class(['text-sm font-bold', 'text-[#2c6904]' => $emploiActive, 'text-[#1e1b18]' => !$emploiActive])>Emploi</p>
                                <p class="text-[11px] text-[#717a69]">Type d'emploi recherché</p>
                            </div>
                        </div>
                        @if($emploiActive)
                            <span class="text-[11px] font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-0.5 rounded-full">
                                {{ count($need_employment_types) }} sélectionné(s)
                            </span>
                        @endif
                    </div>
                    <div class="px-5 pb-4 flex flex-wrap gap-2">
                        @foreach([
                            'emploi_salarie'       => 'Emploi salarié',
                            'contrats_saisonniers' => 'Contrats saisonniers',
                            'missions_ponctuelles' => 'Missions ponctuelles',
                            'apprentissage'        => 'Apprentissage',
                            'entrepreneuriat'      => 'Entrepreneuriat',
                        ] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model.live="need_employment_types" value="{{ $value }}" class="sr-only" />
                                <span @class([
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all',
                                    'bg-[#2c6904] text-white border-[#2c6904] shadow-sm' => in_array($value, $need_employment_types),
                                    'bg-white text-[#41493b] border-[#c1c9b6] hover:border-[#2c6904]/40 hover:text-[#2c6904]' => !in_array($value, $need_employment_types),
                                ])>
                                    @if(in_array($value, $need_employment_types))
                                        <span class="material-symbols-outlined" style="font-size:11px">check</span>
                                    @endif
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Carte 2 — Formation --}}
                @php $formationActive = count($need_formation_types) > 0; @endphp
                <div @class([
                    'rounded-2xl border-2 overflow-hidden transition-all duration-200',
                    'border-[#2c6904]/40 bg-[#aef585]/10' => $formationActive,
                    'border-[#c1c9b6] bg-white' => !$formationActive,
                ])>
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div @class([
                                'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                                'bg-[#aef585]/40' => $formationActive,
                                'bg-[#f5ece7]' => !$formationActive,
                            ])>
                                <span @class(['material-symbols-outlined text-base', 'text-[#2c6904]' => $formationActive, 'text-[#717a69]' => !$formationActive])>school</span>
                            </div>
                            <div>
                                <p @class(['text-sm font-bold', 'text-[#2c6904]' => $formationActive, 'text-[#1e1b18]' => !$formationActive])>Formation</p>
                                <p class="text-[11px] text-[#717a69]">Opportunités de formation en agroécologie</p>
                            </div>
                        </div>
                        @if($formationActive)
                            <span class="text-[11px] font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-0.5 rounded-full">
                                {{ count($need_formation_types) }} sélectionné(s)
                            </span>
                        @endif
                    </div>
                    <div class="px-5 pb-4 flex flex-wrap gap-2">
                        @foreach([
                            'cours'          => 'Cours',
                            'master'         => 'Master',
                            'atelier'        => 'Atelier',
                            'stage'          => 'Stage',
                            'voyage_echange' => 'Voyage d\'échange',
                        ] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model.live="need_formation_types" value="{{ $value }}" class="sr-only" />
                                <span @class([
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all',
                                    'bg-[#2c6904] text-white border-[#2c6904] shadow-sm' => in_array($value, $need_formation_types),
                                    'bg-white text-[#41493b] border-[#c1c9b6] hover:border-[#2c6904]/40 hover:text-[#2c6904]' => !in_array($value, $need_formation_types),
                                ])>
                                    @if(in_array($value, $need_formation_types))
                                        <span class="material-symbols-outlined" style="font-size:11px">check</span>
                                    @endif
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Cartes 3 & 4 — Financement + Appui CV --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    {{-- Financement --}}
                    <button type="button" wire:click="$toggle('need_financing')"
                            @class([
                                'rounded-2xl border-2 p-4 text-left transition-all duration-200 w-full',
                                'border-amber-400 bg-amber-50' => $need_financing,
                                'border-[#c1c9b6] bg-white hover:border-amber-300/60' => !$need_financing,
                            ])>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div @class([
                                    'w-8 h-8 rounded-lg flex items-center justify-center',
                                    'bg-amber-100' => $need_financing,
                                    'bg-[#f5ece7]' => !$need_financing,
                                ])>
                                    <span @class(['material-symbols-outlined text-base', 'text-amber-600' => $need_financing, 'text-[#717a69]' => !$need_financing])>payments</span>
                                </div>
                                <div>
                                    <p @class(['text-sm font-bold', 'text-amber-700' => $need_financing, 'text-[#1e1b18]' => !$need_financing])>Financement</p>
                                    <p class="text-[11px] text-[#717a69] leading-tight">Opportunités de financement<br>& services entrepreneuriat</p>
                                </div>
                            </div>
                            {{-- Toggle visuel --}}
                            <div @class([
                                'w-11 h-6 rounded-full transition-colors flex-shrink-0 relative',
                                'bg-amber-400' => $need_financing,
                                'bg-gray-200' => !$need_financing,
                            ])>
                                <div @class([
                                    'absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200',
                                    'translate-x-5' => $need_financing,
                                    'translate-x-0.5' => !$need_financing,
                                ])></div>
                            </div>
                        </div>
                    </button>

                    {{-- Appui CV --}}
                    <button type="button" wire:click="$toggle('need_cv_support')"
                            @class([
                                'rounded-2xl border-2 p-4 text-left transition-all duration-200 w-full',
                                'border-purple-400 bg-purple-50' => $need_cv_support,
                                'border-[#c1c9b6] bg-white hover:border-purple-300/60' => !$need_cv_support,
                            ])>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div @class([
                                    'w-8 h-8 rounded-lg flex items-center justify-center',
                                    'bg-purple-100' => $need_cv_support,
                                    'bg-[#f5ece7]' => !$need_cv_support,
                                ])>
                                    <span @class(['material-symbols-outlined text-base', 'text-purple-600' => $need_cv_support, 'text-[#717a69]' => !$need_cv_support])>description</span>
                                </div>
                                <div>
                                    <p @class(['text-sm font-bold', 'text-purple-700' => $need_cv_support, 'text-[#1e1b18]' => !$need_cv_support])>Appui rédaction CV</p>
                                    <p class="text-[11px] text-[#717a69] leading-tight">Accompagnement à la<br>rédaction de CV</p>
                                </div>
                            </div>
                            <div @class([
                                'w-11 h-6 rounded-full transition-colors flex-shrink-0 relative',
                                'bg-purple-400' => $need_cv_support,
                                'bg-gray-200' => !$need_cv_support,
                            ])>
                                <div @class([
                                    'absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200',
                                    'translate-x-5' => $need_cv_support,
                                    'translate-x-0.5' => !$need_cv_support,
                                ])></div>
                            </div>
                        </div>
                    </button>

                </div>
            </div>

            {{-- Notes opérateur --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Notes de l'opérateur</label>
                <textarea wire:model="operator_notes" rows="4"
                          placeholder="Observations internes, remarques importantes..."
                          class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="px-6 pb-6 flex justify-between items-center border-t border-[#c1c9b6]/50 pt-5">
            @if($currentSection > 1)
                <button type="button" wire:click="prevSection"
                        class="flex items-center gap-2 px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Précédent
                </button>
            @else
                <div></div>
            @endif

            @if($currentSection < $totalSections)
                <button type="button" wire:click="nextSection"
                        wire:loading.attr="disabled" wire:target="nextSection"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#2c6904] text-white font-semibold rounded-xl hover:bg-[#448322] transition-colors text-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="nextSection">Suivant</span>
                    <span wire:loading wire:target="nextSection">Vérification...</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </button>
            @else
                <button type="button" wire:click="save"
                        wire:loading.attr="disabled" wire:target="save"
                        class="flex items-center gap-2 px-6 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm shadow-lg shadow-[#2c6904]/20 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-base">save</span>
                    <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                    <span wire:loading.remove wire:target="save">Enregistrer le candidat</span>
                    <span wire:loading wire:target="save">Enregistrement...</span>
                </button>
            @endif
        </div>
    </div>
</div>