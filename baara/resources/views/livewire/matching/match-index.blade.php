<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Matching & Mises en relation</h2>
            <p class="text-[#41493b] mt-1 text-sm">Suivi de toutes les mises en relation candidats–offres.</p>
        </div>
    </div>

    {{-- Filtres statut --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold text-[#1e1b18] whitespace-nowrap">Filtrer par statut :</span>
            <div class="flex flex-wrap gap-1.5">
                <button wire:click="$set('status', '')"
                        @class(['px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all', 'bg-[#1e1b18] text-white border-[#1e1b18]' => $status === '', 'border-[#c1c9b6] text-[#41493b] hover:border-[#1e1b18]' => $status !== ''])>
                    Tous
                </button>
                @foreach($statuses as $s)
                    <button wire:click="$set('status', '{{ $s->value }}')"
                            @class(['px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all', $s->badgeClass() . ' border-transparent' => $status === $s->value, 'border-[#c1c9b6] text-[#41493b] hover:border-[#1e1b18]/30' => $status !== $s->value])>
                        {{ $s->label() }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Candidat</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Offre</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Entreprise</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Opérateur</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Mise à jour</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($matches as $match)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-[#f5ece7] flex items-center justify-center text-[#2c6904] font-bold text-xs flex-shrink-0 border border-[#c1c9b6]">
                                        {{ strtoupper(substr($match->candidate->first_name, 0, 1) . substr($match->candidate->last_name, 0, 1)) }}
                                    </div>
                                    <p class="font-semibold text-sm text-[#1e1b18] truncate">{{ $match->candidate->full_name }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-sm text-[#1e1b18] truncate max-w-[160px]">{{ $match->offer->title }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <p class="text-sm font-semibold text-[#875212] truncate max-w-[120px]">{{ $match->offer->company->name }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $match->status->badgeClass() }}">
                                    {{ $match->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-xs text-[#41493b]">{{ $match->operator?->full_name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <p class="text-xs text-[#717a69]">{{ $match->updated_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('admin.matches.show', $match) }}" wire:navigate
                                   class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#2c6904] rounded-lg transition-colors inline-flex">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">handshake</span>
                                <p class="text-[#717a69] font-medium text-sm">Aucune mise en relation trouvée.</p>
                                @if($status)
                                    <button wire:click="$set('status', '')"
                                            class="mt-2 text-sm text-[#2c6904] hover:underline font-semibold">
                                        Voir toutes les mises en relation
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matches->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    Affichage de {{ $matches->firstItem() }} à {{ $matches->lastItem() }} sur {{ $matches->total() }} résultats
                </p>
                {{ $matches->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#2c6904] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>
</div>