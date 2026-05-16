<div class="space-y-5 max-w-5xl mx-auto">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif

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
            <button wire:click="toggleSuggestedCandidates"
                    class="flex items-center gap-2 px-4 py-2 bg-[#2c6904] text-white font-semibold text-sm rounded-xl hover:bg-[#448322] transition-colors">
                <span class="material-symbols-outlined text-base">psychology</span>
                {{ $showSuggestedCandidates ? 'Masquer' : 'Matching candidats' }}
            </button>
        </div>
    </div>

    {{-- Candidats suggérés --}}
    @if($showSuggestedCandidates)
        <div class="bg-[#f5ece7] rounded-2xl p-4 border border-[#c1c9b6] space-y-3" wire:loading.remove wire:target="toggleSuggestedCandidates">
            <h3 class="font-sora font-bold text-sm text-[#2c6904] flex items-center gap-2">
                <span class="material-symbols-outlined text-base">psychology</span>
                Candidats suggérés par le matching
            </h3>
            @forelse($suggestedCandidates as $candidate)
                <div class="bg-white rounded-xl border border-[#c1c9b6] p-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-sm border border-[#c1c9b6]">
                            {{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-[#1e1b18]">{{ $candidate->full_name }}</p>
                            <p class="text-xs text-[#41493b]">{{ $candidate->commune?->name ?? '—' }} · {{ $candidate->education_level?->label() ?? '—' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.candidates.show', $candidate) }}" wire:navigate
                       class="flex-shrink-0 px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg hover:bg-[#448322] transition-colors">
                        Voir
                    </a>
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
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $offer->duration }}</p>
                            </div>
                        @endif
                        @if($offer->start_date)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Début souhaité</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $offer->start_date->format('d/m/Y') }}</p>
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
</div>