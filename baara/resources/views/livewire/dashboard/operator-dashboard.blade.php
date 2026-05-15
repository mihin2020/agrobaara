<div class="space-y-8">

    {{-- En-tête de bienvenue --}}
    <div>
        <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Tableau de bord</h2>
        <p class="text-[#41493b] mt-1 text-sm">Bienvenue, voici l'état actuel de votre pipeline agro-écologique.</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <span class="p-2 bg-[#aef585]/20 text-[#2c6904] rounded-xl material-symbols-outlined text-xl">person_add</span>
                <span class="text-[#2E7D32] font-bold text-xs flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">trending_up</span>+12%
                </span>
            </div>
            <p class="text-2xl font-sora font-bold text-[#1e1b18]">{{ number_format($totalCandidates) }}</p>
            <p class="text-[#41493b] font-semibold text-xs uppercase tracking-wide mt-1">Candidats créés</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <span class="p-2 bg-[#ffdcbd]/30 text-[#875212] rounded-xl material-symbols-outlined text-xl">corporate_fare</span>
                <span class="text-[#2E7D32] font-bold text-xs flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">trending_up</span>+5%
                </span>
            </div>
            <p class="text-2xl font-sora font-bold text-[#1e1b18]">{{ number_format($totalCompanies) }}</p>
            <p class="text-[#41493b] font-semibold text-xs uppercase tracking-wide mt-1">Entreprises</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <span class="p-2 bg-[#ebe2c8]/40 text-[#615c47] rounded-xl material-symbols-outlined text-xl">work_outline</span>
                <span class="text-[#D32F2F] font-bold text-xs flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">trending_down</span>-2%
                </span>
            </div>
            <p class="text-2xl font-sora font-bold text-[#1e1b18]">{{ number_format($activeOffers) }}</p>
            <p class="text-[#41493b] font-semibold text-xs uppercase tracking-wide mt-1">Offres actives</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <span class="p-2 bg-green-100 text-green-700 rounded-xl material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1">handshake</span>
                <span class="text-green-700 font-bold text-xs flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">check_circle</span>succès
                </span>
            </div>
            <p class="text-2xl font-sora font-bold text-[#1e1b18]">{{ number_format($successMatches) }}</p>
            <p class="text-[#41493b] font-semibold text-xs uppercase tracking-wide mt-1">Matching réussis</p>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.candidates.create') }}" wire:navigate
           class="group flex items-center gap-4 bg-[#2c6904] text-white p-5 rounded-2xl hover:bg-[#448322] transition-colors">
            <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">person_add</span>
            <div>
                <span class="block font-bold text-sm">Nouveau Candidat</span>
                <span class="text-xs opacity-80">Enregistrer un profil agro-écologique</span>
            </div>
        </a>
        <a href="{{ route('admin.companies.create') }}" wire:navigate
           class="group flex items-center gap-4 bg-[#875212] text-white p-5 rounded-2xl hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">domain_add</span>
            <div>
                <span class="block font-bold text-sm">Nouvelle Entreprise</span>
                <span class="text-xs opacity-80">Affilier un nouveau partenaire local</span>
            </div>
        </a>
        <a href="{{ route('admin.offers.create') }}" wire:navigate
           class="group flex items-center gap-4 bg-[#615c47] text-white p-5 rounded-2xl hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">post_add</span>
            <div>
                <span class="block font-bold text-sm">Nouvelle Offre</span>
                <span class="text-xs opacity-80">Publier une opportunité de terrain</span>
            </div>
        </a>
    </div>

    {{-- Données : matching + offres --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Matching prioritaires --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="font-sora text-base font-bold text-[#1e1b18]">Mises en relation prioritaires</h3>
                <a href="{{ route('admin.matches.index') }}" wire:navigate
                   class="text-[#2c6904] font-semibold text-sm hover:underline">Voir tout</a>
            </div>
            <div class="bg-white border border-[#c1c9b6] rounded-2xl overflow-hidden">
                @forelse($priorityMatches as $match)
                    <div class="p-4 flex items-center gap-4 hover:bg-[#fbf2ed] transition-colors border-b border-[#c1c9b6]/50 last:border-0">
                        <div class="w-11 h-11 rounded-xl bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-sm border border-[#c1c9b6] flex-shrink-0">
                            {{ strtoupper(substr($match->candidate->first_name, 0, 1) . substr($match->candidate->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-[#1e1b18] truncate text-sm">{{ $match->candidate->full_name }}</p>
                            <p class="text-xs text-[#41493b] truncate">
                                {{ $match->offer->title }} ·
                                <span class="text-[#875212] font-medium">{{ $match->offer->company->name }}</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span @class([
                                'px-2 py-0.5 text-[10px] font-bold rounded-full uppercase',
                                $match->status->badgeClass(),
                            ])>{{ $match->status->label() }}</span>
                            <span class="text-[10px] text-[#717a69]">{{ $match->updated_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('admin.matches.show', $match) }}" wire:navigate
                           class="p-2 hover:bg-[#f5ece7] rounded-full text-[#41493b] flex-shrink-0">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#717a69]">
                        <span class="material-symbols-outlined text-4xl mb-2 block">handshake</span>
                        <p class="text-sm">Aucune mise en relation en cours.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Dernières offres --}}
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="font-sora text-base font-bold text-[#1e1b18]">Dernières Offres</h3>
                <a href="{{ route('admin.offers.index') }}" wire:navigate
                   class="text-[#2c6904] font-semibold text-sm hover:underline">Voir tout</a>
            </div>
            <div class="bg-white border border-[#c1c9b6] rounded-2xl overflow-hidden">
                <div class="p-3 space-y-2">
                    @forelse($latestOffers as $offer)
                        <div class="p-3 rounded-xl border border-[#c1c9b6] hover:bg-[#fbf2ed]/40 transition-colors">
                            <div class="flex justify-between items-start gap-2">
                                <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $offer->title }}</p>
                                @if($offer->isDraft())
                                    <span class="material-symbols-outlined text-amber-500 text-lg flex-shrink-0">lock</span>
                                @endif
                            </div>
                            <p class="text-xs text-[#41493b] mb-2">{{ $offer->company->name }}</p>
                            <div class="flex justify-between items-center">
                                <span @class([
                                    'text-[10px] font-bold px-2 py-0.5 rounded',
                                    $offer->status->badgeClass(),
                                ])>{{ $offer->status->label() }}</span>
                                @if($offer->isDraft())
                                    <a href="{{ route('admin.offers.edit', $offer) }}" wire:navigate
                                       class="text-[10px] font-bold text-[#2c6904] flex items-center gap-0.5 hover:underline">
                                        VALIDER <span class="material-symbols-outlined text-sm">chevron_right</span>
                                    </a>
                                @else
                                    <span class="text-[10px] text-[#717a69]">{{ $offer->published_at?->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-[#717a69] text-sm">Aucune offre créée.</div>
                    @endforelse
                </div>
                <a href="{{ route('admin.offers.index') }}" wire:navigate
                   class="block w-full py-3 text-center text-[#2c6904] font-semibold text-sm border-t border-[#c1c9b6] hover:bg-[#fbf2ed] transition-colors">
                    Voir tout le catalogue
                </a>
            </div>
        </div>
    </div>

    {{-- Outils d'audit interne (Modérateur + Super-admin) --}}
    @if($auditData)
        <section class="bg-[#F9F7F2] p-6 rounded-2xl border border-dashed border-[#c1c9b6]">
            <div class="flex items-center gap-3 mb-5">
                <span class="material-symbols-outlined text-amber-600">admin_panel_settings</span>
                <h4 class="font-bold text-sm text-[#1e1b18]">Outils d'Audit Interne</h4>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1 p-4 bg-white rounded-xl border border-[#c1c9b6]">
                    <span class="text-[10px] uppercase font-bold text-[#41493b] tracking-wide">Données manquantes</span>
                    <span class="text-lg font-sora font-bold text-[#875212]">{{ $auditData['missing_data'] }} fiches</span>
                </div>
                <div class="flex flex-col gap-1 p-4 bg-white rounded-xl border border-[#c1c9b6]">
                    <span class="text-[10px] uppercase font-bold text-[#41493b] tracking-wide">Opérateurs actifs</span>
                    <span class="text-lg font-sora font-bold text-[#2c6904]">{{ $auditData['active_operators'] }} en ligne</span>
                </div>
                <div class="flex flex-col gap-1 p-4 bg-white rounded-xl border border-[#c1c9b6]">
                    <span class="text-[10px] uppercase font-bold text-[#41493b] tracking-wide">Dernier Back-up</span>
                    <span class="text-lg font-sora font-bold text-[#2c6904]">Succès (04:00)</span>
                </div>
            </div>
        </section>
    @endif

</div>
