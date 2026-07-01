<div class="space-y-6" x-data="{
    archiveModal: false,
    archiveId: null,
    openArchive(id) { this.archiveId = id; this.archiveModal = true; },
    confirmArchive() { $wire.archiveOffer(this.archiveId); this.archiveModal = false; }
}">

    {{-- Modal confirmation archivage --}}
    <div x-show="archiveModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="archiveModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-gray-500">archive</span>
                </div>
                <div>
                    <h3 class="font-sora font-bold text-base text-[#1e1b18]">Archiver cette offre ?</h3>
                    <p class="text-xs text-[#717a69] mt-0.5">Cette action peut être annulée par un administrateur.</p>
                </div>
            </div>
            <p class="text-sm text-[#41493b]">L'offre sera retirée de la liste active et ne sera plus visible pour le matching. Vous pourrez la désarchiver en cas d'erreur.</p>
            <div class="flex gap-3 pt-1">
                <button @click="archiveModal = false"
                        class="flex-1 px-4 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold text-sm rounded-xl hover:bg-[#f5ece7] transition-colors">
                    Annuler
                </button>
                <button @click="confirmArchive()"
                        class="flex-1 px-4 py-2.5 bg-gray-600 text-white font-semibold text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Archiver
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Offres d'emploi</h2>
            <p class="text-[#41493b] mt-1 text-sm">Gérez le catalogue des opportunités.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-export-button />
            @can('create', \App\Models\JobOffer::class)
                <a href="{{ route('admin.offers.create') }}" wire:navigate
                   class="flex items-center gap-2 bg-[#615c47] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-lg">post_add</span>
                    Publier une offre
                </a>
            @endcan
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
                <input wire:model.live.debounce.400ms="search"
                       type="text"
                       placeholder="Titre, référence, entreprise..."
                       class="w-full pl-10 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#615c47]/20 focus:border-[#615c47] transition-all" />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-[#1e1b18] whitespace-nowrap">Statut :</span>
                <div class="flex bg-[#fbf2ed] p-1 rounded-xl gap-0.5">
                    <button wire:click="$set('status', '')"
                            @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#615c47] shadow-sm' => $status === '', 'text-[#41493b] hover:text-[#1e1b18]' => $status !== ''])>
                        Toutes
                    </button>
                    @foreach($statuses as $s)
                        <button wire:click="$set('status', '{{ $s->value }}')"
                                @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#615c47] shadow-sm' => $status === $s->value, 'text-[#41493b] hover:text-[#1e1b18]' => $status !== $s->value])>
                            {{ $s->label() }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Offre</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Entreprise</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Type</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Postes</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Matching</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div>
                                    <p class="font-semibold text-sm text-[#1e1b18]">{{ $offer->title }}</p>
                                    <span class="font-mono text-[11px] font-semibold text-[#615c47] bg-[#ebe2c8]/30 px-1.5 py-0.5 rounded">{{ $offer->reference }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <a href="{{ route('admin.companies.show', $offer->company) }}" wire:navigate
                                   class="text-sm font-semibold text-[#875212] hover:underline">{{ $offer->company->name }}</a>
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <span class="text-xs text-[#41493b]">{{ $offer->contract_type->label() }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-[#1e1b18] hidden md:table-cell">
                                {{ $offer->positions_count ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                @if($offer->matches_count > 0)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">{{ $offer->matches_count }}</span>
                                @else
                                    <span class="text-xs text-[#717a69]">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $offer->status->badgeClass() }}">
                                    {{ $offer->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.offers.show', $offer) }}" wire:navigate
                                       class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#615c47] rounded-lg transition-colors" title="Voir">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    @can('update', $offer)
                                        <a href="{{ route('admin.offers.edit', $offer) }}" wire:navigate
                                           class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#615c47] rounded-lg transition-colors" title="Modifier">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                    @endcan
                                    @can('publish', $offer)
                                        @if($offer->isDraft())
                                            <button wire:click="publishOffer('{{ $offer->id }}')"
                                                    class="p-1.5 text-[#41493b] hover:bg-green-50 hover:text-green-700 rounded-lg transition-colors" title="Publier">
                                                <span class="material-symbols-outlined text-lg">publish</span>
                                            </button>
                                        @endif
                                    @endcan
                                    @can('archive', $offer)
                                        @if($offer->isPublished())
                                            <button @click="openArchive('{{ $offer->id }}')"
                                                    class="p-1.5 text-[#41493b] hover:bg-gray-50 hover:text-gray-600 rounded-lg transition-colors" title="Archiver">
                                                <span class="material-symbols-outlined text-lg">archive</span>
                                            </button>
                                        @endif
                                    @endcan
                                    @can('unarchive', $offer)
                                        @if($offer->isArchived())
                                            <button wire:click="unarchiveOffer('{{ $offer->id }}')"
                                                    wire:confirm="Remettre cette offre en brouillon ?"
                                                    class="p-1.5 text-[#41493b] hover:bg-amber-50 hover:text-amber-700 rounded-lg transition-colors" title="Désarchiver">
                                                <span class="material-symbols-outlined text-lg">unarchive</span>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">work_outline</span>
                                <p class="text-[#717a69] font-medium text-sm">Aucune offre trouvée.</p>
                                @if($search || $status)
                                    <button wire:click="$set('search', ''); $set('status', '')"
                                            class="mt-2 text-sm text-[#615c47] hover:underline font-semibold">
                                        Réinitialiser les filtres
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($offers->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    Affichage de {{ $offers->firstItem() }} à {{ $offers->lastItem() }} sur {{ $offers->total() }} offres
                </p>
                {{ $offers->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#615c47] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>
</div>