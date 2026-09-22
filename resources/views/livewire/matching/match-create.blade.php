<div class="space-y-6 max-w-3xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.matches.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Nouvelle mise en relation</h2>
            <p class="text-[#41493b] text-sm mt-0.5">Matching manuel — aucun score minimum requis (phase 1).</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#2c6904]">handshake</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Candidat & Offre</h3>
        </div>

        <div class="p-6 space-y-6">

            {{-- Candidat --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Candidat *</label>
                @error('candidate_id') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror

                @if($selectedCandidate)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-[#2c6904]/40 bg-[#aef585]/15">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-sm border border-[#c1c9b6] flex-shrink-0">
                                {{ strtoupper(substr($selectedCandidate->first_name, 0, 1) . substr($selectedCandidate->last_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $selectedCandidate->full_name }}</p>
                                <p class="text-xs text-[#717a69]">{{ $selectedCandidate->reference }} · {{ $selectedCandidate->commune?->name ?? 'Ville non renseignée' }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="clearCandidate"
                                class="text-xs font-semibold text-[#41493b] hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50">
                            Changer
                        </button>
                    </div>
                @else
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
                        <input type="search" wire:model.live.debounce.300ms="candidateSearch"
                               placeholder="Rechercher par nom ou référence..."
                               class="w-full pl-9 pr-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    </div>
                    @if(strlen($candidateSearch) >= 2)
                        <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-[#c1c9b6] bg-white divide-y divide-[#c1c9b6]/40">
                            @forelse($candidates as $c)
                                <button type="button" wire:click="selectCandidate('{{ $c->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-[#fbf2ed] transition-colors">
                                    <p class="text-sm font-semibold text-[#1e1b18]">{{ $c->full_name }}</p>
                                    <p class="text-xs text-[#717a69]">{{ $c->reference }} · {{ $c->commune?->name ?? '-' }}</p>
                                </button>
                            @empty
                                <p class="px-3 py-3 text-xs text-[#717a69]">Aucun candidat trouvé.</p>
                            @endforelse
                        </div>
                    @endif
                @endif
            </div>

            {{-- Offre --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Offre *</label>
                @error('offer_id') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror

                @if($selectedOffer)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-[#2c6904]/40 bg-[#aef585]/15">
                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $selectedOffer->title }}</p>
                            <p class="text-xs text-[#717a69]">
                                {{ $selectedOffer->reference }} · {{ $selectedOffer->company->name }}
                                · <span class="{{ $selectedOffer->status->badgeClass() }} text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $selectedOffer->status->label() }}</span>
                            </p>
                        </div>
                        <button type="button" wire:click="clearOffer"
                                class="text-xs font-semibold text-[#41493b] hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 flex-shrink-0">
                            Changer
                        </button>
                    </div>
                @else
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
                        <input type="search" wire:model.live.debounce.300ms="offerSearch"
                               placeholder="Rechercher par titre, référence ou entreprise..."
                               class="w-full pl-9 pr-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    </div>
                    @if(strlen($offerSearch) >= 2)
                        <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-[#c1c9b6] bg-white divide-y divide-[#c1c9b6]/40">
                            @forelse($offers as $o)
                                <button type="button" wire:click="selectOffer('{{ $o->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-[#fbf2ed] transition-colors">
                                    <p class="text-sm font-semibold text-[#1e1b18]">{{ $o->title }}</p>
                                    <p class="text-xs text-[#717a69]">{{ $o->reference }} · {{ $o->company->name }} · {{ $o->status->label() }}</p>
                                </button>
                            @empty
                                <p class="px-3 py-3 text-xs text-[#717a69]">Aucune offre trouvée.</p>
                            @endforelse
                        </div>
                    @endif
                @endif
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Notes (optionnel)</label>
                <textarea wire:model="notes" rows="3"
                          placeholder="Contexte de la proposition, contraintes géographiques, etc."
                          class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs text-[#717a69] bg-[#fbf2ed] rounded-xl px-3 py-2 border border-[#c1c9b6]">
                Un score indicatif sera calculé automatiquement (compétences, géographie…). Il n’empêche pas la création, même si les villes ou compétences diffèrent.
            </p>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-4">
        <a href="{{ route('admin.matches.index') }}" wire:navigate
           class="px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
            Annuler
        </a>
        <button wire:click="save" type="button"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 px-6 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm shadow-lg shadow-[#2c6904]/20">
            <span wire:loading.remove class="material-symbols-outlined text-base">handshake</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Créer la mise en relation</span>
            <span wire:loading>Création...</span>
        </button>
    </div>
</div>
