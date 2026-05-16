<div class="space-y-5 max-w-5xl mx-auto">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.candidates.index') }}" wire:navigate
               class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div class="w-12 h-12 rounded-2xl bg-[#2c6904] flex items-center justify-center text-white font-bold text-base flex-shrink-0">
                {{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}
            </div>
            <div>
                <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">{{ $candidate->full_name }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="font-mono text-xs font-semibold text-[#2c6904] bg-[#aef585]/20 px-2 py-0.5 rounded-lg">{{ $candidate->reference }}</span>
                    <span class="text-xs text-[#717a69]">{{ $candidate->age }} ans · {{ $candidate->gender->label() }}</span>
                </div>
            </div>
        </div>
        @can('update', $candidate)
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.candidates.edit', $candidate) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Modifier
                </a>
                <button wire:click="toggleSuggestedOffers"
                        class="flex items-center gap-2 px-4 py-2 bg-[#2c6904] text-white font-semibold text-sm rounded-xl hover:bg-[#448322] transition-colors">
                    <span class="material-symbols-outlined text-base">psychology</span>
                    {{ $showSuggestedOffers ? 'Masquer offres' : 'Trouver des offres' }}
                </button>
            </div>
        @endcan
    </div>

    {{-- Offres suggérées --}}
    @if($showSuggestedOffers)
        <div class="bg-[#f5ece7] rounded-2xl p-4 border border-[#c1c9b6] space-y-3" wire:loading.remove wire:target="toggleSuggestedOffers">
            <h3 class="font-sora font-bold text-sm text-[#2c6904] flex items-center gap-2">
                <span class="material-symbols-outlined text-base">psychology</span>
                Offres suggérées par le matching
            </h3>
            @forelse($suggestedOffers as $item)
                <div class="bg-white rounded-xl border border-[#c1c9b6] p-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-sm text-[#1e1b18]">{{ $item['offer']->title }}</p>
                        <p class="text-xs text-[#41493b]">{{ $item['offer']->company->name }} · {{ $item['offer']->contract_type->label() }}</p>
                    </div>
                    <a href="{{ route('admin.offers.show', $item['offer']) }}" wire:navigate
                       class="flex-shrink-0 px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg hover:bg-[#448322] transition-colors">
                        Voir
                    </a>
                </div>
            @empty
                <p class="text-sm text-[#717a69] text-center py-3">Aucune offre correspondante trouvée.</p>
            @endforelse
        </div>
        <div wire:loading wire:target="toggleSuggestedOffers" class="text-center py-3 text-sm text-[#717a69]">
            <span class="material-symbols-outlined animate-spin text-lg align-middle">progress_activity</span> Analyse en cours...
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Colonne gauche — Profil --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Section A — Identité --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#2c6904]">badge</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Identité & Localisation</h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Prénom</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Nom</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Date de naissance</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->birth_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Lieu de naissance</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->birth_place ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Situation matrimoniale</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->marital_status ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Nationalité</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->nationality }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Commune</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->commune?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Adresse</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->address ?? '—' }}</p>
                    </div>
                    @if($candidate->transport_mode)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Transport</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->transport_mode->label() }}</p>
                        </div>
                    @endif
                    @if($candidate->licenses->isNotEmpty())
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Permis</p>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @foreach($candidate->licenses as $lic)
                                    <span class="px-2 py-0.5 bg-[#f5ece7] text-[#41493b] text-xs font-semibold rounded-lg border border-[#c1c9b6]">{{ $lic->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section B — Contacts --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#2c6904]">contact_phone</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Contacts & Langues</h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Tél. principal</p>
                        <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->phone }}</p>
                    </div>
                    @if($candidate->phone_secondary)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Tél. secondaire</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->phone_secondary }}</p>
                        </div>
                    @endif
                    @if($candidate->email)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">E-mail</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->email }}</p>
                        </div>
                    @endif
                    <div class="col-span-2">
                        <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1.5">Langues parlées</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($candidate->languages as $lang)
                                <span class="px-2.5 py-1 bg-[#aef585]/20 text-[#2c6904] text-xs font-semibold rounded-lg border border-[#2c6904]/20">{{ $lang->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section C — Formation --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#2c6904]">school</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Formation & Éducation</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-x-8">
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Niveau d'étude</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->educationLevel?->name ?? ($candidate->education_level ? ucfirst($candidate->education_level) : '—') }}</p>
                        </div>
                        @if($candidate->agro_training_place)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Lieu de formation</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $candidate->agro_training_place }}</p>
                            </div>
                        @endif
                    </div>
                    @if($candidate->agro_training_text)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1">Formation agroécologique</p>
                            <p class="text-sm text-[#1e1b18] leading-relaxed bg-[#fbf2ed] rounded-xl p-3">{{ $candidate->agro_training_text }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section D — Compétences & Expériences --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#2c6904]">work_history</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Compétences & Expériences</h3>
                </div>
                <div class="p-5 space-y-4">
                    @if($candidate->skills->isNotEmpty())
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-2">Compétences agroécologiques</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($candidate->skills as $skill)
                                    <span class="px-2.5 py-1 bg-[#f5ece7] text-[#41493b] text-xs font-semibold rounded-lg border border-[#c1c9b6]">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($candidate->other_skills_text)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1">Autres compétences</p>
                            <p class="text-sm text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-3">{{ $candidate->other_skills_text }}</p>
                        </div>
                    @endif
                    @if($candidate->experiences->isNotEmpty())
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-2">Expériences professionnelles</p>
                            <div class="space-y-2">
                                @foreach($candidate->experiences as $exp)
                                    <div class="bg-[#fbf2ed] rounded-xl p-3 border border-[#c1c9b6]">
                                        <div class="flex justify-between items-start">
                                            <p class="font-semibold text-sm text-[#1e1b18]">{{ $exp->position ?? '—' }}</p>
                                            @if($exp->year)<span class="text-xs text-[#717a69] font-semibold">{{ $exp->year }}</span>@endif
                                        </div>
                                        @if($exp->location)<p class="text-xs text-[#41493b] mt-0.5">{{ $exp->location }}</p>@endif
                                        @if($exp->employer_contacts)<p class="text-xs text-[#717a69] mt-0.5">Contact : {{ $exp->employer_contacts }}</p>@endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-4">

            {{-- Besoins exprimés --}}
            @php
                $employmentLabels = [
                    'emploi_salarie'       => 'Emploi salarié',
                    'contrats_saisonniers' => 'Contrats saisonniers',
                    'missions_ponctuelles' => 'Missions ponctuelles',
                    'apprentissage'        => 'Apprentissage',
                    'entrepreneuriat'      => 'Entrepreneuriat',
                ];
                $formationLabels = [
                    'cours'          => 'Cours',
                    'master'         => 'Master',
                    'atelier'        => 'Atelier',
                    'stage'          => 'Stage',
                    'voyage_echange' => "Voyage d'échange",
                ];
                $employmentTypes = $candidate->need_employment_types ?? [];
                $formationTypes  = $candidate->need_formation_types ?? [];
                $hasAnyNeed = !empty($employmentTypes) || !empty($formationTypes) || $candidate->need_financing || $candidate->need_cv_support;
            @endphp
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-amber-50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-amber-600">admin_panel_settings</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Besoins exprimés</h3>
                </div>
                <div class="p-4 space-y-3">
                    @if(!$hasAnyNeed)
                        <p class="text-xs text-[#717a69] text-center py-2">Aucun besoin enregistré.</p>
                    @else
                        {{-- Emploi --}}
                        @if(!empty($employmentTypes))
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="material-symbols-outlined text-[#2c6904]" style="font-size:14px">work</span>
                                    <p class="text-[11px] text-[#2c6904] uppercase font-bold tracking-wide">Emploi</p>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($employmentTypes as $type)
                                        <span class="text-[11px] font-semibold px-2.5 py-1 bg-[#aef585]/20 text-[#2c6904] border border-[#2c6904]/20 rounded-full">
                                            {{ $employmentLabels[$type] ?? $type }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Formation --}}
                        @if(!empty($formationTypes))
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="material-symbols-outlined text-[#2c6904]" style="font-size:14px">school</span>
                                    <p class="text-[11px] text-[#2c6904] uppercase font-bold tracking-wide">Formation</p>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($formationTypes as $type)
                                        <span class="text-[11px] font-semibold px-2.5 py-1 bg-[#aef585]/20 text-[#2c6904] border border-[#2c6904]/20 rounded-full">
                                            {{ $formationLabels[$type] ?? $type }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Financement + Appui côte à côte --}}
                        @if($candidate->need_financing || $candidate->need_cv_support)
                            <div class="flex flex-wrap gap-2 pt-1">
                                @if($candidate->need_financing)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                                        <span class="material-symbols-outlined" style="font-size:12px">payments</span>
                                        Financement
                                    </span>
                                @endif
                                @if($candidate->need_cv_support)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full">
                                        <span class="material-symbols-outlined" style="font-size:12px">description</span>
                                        Appui rédaction CV
                                    </span>
                                @endif
                            </div>
                        @endif
                    @endif

                    {{-- Notes opérateur --}}
                    @if($candidate->operator_notes)
                        <div class="pt-1 border-t border-[#c1c9b6]/40">
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1">Notes opérateur</p>
                            <p class="text-xs text-[#1e1b18] bg-amber-50 rounded-xl p-3 border border-amber-200/50 leading-relaxed">{{ $candidate->operator_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Historique matching --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">handshake</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Mises en relation</h3>
                    </div>
                    <span class="text-xs font-bold text-[#2c6904] bg-[#aef585]/30 px-2 py-0.5 rounded-full">{{ $candidate->matches->count() }}</span>
                </div>
                <div class="divide-y divide-[#c1c9b6]/40">
                    @forelse($candidate->matches->take(5) as $match)
                        <div class="p-3 hover:bg-[#fbf2ed]/50 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#1e1b18] truncate">{{ $match->offer->title }}</p>
                                    <p class="text-[11px] text-[#875212] truncate">{{ $match->offer->company->name }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 {{ $match->status->badgeClass() }}">
                                    {{ $match->status->label() }}
                                </span>
                            </div>
                            <p class="text-[11px] text-[#717a69] mt-1">{{ $match->updated_at->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-[#717a69] text-xs">Aucune mise en relation.</div>
                    @endforelse
                </div>
                @if($candidate->matches->count() > 5)
                    <div class="px-4 py-3 border-t border-[#c1c9b6]">
                        <a href="{{ route('admin.matches.index') }}" wire:navigate
                           class="text-xs text-[#2c6904] font-semibold hover:underline">Voir tout ({{ $candidate->matches->count() }})</a>
                    </div>
                @endif
            </div>

            {{-- Méta --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4 space-y-2">
                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Informations système</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Créé le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $candidate->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Modifié le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $candidate->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($candidate->createdBy)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Créé par</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $candidate->createdBy->full_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>