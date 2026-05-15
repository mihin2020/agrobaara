<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Gestion des Entreprises</h2>
            <p class="text-[#41493b] mt-1 text-sm">Partenaires et organisations agroécologiques du territoire.</p>
        </div>
        @can('create', \App\Models\Company::class)
            <a href="{{ route('admin.companies.create') }}" wire:navigate
               class="flex items-center gap-2 bg-[#875212] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-lg">add_business</span>
                Ajouter une entreprise
            </a>
        @endcan
    </div>

    {{-- Recherche --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="relative max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
            <input wire:model.live.debounce.400ms="search"
                   type="text"
                   placeholder="Nom, référence, téléphone, e-mail..."
                   class="w-full pl-10 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#875212]/20 focus:border-[#875212] transition-all" />
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Entreprise</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Référence</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Statut juridique</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Sites</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Offres publiées</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Créée le</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($companies as $company)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#ffdcbd]/40 border border-[#c1c9b6] flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-base text-[#875212]">domain</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-[#1e1b18]">{{ $company->name }}</p>
                                        @if($company->phone)
                                            <p class="text-xs text-[#41493b]">{{ $company->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="font-mono text-xs font-semibold text-[#875212] bg-[#ffdcbd]/20 px-2 py-1 rounded-lg">
                                    {{ $company->reference }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <span class="text-xs font-semibold px-2 py-1 bg-[#f5ece7] text-[#41493b] rounded-lg border border-[#c1c9b6]">
                                    {{ $company->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-[#1e1b18] hidden md:table-cell">
                                {{ $company->sites->count() }}
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                @if($company->published_offers_count > 0)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                        {{ $company->published_offers_count }} offre(s)
                                    </span>
                                @else
                                    <span class="text-xs text-[#717a69]">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-[#41493b] hidden md:table-cell">
                                {{ $company->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.companies.show', $company) }}" wire:navigate
                                       class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#875212] rounded-lg transition-colors"
                                       title="Voir la fiche">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    @can('update', $company)
                                        <a href="{{ route('admin.companies.edit', $company) }}" wire:navigate
                                           class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#875212] rounded-lg transition-colors"
                                           title="Modifier">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">domain</span>
                                <p class="text-[#717a69] font-medium text-sm">Aucune entreprise trouvée.</p>
                                @if($search)
                                    <button wire:click="$set('search', '')"
                                            class="mt-2 text-sm text-[#875212] hover:underline font-semibold">
                                        Effacer la recherche
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    Affichage de {{ $companies->firstItem() }} à {{ $companies->lastItem() }} sur {{ $companies->total() }} entreprises
                </p>
                {{ $companies->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#875212] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>
</div>