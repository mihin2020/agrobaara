<div class="space-y-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.contracts.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Nouveau contrat</h2>
            <p class="text-[#41493b] text-sm mt-0.5">
                @if($fromMatch)
                    Création à partir d'une mise en relation acceptée.
                @else
                    Sélectionnez le candidat, l'entreprise et l'offre concernés.
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#2c6904]">description</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Parties & offre</h3>
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
                                <p class="text-xs text-[#717a69]">{{ $selectedCandidate->reference }} · {{ $selectedCandidate->commune?->name ?? '-' }}</p>
                            </div>
                        </div>
                        @unless($fromMatch)
                            <button type="button" wire:click="clearCandidate"
                                    class="text-xs font-semibold text-[#41493b] hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50">
                                Changer
                            </button>
                        @endunless
                    </div>
                @elseif(!$fromMatch)
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
                                    <p class="text-xs text-[#717a69]">{{ $c->reference }}</p>
                                </button>
                            @empty
                                <p class="px-3 py-3 text-xs text-[#717a69]">Aucun candidat trouvé.</p>
                            @endforelse
                        </div>
                    @endif
                @endif
            </div>

            {{-- Entreprise --}}
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-2">Entreprise *</label>
                @error('company_id') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror

                @if($selectedCompany)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-[#875212]/30 bg-[#ffdcbd]/15">
                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $selectedCompany->name }}</p>
                            <p class="text-xs text-[#717a69]">{{ $selectedCompany->reference }}</p>
                        </div>
                        @unless($fromMatch)
                            <button type="button" wire:click="clearCompany"
                                    class="text-xs font-semibold text-[#41493b] hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 flex-shrink-0">
                                Changer
                            </button>
                        @endunless
                    </div>
                @elseif(!$fromMatch)
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
                        <input type="search" wire:model.live.debounce.300ms="companySearch"
                               placeholder="Rechercher une entreprise..."
                               class="w-full pl-9 pr-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    </div>
                    @if(strlen($companySearch) >= 2)
                        <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-[#c1c9b6] bg-white divide-y divide-[#c1c9b6]/40">
                            @forelse($companies as $co)
                                <button type="button" wire:click="selectCompany('{{ $co->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-[#fbf2ed] transition-colors">
                                    <p class="text-sm font-semibold text-[#1e1b18]">{{ $co->name }}</p>
                                    <p class="text-xs text-[#717a69]">{{ $co->reference }}</p>
                                </button>
                            @empty
                                <p class="px-3 py-3 text-xs text-[#717a69]">Aucune entreprise trouvée.</p>
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
                            <p class="text-xs text-[#717a69]">{{ $selectedOffer->reference }} · {{ $selectedOffer->company->name }}</p>
                        </div>
                        @unless($fromMatch)
                            <button type="button" wire:click="clearOffer"
                                    class="text-xs font-semibold text-[#41493b] hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 flex-shrink-0">
                                Changer
                            </button>
                        @endunless
                    </div>
                @elseif(!$fromMatch)
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
                        <input type="search" wire:model.live.debounce.300ms="offerSearch"
                               placeholder="Rechercher une offre..."
                               class="w-full pl-9 pr-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    </div>
                    @if(strlen($offerSearch) >= 2)
                        <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-[#c1c9b6] bg-white divide-y divide-[#c1c9b6]/40">
                            @forelse($offers as $o)
                                <button type="button" wire:click="selectOffer('{{ $o->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-[#fbf2ed] transition-colors">
                                    <p class="text-sm font-semibold text-[#1e1b18]">{{ $o->title }}</p>
                                    <p class="text-xs text-[#717a69]">{{ $o->reference }} · {{ $o->company->name }}</p>
                                </button>
                            @empty
                                <p class="px-3 py-3 text-xs text-[#717a69]">Aucune offre trouvée.</p>
                            @endforelse
                        </div>
                    @endif
                @endif
            </div>

            <div class="rounded-xl border border-[#c1c9b6] bg-[#fbfdfb] p-4 space-y-4"
                 x-data="{
                    parseEnd(start, duration) {
                        if (!start || !duration) return '';
                        const d = String(duration).toLowerCase().trim();
                        const base = new Date(start + 'T00:00:00');
                        if (isNaN(base)) return '';
                        let m;
                        const addMonths = (n) => { const x = new Date(base); x.setMonth(x.getMonth() + n); return x; };
                        const addDays = (n) => { const x = new Date(base); x.setDate(x.getDate() + n); return x; };
                                const fmt = (x) => {
                                    const y = x.getFullYear();
                                    const m = String(x.getMonth() + 1).padStart(2, '0');
                                    const day = String(x.getDate()).padStart(2, '0');
                                    return `${y}-${m}-${day}`;
                                };
                        if ((m = d.match(/(\d+)\s*mois/))) return fmt(addMonths(parseInt(m[1], 10)));
                        if ((m = d.match(/(\d+)\s*ans?/))) return fmt(addMonths(parseInt(m[1], 10) * 12));
                        if ((m = d.match(/(\d+)\s*semaines?/))) return fmt(addDays(parseInt(m[1], 10) * 7));
                        if ((m = d.match(/(\d+)\s*jours?/))) return fmt(addDays(parseInt(m[1], 10)));
                        if ((m = d.match(/^(\d+)\s*$/))) return fmt(addMonths(parseInt(m[1], 10)));
                        return '';
                    },
                    syncEnd() {
                        const end = this.parseEnd($wire.starts_at, $wire.duration_label);
                        if (end) { $wire.set('ends_at', end); }
                    }
                 }">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#2c6904]">date_range</span>
                    <p class="text-sm font-semibold text-[#1e1b18]">Période (clauses de l’offre)</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Début</label>
                        <input type="date" wire:model.live="starts_at"
                               x-on:change="syncEnd()"
                               class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                        @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Fin <span class="font-normal text-[#717a69]">(auto)</span></label>
                        <input type="date" wire:model.live="ends_at"
                               wire:key="create-ends-{{ $ends_at }}"
                               value="{{ $ends_at }}"
                               class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                        @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Durée (libellé offre)</label>
                    <input type="text" wire:model.live.debounce.300ms="duration_label"
                           x-on:input.debounce.300ms="syncEnd()"
                           x-on:blur="syncEnd()"
                           placeholder="Ex. 6 mois — calcule automatiquement la fin"
                           class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    <p class="mt-1 text-[11px] text-[#717a69]">Ex. « 6 mois », « 1 an », « 3 semaines » → date de fin calculée depuis le début.</p>
                </div>
                <p class="text-[11px] text-[#717a69] bg-[#fbf2ed] rounded-lg px-3 py-2 border border-[#c1c9b6]/50">
                    Le bouton <strong>Renouveler</strong> apparaîtra automatiquement sur la fiche une fois le contrat <strong>expiré</strong>. L’ancien restera historisé (candidat & offre).
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Salaire négocié (optionnel)</label>
                <input type="text" wire:model="negotiated_salary"
                       placeholder="Ex. 150 000 FCFA / mois"
                       class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                @error('negotiated_salary') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Notes (optionnel)</label>
                <textarea wire:model="notes" rows="3"
                          placeholder="Conditions particulières, date de prise de poste..."
                          class="w-full px-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @error('match_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

            <p class="text-xs text-[#717a69] bg-[#fbf2ed] rounded-xl px-3 py-2 border border-[#c1c9b6]">
                Le contrat sera créé avec le statut « Brouillon ». Vous pourrez le finaliser depuis la fiche contrat.
            </p>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-4">
        <a href="{{ route('admin.contracts.index') }}" wire:navigate
           class="px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
            Annuler
        </a>
        <button wire:click="save" type="button"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 px-6 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm shadow-lg shadow-[#2c6904]/20">
            <span wire:loading.remove class="material-symbols-outlined text-base">description</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Créer le contrat</span>
            <span wire:loading>Création...</span>
        </button>
    </div>
</div>
