<div class="space-y-5 max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.index') }}" wire:navigate
               class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div class="w-10 h-10 rounded-xl bg-[#aef585]/30 flex items-center justify-center flex-shrink-0 border border-[#2c6904]/20">
                <span class="material-symbols-outlined text-lg text-[#2c6904]">description</span>
            </div>
            <div>
                <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">
                    Contrat {{ $contract->reference ?? '' }}
                </h1>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="text-sm text-[#41493b]">{{ $contract->candidate->full_name }}</span>
                    <span class="text-[#c1c9b6]">→</span>
                    <span class="text-sm text-[#875212] font-semibold">{{ $contract->company->name }}</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $contract->status->badgeClass() }}">{{ $contract->status->label() }}</span>
                    @if($contract->isExpired())
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700">Échu</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap self-start">
            <button type="button" wire:click="exportPdf"
                    class="flex items-center gap-2 px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                Exporter PDF
            </button>
            @can('update', $contract)
                @if($contract->canRenew())
                    <button type="button" wire:click="openRenewModal"
                            class="flex items-center gap-2 px-4 py-2 bg-[#875212] text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-base">autorenew</span>
                        Renouveler le contrat
                    </button>
                @elseif($contract->isExpired() && $contract->renewals->isNotEmpty())
                    <span class="text-xs font-semibold text-[#717a69] px-3 py-2 bg-[#f5ece7] rounded-xl border border-[#c1c9b6]">
                        Déjà renouvelé (historisé)
                    </span>
                @endif
            @endcan
            @if($contract->match)
                <a href="{{ route('admin.matches.show', $contract->match) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                    <span class="material-symbols-outlined text-base">handshake</span>
                    Matching
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#aef585]/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">person</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Candidat</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <p class="font-bold text-sm text-[#1e1b18]">{{ $contract->candidate->full_name }}</p>
                        <p class="text-xs text-[#717a69]">{{ $contract->candidate->reference }} · {{ $contract->candidate->commune?->name ?? '-' }}</p>
                        <a href="{{ route('admin.candidates.show', $contract->candidate) }}" wire:navigate
                           class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline pt-1">
                            Fiche candidat <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#ebe2c8]/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#875212]">domain</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Entreprise & offre</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <p class="font-bold text-sm text-[#875212]">{{ $contract->company->name }}</p>
                        <p class="text-xs text-[#717a69]">{{ $contract->company->reference }}</p>
                        @if($contract->offer)
                            <p class="text-sm text-[#1e1b18]">{{ $contract->offer->title }}</p>
                            <p class="text-xs text-[#717a69]">{{ $contract->offer->reference }} · {{ $contract->offer->contract_type?->label() }}</p>
                            <a href="{{ route('admin.offers.show', $contract->offer) }}" wire:navigate
                               class="flex items-center gap-1 text-xs text-[#615c47] font-semibold hover:underline pt-1">
                                Voir l'offre <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Période --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">date_range</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Période du contrat</h3>
                    </div>
                    @if($contract->isExpired())
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-red-100 text-red-700">Expiré</span>
                    @endif
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Période</p>
                        <p class="font-semibold text-[#1e1b18] mt-0.5">{{ $contract->periodLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Durée</p>
                        <p class="font-semibold text-[#1e1b18] mt-0.5">{{ $contract->duration_label ?: '—' }}</p>
                    </div>
                </div>
                @can('update', $contract)
                    @if($contract->canRenew())
                        <div class="px-5 pb-5">
                            <button type="button" wire:click="openRenewModal"
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-[#875212] text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity">
                                <span class="material-symbols-outlined text-base">autorenew</span>
                                Renouveler (contrat expiré)
                            </button>
                            <p class="mt-2 text-[11px] text-[#717a69]">L’ancien contrat restera historisé sur la fiche candidat et sur l’offre.</p>
                        </div>
                    @endif
                @endcan
            </div>

            @can('update', $contract)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">edit_note</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Modifier le contrat</h3>
                    </div>
                    <div class="p-5 space-y-4"
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Statut</label>
                                <select wire:model.live="newStatus"
                                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Date de signature <span class="font-normal text-[#717a69]">(auto si Actif)</span></label>
                                <input type="datetime-local" wire:model="signed_at"
                                       class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Début</label>
                                <input type="date" wire:model.live="starts_at"
                                       x-on:change="syncEnd()"
                                       class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Fin <span class="font-normal text-[#717a69]">(auto)</span></label>
                                <input type="date" wire:model.live="ends_at"
                                       wire:key="show-ends-{{ $ends_at }}"
                                       value="{{ $ends_at }}"
                                       class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                                @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Durée (libellé)</label>
                            <input type="text" wire:model.live.debounce.300ms="duration_label"
                                   x-on:input.debounce.300ms="syncEnd()"
                                   x-on:blur="syncEnd()"
                                   placeholder="Ex. 6 mois"
                                   class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                            <p class="mt-1 text-[11px] text-[#717a69]">La date de fin est recalculée automatiquement à partir du début + durée (ex. « 6 mois », « 12 mois renouvelables »).</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Salaire négocié</label>
                            <input type="text" wire:model="negotiated_salary"
                                   placeholder="Ex. 150 000 FCFA / mois"
                                   class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Notes</label>
                            <textarea wire:model="notes" rows="3"
                                      class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] resize-none"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="save"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                                <span class="material-symbols-outlined text-base">save</span>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            @endcan

            @if($contract->clauses_snapshot)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#875212]">gavel</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Clauses / conditions (snapshot offre)</h3>
                    </div>
                    <div class="p-5">
                        <pre class="text-xs text-[#1e1b18] whitespace-pre-wrap font-sans leading-relaxed bg-[#fbfdfb] rounded-xl p-4 border border-[#c1c9b6]/50">{{ $contract->clauses_snapshot }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4 space-y-2">
                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Informations</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="text-[#717a69]">Référence</span>
                        <span class="font-mono font-semibold text-[#2c6904]">{{ $contract->reference ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Créé le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $contract->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($contract->signed_at)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Signé le</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $contract->signed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($contract->createdBy)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Créé par</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $contract->createdBy->full_name }}</span>
                        </div>
                    @endif
                    @if($contract->negotiated_salary)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Salaire</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $contract->negotiated_salary }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Historique renouvellements --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#c1c9b6] bg-[#fbf2ed]">
                    <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Historique / suivi</p>
                </div>
                <div class="divide-y divide-[#c1c9b6]/40">
                    @forelse($history as $item)
                        <a href="{{ route('admin.contracts.show', $item) }}" wire:navigate
                           class="block px-4 py-3 hover:bg-[#fbf2ed]/60 transition-colors {{ $item->id === $contract->id ? 'bg-[#aef585]/10' : '' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-[11px] font-bold text-[#2c6904]">{{ $item->reference ?? '—' }}</span>
                                <div class="flex items-center gap-1">
                                    @if($item->isExpired())
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">Expiré</span>
                                    @endif
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $item->status->badgeClass() }}">{{ $item->status->label() }}</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-[#717a69] mt-1">{{ $item->periodLabel() }}</p>
                        </a>
                    @empty
                        <p class="px-4 py-3 text-xs text-[#717a69]">Aucun historique de renouvellement.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($showRenewModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" wire:click.self="closeRenewModal">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2 sticky top-0 bg-[#fbf2ed]">
                    <span class="material-symbols-outlined text-[#875212]">autorenew</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Renouveler le contrat expiré</h3>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-[#41493b]">
                        L’ancien contrat <span class="font-mono font-semibold text-[#2c6904]">{{ $contract->reference }}</span>
                        sera clos et <strong>historisé</strong> sur la fiche candidat et sur l’offre.
                    </p>

                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors
                            {{ $renewMode === 'same' ? 'border-[#875212] bg-[#ffdcbd]/20' : 'border-[#c1c9b6] hover:bg-[#fbf2ed]' }}">
                            <input type="radio" wire:model.live="renewMode" value="same" class="mt-0.5 text-[#875212] focus:ring-[#875212]/30" />
                            <span>
                                <span class="block text-sm font-semibold text-[#1e1b18]">Mêmes clauses</span>
                                <span class="block text-xs text-[#717a69] mt-0.5">Reprend durée, salaire, notes et conditions de l’ancien contrat. Nouvelle période à partir de demain après la fin.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors
                            {{ $renewMode === 'new' ? 'border-[#875212] bg-[#ffdcbd]/20' : 'border-[#c1c9b6] hover:bg-[#fbf2ed]' }}">
                            <input type="radio" wire:model.live="renewMode" value="new" class="mt-0.5 text-[#875212] focus:ring-[#875212]/30" />
                            <span>
                                <span class="block text-sm font-semibold text-[#1e1b18]">Nouvelles clauses</span>
                                <span class="block text-xs text-[#717a69] mt-0.5">Modifier durée, période, salaire et conditions avant de créer le nouveau contrat.</span>
                            </span>
                        </label>
                    </div>

                    @if($renewMode === 'new')
                        <div class="space-y-3 rounded-xl border border-[#c1c9b6] bg-[#fbfdfb] p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Début</label>
                                    <input type="date" wire:model.live="renew_starts_at"
                                           class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Fin</label>
                                    <input type="date" wire:model="renew_ends_at"
                                           class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Durée</label>
                                <input type="text" wire:model.live.debounce.300ms="renew_duration_label"
                                       class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm"
                                       placeholder="Ex. 6 mois" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Salaire négocié</label>
                                <input type="text" wire:model="renew_negotiated_salary"
                                       class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Notes</label>
                                <textarea wire:model="renew_notes" rows="2"
                                          class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1">Clauses / conditions</label>
                                <textarea wire:model="renew_clauses" rows="4"
                                          class="w-full px-3 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-xs resize-none font-mono"></textarea>
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-[#41493b] bg-[#f5f9f2] rounded-xl p-3 border border-[#c1c9b6]/50 space-y-1">
                            <p><span class="text-[#717a69]">Nouvelle période :</span> {{ $renew_starts_at ?: '—' }} → {{ $renew_ends_at ?: '—' }}</p>
                            <p><span class="text-[#717a69]">Durée :</span> {{ $renew_duration_label ?: '—' }}</p>
                        </div>
                    @endif
                </div>
                <div class="px-5 py-4 border-t border-[#c1c9b6] bg-[#fbfdfb] flex justify-end gap-2 sticky bottom-0">
                    <button type="button" wire:click="closeRenewModal"
                            class="px-4 py-2 text-sm font-semibold text-[#41493b] rounded-xl border border-[#c1c9b6] hover:bg-[#f5ece7]">
                        Annuler
                    </button>
                    <button type="button" wire:click="confirmRenew"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#875212] rounded-xl hover:opacity-90">
                        Confirmer le renouvellement
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
