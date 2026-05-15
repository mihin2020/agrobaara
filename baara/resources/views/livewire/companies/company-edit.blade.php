<div class="space-y-6 max-w-4xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.companies.show', $company) }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Modifier l'entreprise</h2>
            <p class="text-[#41493b] text-sm mt-0.5">{{ $company->name }} · <span class="font-mono text-xs text-[#875212]">{{ $company->reference }}</span></p>
        </div>
    </div>

    {{-- Section A — Informations générales --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#875212]">domain</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Section A — Informations générales</h3>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form.input wire:model="name" label="Nom de l'entreprise *" placeholder="Ex: GIE Yennega Agroécologie" :error="$errors->first('name')" />
                </div>
                <div>
                    <x-form.select wire:model="status" label="Statut juridique *" :error="$errors->first('status')">
                        <option value="">Sélectionner...</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s['value'] }}">{{ $s['label'] }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div></div>
                <x-form.input wire:model="legal_rep_first_name" label="Prénom représentant légal" placeholder="Ex: Fatimata" />
                <x-form.input wire:model="legal_rep_last_name" label="Nom représentant légal" placeholder="Ex: OUEDRAOGO" />
            </div>

            {{-- Types d'activité --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-2">
                    Types d'activité * <span class="text-xs font-normal text-[#717a69]">(plusieurs possibles)</span>
                </label>
                @error('activity_types') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
                <div class="flex flex-wrap gap-2 p-3 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6]">
                    @foreach($activityTypesList as $type)
                        <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1.5 rounded-lg border transition-all
                            {{ in_array($type, $activity_types) ? 'border-[#875212] bg-[#ffdcbd]/20 text-[#875212]' : 'border-[#c1c9b6] bg-white text-[#41493b] hover:border-[#875212]/50' }}">
                            <input type="checkbox" wire:model="activity_types" value="{{ $type }}" class="sr-only" />
                            <span class="text-xs font-semibold">{{ $type }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <x-form.textarea wire:model="description" label="Description de l'entreprise" placeholder="Présentation de l'entreprise, ses activités, son territoire..." rows="3" />
        </div>
    </div>

    {{-- Section B — Sites d'activité --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-[#875212]">location_on</span>
                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Section B — Sites d'activité</h3>
            </div>
            <button wire:click="addSite" type="button"
                    class="flex items-center gap-1 text-xs text-[#875212] font-semibold hover:underline">
                <span class="material-symbols-outlined text-base">add_location_alt</span>
                Ajouter un site
            </button>
        </div>
        <div class="p-6 space-y-4">
            @error('sites') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
            @foreach($sites as $i => $site)
                <div class="bg-[#fbf2ed] rounded-xl p-4 border border-[#c1c9b6] space-y-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#41493b] uppercase">Site {{ $i + 1 }}</span>
                            @if($site['is_main'])
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-[#875212] text-white rounded-full">Principal</span>
                            @endif
                        </div>
                        @if(count($sites) > 1)
                            <button wire:click="removeSite({{ $i }})" type="button" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <x-form.input wire:model="sites.{{ $i }}.label" label="Intitulé du site *" placeholder="Ex: Siège social, Site de production" :error="$errors->first('sites.'.$i.'.label')" />
                        <div>
                            <x-form.select wire:model="sites.{{ $i }}.commune_id" label="Commune *" :error="$errors->first('sites.'.$i.'.commune_id')">
                                <option value="">Sélectionner une commune...</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        <div class="md:col-span-2">
                            <x-form.input wire:model="sites.{{ $i }}.address" label="Adresse précise" placeholder="Ex: Zone industrielle, secteur 28" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Section C — Contacts --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#875212]">contact_phone</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Section C — Contacts</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <x-form.input wire:model="phone" label="Téléphone *" placeholder="Ex: +226 25 00 00 00" :error="$errors->first('phone')" />
            <x-form.input wire:model="email" label="E-mail" type="email" placeholder="Ex: contact@entreprise.bf" />
            <div class="md:col-span-2">
                <x-form.input wire:model="website" label="Site web" placeholder="Ex: https://www.entreprise.bf" />
            </div>
            {{-- Pages sociales --}}
            <div class="md:col-span-2">
                <p class="text-xs font-semibold text-[#41493b] mb-2 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-[#875212]">share</span>
                    Pages sociales <span class="font-normal text-[#717a69]">(optionnel)</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-center gap-2.5 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] px-3 focus-within:border-[#875212]/60 transition-colors">
                        <span class="font-bold text-sm flex-shrink-0" style="color:#1877F2">f</span>
                        <input wire:model="social_facebook" type="url" placeholder="https://facebook.com/..."
                               class="flex-1 bg-transparent py-2.5 text-sm text-[#1e1b18] placeholder-[#717a69] outline-none" />
                    </div>
                    <div class="flex items-center gap-2.5 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] px-3 focus-within:border-[#875212]/60 transition-colors">
                        <span class="font-bold text-sm flex-shrink-0" style="color:#0A66C2">in</span>
                        <input wire:model="social_linkedin" type="url" placeholder="https://linkedin.com/..."
                               class="flex-1 bg-transparent py-2.5 text-sm text-[#1e1b18] placeholder-[#717a69] outline-none" />
                    </div>
                    <div class="flex items-center gap-2.5 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] px-3 focus-within:border-[#875212]/60 transition-colors">
                        <span class="font-bold text-sm flex-shrink-0" style="color:#25D366">W</span>
                        <input wire:model="social_whatsapp" type="text" placeholder="+226 XX XX XX XX"
                               class="flex-1 bg-transparent py-2.5 text-sm text-[#1e1b18] placeholder-[#717a69] outline-none" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section D — Besoins internes --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-amber-50 flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-amber-600">admin_panel_settings</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Section D — Besoins exprimés
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-normal ml-2">Interne BAARA</span>
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-4 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] cursor-pointer hover:border-[#875212]/50 transition-colors">
                    <input type="checkbox" wire:model="need_training" class="w-4 h-4 text-[#875212] rounded" />
                    <div>
                        <p class="font-semibold text-sm">Formation</p>
                        <p class="text-xs text-[#717a69]">Perfectionnement et innovation en AE</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] cursor-pointer hover:border-[#875212]/50 transition-colors">
                    <input type="checkbox" wire:model="need_financing" class="w-4 h-4 text-[#875212] rounded" />
                    <div>
                        <p class="font-semibold text-sm">Financement</p>
                        <p class="text-xs text-[#717a69]">Accès au crédit pour les entreprises</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] cursor-pointer hover:border-[#875212]/50 transition-colors">
                    <input type="checkbox" wire:model="need_contract_support" class="w-4 h-4 text-[#875212] rounded" />
                    <div>
                        <p class="font-semibold text-sm">Appui contractuel</p>
                        <p class="text-xs text-[#717a69]">Formalisation du contrat de travail</p>
                    </div>
                </label>
            </div>
            <x-form.textarea wire:model="operator_notes" label="Notes de l'opérateur" placeholder="Observations, contexte, priorités..." rows="3" />
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex justify-end gap-3 pb-4">
        <a href="{{ route('admin.companies.show', $company) }}" wire:navigate
           class="px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
            Annuler
        </a>
        <button wire:click="save" type="button"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
                class="flex items-center gap-2 px-6 py-2.5 bg-[#875212] text-white font-bold rounded-xl hover:opacity-90 transition-opacity text-sm shadow-lg shadow-[#875212]/20">
            <span wire:loading.remove class="material-symbols-outlined text-base">save</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Enregistrer les modifications</span>
            <span wire:loading>Enregistrement...</span>
        </button>
    </div>
</div>
