<div class="space-y-5 max-w-5xl mx-auto">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.companies.index') }}" wire:navigate
               class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div class="w-12 h-12 rounded-2xl bg-[#ffdcbd]/40 border border-[#c1c9b6] flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl text-[#875212]">domain</span>
            </div>
            <div>
                <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">{{ $company->name }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="font-mono text-xs font-semibold text-[#875212] bg-[#ffdcbd]/20 px-2 py-0.5 rounded-lg">{{ $company->reference }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 bg-[#f5ece7] text-[#41493b] rounded-lg border border-[#c1c9b6]">{{ $company->status->label() }}</span>
                </div>
            </div>
        </div>
        @can('update', $company)
            <a href="{{ route('admin.companies.edit', $company) }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors self-start sm:self-auto">
                <span class="material-symbols-outlined text-base">edit</span>
                Modifier
            </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-4">

            {{-- Informations générales --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#875212]">info</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Informations générales</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        @if($company->legal_rep_first_name || $company->legal_rep_last_name)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Représentant légal</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ trim($company->legal_rep_first_name . ' ' . $company->legal_rep_last_name) }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Téléphone</p>
                            <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $company->phone }}</p>
                        </div>
                        @if($company->email)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">E-mail</p>
                                <p class="text-sm font-semibold text-[#1e1b18] mt-0.5">{{ $company->email }}</p>
                            </div>
                        @endif
                        @if($company->website)
                            <div>
                                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Site web</p>
                                <a href="{{ $company->website }}" target="_blank" class="text-sm font-semibold text-[#875212] hover:underline mt-0.5 block">{{ $company->website }}</a>
                            </div>
                        @endif
                    </div>
                    @if($company->social_links && count(array_filter($company->social_links)))
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-2">Pages sociales</p>
                            <div class="flex flex-wrap gap-2">
                                @if(!empty($company->social_links['facebook']))
                                    <a href="{{ $company->social_links['facebook'] }}" target="_blank"
                                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#c1c9b6] bg-[#fbf2ed] text-xs font-semibold text-[#1e1b18] hover:border-[#875212]/40 transition-colors">
                                        <span style="color:#1877F2;font-weight:700">f</span> Facebook
                                    </a>
                                @endif
                                @if(!empty($company->social_links['linkedin']))
                                    <a href="{{ $company->social_links['linkedin'] }}" target="_blank"
                                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#c1c9b6] bg-[#fbf2ed] text-xs font-semibold text-[#1e1b18] hover:border-[#875212]/40 transition-colors">
                                        <span style="color:#0A66C2;font-weight:700">in</span> LinkedIn
                                    </a>
                                @endif
                                @if(!empty($company->social_links['whatsapp']))
                                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#c1c9b6] bg-[#fbf2ed] text-xs font-semibold text-[#1e1b18]">
                                        <span style="color:#25D366;font-weight:700">W</span> {{ $company->social_links['whatsapp'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($company->activity_types && count($company->activity_types) > 0)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-2">Types d'activité</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($company->activity_types as $type)
                                    <span class="px-2.5 py-1 bg-[#ffdcbd]/20 text-[#875212] text-xs font-semibold rounded-lg border border-[#875212]/20">{{ $type }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($company->description)
                        <div>
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1">Description</p>
                            <p class="text-sm text-[#1e1b18] bg-[#fbf2ed] rounded-xl p-3 leading-relaxed">{{ $company->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sites --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#875212]">location_on</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Sites d'activité ({{ $company->sites->count() }})</h3>
                </div>
                <div class="divide-y divide-[#c1c9b6]/40">
                    @forelse($company->sites as $site)
                        <div class="p-4 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#ffdcbd]/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-sm text-[#875212]">{{ $site->is_main ? 'home' : 'place' }}</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-sm text-[#1e1b18]">{{ $site->label }}</p>
                                    @if($site->is_main)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-[#875212] text-white rounded-full">Principal</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#41493b] mt-0.5">{{ $site->commune?->name ?? '—' }}</p>
                                @if($site->address)<p class="text-xs text-[#717a69] mt-0.5">{{ $site->address }}</p>@endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-[#717a69]">Aucun site renseigné.</div>
                    @endforelse
                </div>
            </div>

            {{-- Offres --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#875212]">work_outline</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Offres associées ({{ $company->offers->count() }})</h3>
                    </div>
                    @can('create', \App\Models\JobOffer::class)
                        <a href="{{ route('admin.offers.create') }}" wire:navigate
                           class="text-xs text-[#2c6904] font-semibold hover:underline flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-sm">add</span>Nouvelle offre
                        </a>
                    @endcan
                </div>
                <div class="divide-y divide-[#c1c9b6]/40">
                    @forelse($company->offers->take(8) as $offer)
                        <div class="p-3 flex items-center justify-between gap-3 hover:bg-[#fbf2ed]/50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#1e1b18] truncate">{{ $offer->title }}</p>
                                <p class="text-xs text-[#41493b]">{{ $offer->contract_type->label() }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $offer->status->badgeClass() }}">{{ $offer->status->label() }}</span>
                                <a href="{{ route('admin.offers.show', $offer) }}" wire:navigate
                                   class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg">
                                    <span class="material-symbols-outlined text-base">chevron_right</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-[#717a69]">Aucune offre créée pour cette entreprise.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c1c9b6] bg-amber-50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-amber-600">admin_panel_settings</span>
                    <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Besoins internes</h3>
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between py-2 border-b border-[#c1c9b6]/40">
                        <span class="text-xs font-semibold text-[#1e1b18]">Formation</span>
                        <span @class(['text-xs font-bold px-2 py-0.5 rounded-full', 'bg-orange-100 text-orange-700' => $company->need_training, 'bg-gray-100 text-gray-500' => !$company->need_training])>{{ $company->need_training ? 'Oui' : 'Non' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-[#c1c9b6]/40">
                        <span class="text-xs font-semibold text-[#1e1b18]">Financement</span>
                        <span @class(['text-xs font-bold px-2 py-0.5 rounded-full', 'bg-orange-100 text-orange-700' => $company->need_financing, 'bg-gray-100 text-gray-500' => !$company->need_financing])>{{ $company->need_financing ? 'Oui' : 'Non' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs font-semibold text-[#1e1b18]">Support contrat</span>
                        <span @class(['text-xs font-bold px-2 py-0.5 rounded-full', 'bg-orange-100 text-orange-700' => $company->need_contract_support, 'bg-gray-100 text-gray-500' => !$company->need_contract_support])>{{ $company->need_contract_support ? 'Oui' : 'Non' }}</span>
                    </div>
                    @if($company->operator_notes)
                        <div class="pt-2">
                            <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide mb-1">Notes opérateur</p>
                            <p class="text-xs text-[#1e1b18] bg-amber-50 rounded-xl p-3 border border-amber-200/50 leading-relaxed">{{ $company->operator_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-4 space-y-2">
                <p class="text-[11px] text-[#717a69] uppercase font-bold tracking-wide">Informations système</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Créée le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $company->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#717a69]">Modifiée le</span>
                        <span class="font-semibold text-[#1e1b18]">{{ $company->updated_at->format('d/m/Y') }}</span>
                    </div>
                    @if($company->createdBy)
                        <div class="flex justify-between">
                            <span class="text-[#717a69]">Créée par</span>
                            <span class="font-semibold text-[#1e1b18]">{{ $company->createdBy->full_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
