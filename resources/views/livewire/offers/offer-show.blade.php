<div class="space-y-5 max-w-5xl mx-auto">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.offers.index') }}" wire:navigate
               class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors flex-shrink-0 mt-1">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">{{ $offer->title }}</h1>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $offer->status->badgeClass() }}">{{ $offer->status->label() }}</span>
                    @if($offer->isPeriodic())
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-amber-100 text-amber-800">Périodique</span>
                    @endif
                    @if($offer->isExpired())
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-red-100 text-red-700">Expirée</span>
                    @endif
                    @if($offer->renewed_from_offer_id)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#aef585]/40 text-[#2c6904]">Issu d’une relance</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="font-mono text-xs font-semibold text-[#615c47] bg-[#ebe2c8]/30 px-2 py-0.5 rounded-lg">{{ $offer->reference }}</span>
                    <a href="{{ route('admin.companies.show', $offer->company) }}" wire:navigate
                       class="text-xs font-semibold text-[#875212] hover:underline">{{ $offer->company->name }}</a>
                    <span class="text-xs text-[#717a69]">· {{ $offer->contract_type->label() }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap sm:flex-nowrap">
            @can('create', App\Models\CandidateMatch::class)
                <a href="{{ route('admin.matches.create', ['offer_id' => $offer->id]) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2 border border-[#2c6904] text-[#2c6904] font-semibold text-sm rounded-xl hover:bg-[#aef585]/20 transition-colors">
                    <span class="material-symbols-outlined text-base">handshake</span>
                    Mettre en relation
                </a>
            @endcan
            @can('update', $offer)
                <a href="{{ route('admin.offers.edit', $offer) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Modifier
                </a>
            @endcan
            @can('publish', $offer)
                @if($offer->isDraft())
                    <button wire:click="publish"
                            class="flex items-center gap-2 px-4 py-2 bg-green-700 text-white font-semibold text-sm rounded-xl hover:bg-green-800 transition-colors">
                        <span class="material-symbols-outlined text-base">publish</span>
                        Publier
                    </button>
                @endif
            @endcan
            @can('archive', $offer)
                @if($offer->isPublished())
                    <button wire:click="archive"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white font-semibold text-sm rounded-xl hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-base">archive</span>
                        Archiver
                    </button>
                @endif
            @endcan
            @can('create', App\Models\JobOffer::class)
                @if($offer->canRelaunch())
                    <button type="button" wire:click="openRelaunchModal"
                            class="flex items-center gap-2 px-4 py-2 bg-[#875212] text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity">
                        <span class="material-symbols-outlined text-base">autorenew</span>
                        Relancer / reconduire
                    </button>
                @elseif($offer->isPeriodic() && $offer->renewals_count > 0)
                    <span class="text-xs font-semibold text-[#717a69] px-3 py-2 bg-[#f5ece7] rounded-xl border border-[#c1c9b6]">
                        Déjà relancée (historisée)
                    </span>
                @elseif($offer->isPeriodic() && ! $offer->hasPeriod())
                    <span class="text-xs font-semibold text-amber-800 px-3 py-2 bg-amber-50 rounded-xl border border-amber-200">
                        Période manquante (début / fin)
                    </span>
                @elseif($offer->isPeriodic() && ! $offer->isArchived() && ! $offer->isExpired())
                    <span class="text-xs font-semibold text-[#717a69] px-3 py-2 bg-[#f5ece7] rounded-xl border border-[#c1c9b6]">
                        Relance après expiration ou archivage
                    </span>
                @elseif(($offer->isPublished() || $offer->isArchived()) && ! $offer->isPeriodic())
                    <button wire:click="renew" wire:confirm="Créer une nouvelle offre brouillon à partir de celle-ci ?"
                            class="flex items-center gap-2 px-4 py-2 border border-[#875212] text-[#875212] font-semibold text-sm rounded-xl hover:bg-[#ffdcbd]/30 transition-colors">
                        <span class="material-symbols-outlined text-base">content_copy</span>
                        Cloner
                    </button>
                @endif
            @endcan
            <button wire:click="toggleSuggestedCandidates"
                    class="flex items-center gap-2 px-4 py-2 bg-[#2c6904] text-white font-semibold text-sm rounded-xl hover:bg-[#448322] transition-colors">
                <span class="material-symbols-outlined text-base">psychology</span>
                {{ $showSuggestedCandidates ? 'Masquer' : 'Matching candidats' }}
            </button>
        </div>
    </div>

    {{-- Matching auto top-5 (Phase 2 master) --}}
    @if($autoMatchOn)
        <div class="bg-white rounded-2xl p-4 border-2 border-[#2c6904]/40 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-sora font-bold text-sm text-[#2c6904] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">auto_awesome</span>
                    Suggestions auto — top 5
                </h3>
                <span class="text-[10px] uppercase font-bold text-[#717a69]">Phase 2</span>
            </div>
            <p class="text-xs text-[#717a69]">Valider un profil crée le matching et ouvre la saisie du contrat (préremplie).</p>
            @forelse($autoTop5 as $i => $item)
                @php $candidate = $item['candidate']; @endphp
                <div class="bg-[#fbf2ed] rounded-xl border border-[#c1c9b6] p-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs text-[#717a69]">#{{ $i + 1 }}</p>
                        <p class="font-semibold text-sm truncate">{{ $candidate->full_name }}</p>
                        <p class="text-xs text-[#41493b]">Score {{ number_format($item['score'], 0) }}%</p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        @can('create', App\Models\CandidateMatch::class)
                            <button type="button" wire:click="validateAutoSuggestion('{{ $candidate->id }}')"
                                    class="px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg">
                                Valider → contrat
                            </button>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#717a69] text-center py-2">Aucune suggestion automatique.</p>
            @endforelse
        </div>
    @endif

    {{-- Candidats suggérés (manuel) --}}
    @if($showSuggestedCandidates)
        <div class="bg-[#f5ece7] rounded-2xl p-4 border border-[#c1c9b6] space-y-3" wire:loading.remove wire:target="toggleSuggestedCandidates">
            <h3 class="font-sora font-bold text-sm text-[#2c6904] flex items-center gap-2">
                <span class="material-symbols-outlined text-base">psychology</span>
                Candidats suggérés par le matching
            </h3>
            @forelse($suggestedCandidates as $item)
                @php $candidate = $item['candidate']; @endphp
                <div class="bg-white rounded-xl border border-[#c1c9b6] p-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-sm border border-[#c1c9b6] flex-shrink-0">
                            {{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $candidate->full_name }}</p>
                            <p class="text-xs text-[#41493b]">{{ $candidate->commune?->name ?? '-' }} · {{ $candidate->educationLevel?->name ?? ($candidate->education_level ? ucfirst($candidate->education_level) : '-') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-1 rounded-lg">{{ number_format($item['score'], 0) }}%</span>
                        @can('create', App\Models\CandidateMatch::class)
                            <button type="button" wire:click="proposeMatch('{{ $candidate->id }}')"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 bg-[#615c47] text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity">
                                Proposer
                            </button>
                        @endcan
                        <a href="{{ route('admin.candidates.show', $candidate) }}" wire:navigate
                           class="px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg hover:bg-[#448322] transition-colors">
                            Voir
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#717a69] text-center py-3">Aucun candidat correspondant trouvé.</p>
            @endforelse
        </div>
        <div wire:loading wire:target="toggleSuggestedCandidates" class="text-center py-3 text-sm text-[#717a69]">
            <span class="material-symbols-outlined animate-spin text-lg align-middle">progress_activity</span> Analyse en cours...
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Détails --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#615c47]">info</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Détails du poste</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Type de contrat</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $offer->contract_type->label() }}</p>
                        </div>
                        @if($offer->positions_count)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Postes disponibles</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $offer->positions_count }}</p>
                            </div>
                        @endif
                        @if($offer->duration)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Durée</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">
                                    {{ $offer->duration }}
                                    @if($offer->isPeriodic())
                                        <span class="ml-1 text-[10px] font-bold text-amber-800">(périodique)</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if($offer->renewedFrom)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Relancée depuis</p>
                                <a href="{{ route('admin.offers.show', $offer->renewedFrom) }}" wire:navigate
                                   class="text-sm font-semibold text-[#875212] hover:underline mt-0.5 block">
                                    {{ $offer->renewedFrom->reference }} — historisée
                                </a>
                            </div>
                        @endif
                        @if($offer->hasPeriod() || $offer->start_date)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Période</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $offer->periodLabel() }}</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Description des missions</p>
                        <div class="text-sm text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-4 leading-relaxed whitespace-pre-line">{{ $offer->mission_description }}</div>
                    </div>
                    @if($offer->economic_conditions)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Conditions économiques</p>
                            <p class="text-sm text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-4 leading-relaxed">{{ $offer->economic_conditions }}</p>
                        </div>
                    @endif
                    @if($offer->salary_range)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Fourchette salariale</p>
                            <p class="text-sm font-semibold text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-4">{{ $offer->salary_range }}</p>
                        </div>
                    @endif
                    @if($offer->other_requirements)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Autres exigences</p>
                            <p class="text-sm text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-4 leading-relaxed">{{ $offer->other_requirements }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Compétences --}}
            @if($offer->skills->isNotEmpty())
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#615c47]">psychology</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Compétences requises</h3>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($offer->skills as $skill)
                                <span class="px-2.5 py-1 bg-[#ebe2c8]/30 text-[#615c47] text-xs font-semibold rounded-lg border border-[#615c47]/20">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Lieux --}}
            @if($offer->locations && count($offer->locations) > 0)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#615c47]">location_on</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Lieux de travail</h3>
                    </div>
                    <div class="p-5 space-y-2">
                        @foreach($offer->locations as $loc)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-base text-[#717a69]">place</span>
                                <span class="text-[#1e1b18]">{{ $loc['address'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Matching en cours --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#615c47]">handshake</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Mises en relation ({{ $offer->matches->count() }})</h3>
                    </div>
                    <a href="{{ route('admin.matches.index') }}" wire:navigate
                       class="text-xs text-[#2c6904] font-semibold hover:underline">Voir tout</a>
                </div>
                <div class="divide-y divide-[#c1c9b6]/40">
                    @forelse($offer->matches->take(6) as $match)
                        <div class="p-3 flex items-center justify-between gap-3 hover:bg-[#fbf2ed]/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($match->candidate->first_name, 0, 1) . substr($match->candidate->last_name, 0, 1)) }}
                                </div>
                                <p class="text-sm font-semibold text-[#1e1b18] truncate">{{ $match->candidate->full_name }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $match->status->badgeClass() }}">{{ $match->status->label() }}</span>
                                <a href="{{ route('admin.matches.show', $match) }}" wire:navigate
                                   class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg">
                                    <span class="material-symbols-outlined text-base">chevron_right</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-[#717a69]">Aucune mise en relation pour cette offre.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-4">

            {{-- Entreprise --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#875212]">domain</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Entreprise</h3>
                </div>
                <div class="p-4">
                    <a href="{{ route('admin.companies.show', $offer->company) }}" wire:navigate
                       class="font-semibold text-sm text-[#875212] hover:underline">{{ $offer->company->name }}</a>
                    <p class="text-xs text-[#41493b] mt-0.5">{{ $offer->company->status->label() }}</p>
                    @if($offer->company->phone)
                        <p class="text-xs text-[#717a69] mt-1">{{ $offer->company->phone }}</p>
                    @endif
                </div>
            </div>

            {{-- Historique contrats liés à l’offre --}}
            @can('viewAny', App\Models\Contract::class)
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">description</span>
                            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Contrats (historique)</h3>
                        </div>
                        <a href="{{ route('admin.contracts.index', ['offer_id' => $offer->id]) }}" wire:navigate
                           class="text-[11px] font-semibold text-[#2c6904] hover:underline">
                            Voir tout
                        </a>
                    </div>
                    <div class="divide-y divide-[#c1c9b6]/40">
                        @forelse($offer->contracts->sortByDesc('created_at')->take(8) as $ctr)
                            <a href="{{ route('admin.contracts.show', $ctr) }}" wire:navigate
                               class="block p-3 hover:bg-[#fbf2ed]/50 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-mono text-[10px] font-bold text-[#2c6904]">{{ $ctr->reference ?? '—' }}</p>
                                        <p class="text-xs font-semibold text-[#1e1b18] truncate">{{ $ctr->candidate?->full_name ?? '—' }}</p>
                                        <p class="text-[11px] text-[#41493b] mt-0.5">{{ $ctr->periodLabel() }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ctr->status->badgeClass() }}">{{ $ctr->status->label() }}</span>
                                        @if($ctr->isExpired())
                                            <span class="text-[9px] font-bold text-red-700">Expiré / historisé</span>
                                        @endif
                                        @if($ctr->renewed_from_contract_id)
                                            <span class="text-[9px] font-bold text-[#2c6904]">Renouvellement</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-[#717a69] text-xs">Aucun contrat pour cette offre.</div>
                        @endforelse
                    </div>
                </div>
            @endcan

            {{-- Méta --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4 space-y-2">
                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Informations système</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Créée le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $offer->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($offer->published_at)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Publiée le</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $offer->published_at->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($offer->createdBy)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Créée par</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $offer->createdBy->full_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($showRelaunchModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" wire:click.self="closeRelaunchModal">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#875212]">autorenew</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Relancer l’offre périodique</h3>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-[#41493b]">
                        L’offre <span class="font-mono font-semibold text-[#2c6904]">{{ $offer->reference }}</span>
                        sera <strong>archivée et historisée</strong> avec les candidats / contrats associés.
                        Une nouvelle offre brouillon sera créée.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Date de début de la nouvelle campagne</label>
                        <input type="date" wire:model="relaunch_start_date"
                               class="w-full px-3 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors
                            {{ $relaunchMatchMode === 'fresh' ? 'border-[#875212] bg-[#ffdcbd]/20' : 'border-[#c1c9b6] hover:bg-[#fbf2ed]' }}">
                            <input type="radio" wire:model.live="relaunchMatchMode" value="fresh" class="mt-0.5 text-[#875212]" />
                            <span>
                                <span class="block text-sm font-semibold text-[#1e1b18]">Nouveau matching</span>
                                <span class="block text-xs text-[#717a69] mt-0.5">Aucun match repris. Vous créerez de nouvelles mises en relation si nécessaire.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors
                            {{ $relaunchMatchMode === 'reconduce' ? 'border-[#875212] bg-[#ffdcbd]/20' : 'border-[#c1c9b6] hover:bg-[#fbf2ed]' }}">
                            <input type="radio" wire:model.live="relaunchMatchMode" value="reconduce" class="mt-0.5 text-[#875212]" />
                            <span>
                                <span class="block text-sm font-semibold text-[#1e1b18]">Reconduction des anciens matchs</span>
                                <span class="block text-xs text-[#717a69] mt-0.5">
                                    Reprend les candidats acceptés / ayant exécuté un contrat
                                    ({{ $executableCandidates->count() }} candidat{{ $executableCandidates->count() > 1 ? 's' : '' }})
                                    en « Proposée » sur la nouvelle offre.
                                </span>
                            </span>
                        </label>
                    </div>

                    @if($relaunchMatchMode === 'reconduce' && $executableCandidates->isNotEmpty())
                        <div class="rounded-xl border border-[#c1c9b6] bg-[#fbfdfb] p-3 max-h-36 overflow-y-auto space-y-1.5">
                            <p class="text-[11px] font-bold text-[#717a69] uppercase tracking-wide mb-1">Candidats à reconduire</p>
                            @foreach($executableCandidates as $cand)
                                <p class="text-xs text-[#1e1b18] font-semibold">{{ $cand->full_name }} <span class="text-[#717a69] font-normal">{{ $cand->reference }}</span></p>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="px-5 py-4 border-t border-[#c1c9b6] bg-[#fbfdfb] flex justify-end gap-2">
                    <button type="button" wire:click="closeRelaunchModal"
                            class="px-4 py-2 text-sm font-semibold text-[#41493b] rounded-xl border border-[#c1c9b6] hover:bg-[#f5ece7]">
                        Annuler
                    </button>
                    <button type="button" wire:click="confirmRelaunch"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#875212] rounded-xl hover:opacity-90">
                        Confirmer la relance
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>