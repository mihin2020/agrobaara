<div class="space-y-8">
    <x-page-header
        :title="'Bonjour ' . auth()->user()->first_name"
        subtitle="Votre espace candidat"
    />

    <x-section-card title="Profil" icon="person">
        @if($candidate)
            <p class="text-sm">{{ $candidate->full_name }} · {{ $candidate->reference }}</p>
            <p class="text-xs text-[#717a69]">{{ $candidate->phone }} · {{ $candidate->email }}</p>
            @if($candidate->formattedSalaryExpectation())
                <p class="text-xs mt-2">Prétentions : {{ $candidate->formattedSalaryExpectation() }}</p>
            @endif
        @else
            <p class="text-sm text-amber-700">Fiche candidat en cours de validation.</p>
        @endif
    </x-section-card>

    <x-section-card title="Offres pertinentes" icon="work">
        <x-slot:actions>
            <button type="button" wire:click="toggleSuggestedOffers"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#c1c9b6] hover:bg-[#f5ece7]">
                {{ $showSuggestedOffers ? 'Masquer' : 'Charger' }}
            </button>
        </x-slot:actions>
        @if(! $showSuggestedOffers)
            <p class="text-sm text-[#717a69]">Cliquez sur « Charger » pour calculer les offres scorées.</p>
        @else
            <div class="space-y-2">
                @forelse($offers as $row)
                    <div class="flex justify-between gap-3 text-sm border-b border-[#c1c9b6]/40 py-2">
                        <div>
                            <p class="font-medium">{{ $row['offer']->title }}</p>
                            <p class="text-xs text-[#717a69]">{{ $row['offer']->company?->name }}</p>
                        </div>
                        <span class="text-xs font-bold text-[#2c6904]">{{ number_format($row['score'], 0) }}%</span>
                    </div>
                @empty
                    <x-empty-state icon="search_off" title="Aucune offre scorée pour le moment." class="!py-6" />
                @endforelse
            </div>
        @endif
    </x-section-card>

    <x-section-card title="Mes mises en relation" icon="handshake">
        @forelse($matches as $m)
            <p class="text-sm py-1">{{ $m->offer?->title }} — <span class="text-xs">{{ $m->status->label() }}</span></p>
        @empty
            <x-empty-state icon="handshake" title="Aucun matching." class="!py-6" />
        @endforelse
    </x-section-card>

    <x-section-card title="Mes contrats" icon="description">
        @forelse($contracts as $c)
            <p class="text-sm py-1">{{ $c->reference }} — {{ $c->company?->name }} <x-status-badge :status="$c->status" class="ml-1" /></p>
        @empty
            <x-empty-state icon="description" title="Aucun contrat." class="!py-6" />
        @endforelse
    </x-section-card>

    <x-section-card title="Centre de formation" icon="school">
        <form wire:submit="saveAffiliation" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <select wire:model="training_center_id" class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]">
                    <option value="">— Aucun —</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}">{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl">Enregistrer</button>
        </form>
    </x-section-card>

    @feature('testimonials')
    <x-section-card title="Témoignage" icon="format_quote">
        <form wire:submit="submitTestimonial" class="space-y-3">
            <input type="text" wire:model="testimonial_role" placeholder="Votre rôle / contexte" class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]" />
            <textarea wire:model="testimonial_body" rows="4" placeholder="Votre témoignage..." class="w-full px-3 py-2 border border-[#c1c9b6] rounded-xl text-sm bg-[#fbf2ed]"></textarea>
            @error('testimonial_body') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <button type="submit" class="px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl">Soumettre</button>
        </form>
    </x-section-card>
    @endfeature
</div>
