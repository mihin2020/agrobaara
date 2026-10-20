<div class="space-y-8">
    <x-page-header
        :title="$company?->name ?? 'Mon organisation'"
        :subtitle="$isCenter ? 'Espace centre de formation' : 'Espace entreprise'"
    />

    <x-section-card title="Nouvelle offre" icon="add_business">
        <p class="text-xs text-[#717a69] mb-3">Publication après validation opérateur / admin.</p>
        <form wire:submit="createOffer" class="space-y-3">
            <input type="text" wire:model="offer_title" placeholder="Intitulé du poste" class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]" />
            @error('offer_title') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <select wire:model="contract_type" class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]">
                <option value="salarie">Emploi salarié</option>
                <option value="saisonnier">Saisonnier</option>
                <option value="ponctuel">Mission ponctuelle</option>
                <option value="apprentissage">Apprentissage</option>
                <option value="entrepreneuriat">Entrepreneuriat</option>
                <option value="autre">Autre</option>
            </select>
            <textarea wire:model="offer_mission" rows="4" placeholder="Mission / description" class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]"></textarea>
            @error('offer_mission') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <button type="submit" class="px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl">Soumettre</button>
        </form>
    </x-section-card>

    <x-section-card title="Historique des offres" icon="work_history">
        @forelse($offers as $o)
            <div class="flex justify-between text-sm py-2 border-b border-[#c1c9b6]/40">
                <span>{{ $o->title }}</span>
                <x-status-badge :status="$o->status" />
            </div>
        @empty
            <x-empty-state icon="work_outline" title="Aucune offre." class="!py-6" />
        @endforelse
    </x-section-card>

    @feature('phase2_master')
    <x-section-card title="Candidats suggérés (lecture)" icon="person_search">
        <x-slot:actions>
            <button type="button" wire:click="toggleSuggestions"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#c1c9b6] hover:bg-[#f5ece7]">
                {{ $showSuggestions ? 'Masquer' : 'Charger' }}
            </button>
        </x-slot:actions>
        <p class="text-xs text-[#717a69] mb-3">La validation définitive et le contrat restent côté opérateurs.</p>
        @if(! $showSuggestions)
            <p class="text-sm text-[#717a69]">Cliquez sur « Charger » pour afficher les suggestions (top 5).</p>
        @else
            @forelse($suggestions as $block)
                <div class="mb-4">
                    <p class="text-sm font-medium mb-1">{{ $block['offer']->title }}</p>
                    @foreach($block['rows'] as $row)
                        <p class="text-xs text-[#41493b] pl-2">{{ $row['candidate']->full_name }} — {{ number_format($row['score'], 0) }}%</p>
                    @endforeach
                </div>
            @empty
                <x-empty-state icon="search_off" title="Aucune suggestion (offres publiées requises)." class="!py-6" />
            @endforelse
        @endif
    </x-section-card>
    @endfeature

    <x-section-card title="Contrats" icon="description">
        @forelse($contracts as $c)
            <p class="text-sm py-1">{{ $c->reference }} — {{ $c->candidate?->full_name }} <x-status-badge :status="$c->status" class="ml-1" /></p>
        @empty
            <x-empty-state icon="description" title="Aucun contrat." class="!py-6" />
        @endforelse
    </x-section-card>

    @if($isCenter)
        <x-section-card title="Candidats formés affiliés" icon="school">
            @forelse($trained as $cand)
                <p class="text-sm py-1">{{ $cand->full_name }} · {{ $cand->reference }}@if($cand->commune) · {{ $cand->commune->name }}@endif</p>
            @empty
                <x-empty-state icon="group" title="Aucun candidat affilié pour l’instant." class="!py-6" />
            @endforelse
        </x-section-card>
    @endif
</div>
