<div class="space-y-5 max-w-5xl mx-auto">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.matches.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-lg text-green-700" style="font-variation-settings: 'FILL' 1">handshake</span>
        </div>
        <div>
            <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">Mise en relation</h1>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="text-sm text-[#41493b]">{{ $match->candidate->full_name }}</span>
                <span class="text-[#c1c9b6]">↔</span>
                <span class="text-sm text-[#875212] font-semibold">{{ $match->offer->title }}</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $match->status->badgeClass() }}">{{ $match->status->label() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Profils côte à côte --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Candidat --}}
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#aef585]/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">person</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Candidat</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-sm border border-[#c1c9b6]">
                                {{ strtoupper(substr($match->candidate->first_name, 0, 1) . substr($match->candidate->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-[#1e1b18]">{{ $match->candidate->full_name }}</p>
                                <p class="text-xs text-[#41493b]">{{ $match->candidate->age }} ans · {{ $match->candidate->gender->label() }}</p>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#717a69]">Téléphone</span>
                                <span class="font-semibold text-[#1e1b18]">{{ $match->candidate->phone }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#717a69]">Commune</span>
                                <span class="font-semibold text-[#1e1b18]">{{ $match->candidate->commune?->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#717a69]">Niveau</span>
                                <span class="font-semibold text-[#1e1b18]">{{ $match->candidate->education_level?->label() ?? '—' }}</span>
                            </div>
                        </div>
                        @if($match->candidate->skills->isNotEmpty())
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Compétences</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($match->candidate->skills->take(4) as $skill)
                                        <span class="px-2 py-0.5 bg-[#aef585]/20 text-[#2c6904] text-[10px] font-semibold rounded border border-[#2c6904]/20">{{ $skill->name }}</span>
                                    @endforeach
                                    @if($match->candidate->skills->count() > 4)
                                        <span class="px-2 py-0.5 text-[#717a69] text-[10px]">+{{ $match->candidate->skills->count() - 4 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('admin.candidates.show', $match->candidate) }}" wire:navigate
                           class="flex items-center gap-1 text-xs text-[#2c6904] font-semibold hover:underline">
                            Voir la fiche complète <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                {{-- Offre --}}
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#ebe2c8]/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#615c47]">work_outline</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Offre</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="font-bold text-sm text-[#1e1b18]">{{ $match->offer->title }}</p>
                            <p class="text-xs text-[#875212] font-semibold mt-0.5">{{ $match->offer->company->name }}</p>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#717a69]">Type de contrat</span>
                                <span class="font-semibold text-[#1e1b18]">{{ $match->offer->contract_type->label() }}</span>
                            </div>
                            @if($match->offer->positions_count)
                                <div class="flex justify-between">
                                    <span class="text-[#717a69]">Postes</span>
                                    <span class="font-semibold text-[#1e1b18]">{{ $match->offer->positions_count }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-[#717a69]">Statut offre</span>
                                <span class="font-bold px-1.5 py-0.5 rounded text-[10px] {{ $match->offer->status->badgeClass() }}">{{ $match->offer->status->label() }}</span>
                            </div>
                        </div>
                        @if($match->offer->skills->isNotEmpty())
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Compétences requises</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($match->offer->skills->take(4) as $skill)
                                        <span class="px-2 py-0.5 bg-[#ebe2c8]/30 text-[#615c47] text-[10px] font-semibold rounded border border-[#615c47]/20">{{ $skill->name }}</span>
                                    @endforeach
                                    @if($match->offer->skills->count() > 4)
                                        <span class="px-2 py-0.5 text-[#717a69] text-[10px]">+{{ $match->offer->skills->count() - 4 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('admin.offers.show', $match->offer) }}" wire:navigate
                           class="flex items-center gap-1 text-xs text-[#615c47] font-semibold hover:underline">
                            Voir l'offre complète <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Mise à jour du statut --}}
            @can('update', $match)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">update</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Mettre à jour le statut</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Nouveau statut</label>
                                <select wire:model="newStatus"
                                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Notes / Compte-rendu</label>
                            <textarea wire:model="notes" rows="3"
                                      placeholder="Résumé de l'entretien, raison du refus, observations..."
                                      class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all resize-none"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="updateStatus"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-75"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                                <span wire:loading.remove class="material-symbols-outlined text-base">save</span>
                                <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-4">

            {{-- Notes actuelles --}}
            @if($match->notes)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">note</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Notes</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-[#1e1b18] leading-relaxed bg-[#fbf2ed] rounded-xl p-3">{{ $match->notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Méta --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4 space-y-2">
                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Informations</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Créée le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $match->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Mise à jour</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $match->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($match->closed_at)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Clôturée le</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $match->closed_at->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($match->operator)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Opérateur</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $match->operator->full_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
