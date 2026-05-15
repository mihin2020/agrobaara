<div class="space-y-6 max-w-4xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.offers.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Nouvelle Offre</h2>
            <p class="text-[#41493b] text-sm mt-0.5">Créer une opportunité d'emploi agroécologique</p>
        </div>
    </div>

    {{-- Infos essentielles --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#615c47]">work_outline</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Informations essentielles</h3>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form.select wire:model="company_id" label="Entreprise *" :error="$errors->first('company_id')">
                        <option value="">Sélectionner une entreprise...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div class="md:col-span-2">
                    <x-form.input wire:model="title" label="Intitulé du poste *" placeholder="Ex: Technicien en agroécologie" :error="$errors->first('title')" />
                </div>
                <div>
                    <x-form.select wire:model="contract_type" label="Type de contrat *" :error="$errors->first('contract_type')">
                        <option value="">Sélectionner...</option>
                        @foreach($contractTypes as $ct)
                            <option value="{{ $ct->value }}">{{ $ct->label() }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div>
                    <x-form.input wire:model="positions_count" label="Nombre de postes" type="number" placeholder="1" />
                </div>
                <div>
                    <x-form.input wire:model="duration" label="Durée du contrat" placeholder="Ex: 6 mois, 1 an..." />
                </div>
                <div>
                    <x-form.input wire:model="start_date" label="Date de début souhaitée" type="date" />
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#615c47]">description</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Description du poste</h3>
        </div>
        <div class="p-6 space-y-5">
            <x-form.textarea wire:model="mission_description" label="Description des missions *" placeholder="Décrivez les missions, responsabilités et activités du poste..." rows="5" :error="$errors->first('mission_description')" />
            <x-form.textarea wire:model="economic_conditions" label="Conditions économiques" placeholder="Rémunération, avantages, indemnités..." rows="3" />
            <x-form.textarea wire:model="other_requirements" label="Autres exigences" placeholder="Conditions spécifiques, disponibilité requise..." rows="3" />
        </div>
    </div>

    {{-- Compétences requises --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#615c47]">psychology</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Compétences requises *</h3>
        </div>
        <div class="p-6">
            @error('skill_ids') <p class="text-xs text-red-600 mb-3">{{ $message }}</p> @enderror
            <div class="flex flex-wrap gap-2 max-h-52 overflow-y-auto p-3 bg-[#fbf2ed] rounded-xl border border-[#c1c9b6]">
                @foreach($skills as $skill)
                    <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1.5 rounded-lg border transition-all
                        {{ in_array($skill->id, $skill_ids) ? 'border-[#615c47] bg-[#ebe2c8]/30 text-[#615c47]' : 'border-[#c1c9b6] bg-white text-[#41493b] hover:border-[#615c47]/50' }}">
                        <input type="checkbox" wire:model="skill_ids" value="{{ $skill->id }}" class="sr-only" />
                        <span class="text-xs font-semibold">{{ $skill->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Lieux de travail --}}
    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-[#615c47]">location_on</span>
                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Lieux de travail</h3>
            </div>
            <button wire:click="addLocation" type="button"
                    class="flex items-center gap-1 text-xs text-[#615c47] font-semibold hover:underline">
                <span class="material-symbols-outlined text-base">add_location_alt</span>
                Ajouter
            </button>
        </div>
        <div class="p-6 space-y-3">
            @error('locations') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
            @foreach($locations as $i => $loc)
                <div class="bg-[#fbf2ed] rounded-xl p-4 border border-[#c1c9b6]">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-bold text-[#41493b] uppercase">Lieu {{ $i + 1 }}</span>
                        @if(count($locations) > 1)
                            <button wire:click="removeLocation({{ $i }})" type="button" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-form.select wire:model="locations.{{ $i }}.commune_id" label="Commune *" :error="$errors->first('locations.'.$i.'.commune_id')">
                                <option value="">Sélectionner...</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        <x-form.input wire:model="locations.{{ $i }}.address" label="Adresse précise" placeholder="Ex: Zone périurbaine, secteur 30" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex justify-end gap-3 pb-4">
        <a href="{{ route('admin.offers.index') }}" wire:navigate
           class="px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
            Annuler
        </a>
        <button wire:click="save" type="button"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
                class="flex items-center gap-2 px-6 py-2.5 bg-[#615c47] text-white font-bold rounded-xl hover:opacity-90 transition-opacity text-sm shadow-lg shadow-[#615c47]/20">
            <span wire:loading.remove class="material-symbols-outlined text-base">save</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Enregistrer l'offre</span>
            <span wire:loading>Enregistrement...</span>
        </button>
    </div>
</div>