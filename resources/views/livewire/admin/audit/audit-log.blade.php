<div class="space-y-6">

    {{-- En-tête --}}
    <div>
        <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Journal d'Audit</h2>
        <p class="text-[#41493b] mt-1 text-sm">Traçabilité de toutes les actions réalisées dans le système.</p>
    </div>

    {{-- Filtres --}}
    <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Recherche</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-base">search</span>
                    <input wire:model.live.debounce.500ms="search"
                           type="text"
                           placeholder="Action, e-mail..."
                           class="w-full pl-9 pr-4 py-2 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Type d'événement</label>
                <select wire:model.live="event"
                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all">
                    <option value="">Tous les événements</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt }}">{{ $evt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Date de début</label>
                <input wire:model.live="dateFrom" type="date"
                       class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Date de fin</label>
                <input wire:model.live="dateTo" type="date"
                       class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all" />
            </div>
        </div>
        @if($search || $event || $dateFrom || $dateTo)
            <div class="flex justify-end mt-3 pt-3 border-t border-[#c1c9b6]/50">
                <button wire:click="$set('search', ''); $set('event', ''); $set('dateFrom', ''); $set('dateTo', '')"
                        class="flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold">
                    <span class="material-symbols-outlined text-base">filter_alt_off</span>
                    Réinitialiser les filtres
                </button>
            </div>
        @endif
    </div>

    {{-- Journal --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Date / Heure</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Utilisateur</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Objet</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Détails</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3 text-xs text-[#41493b] whitespace-nowrap">
                                <p class="font-semibold text-[#1e1b18]">{{ $activity->created_at->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-[#717a69]">{{ $activity->created_at->format('H:i:s') }}</p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($activity->causer)
                                    <p class="text-sm font-semibold text-[#1e1b18]">{{ $activity->causer->full_name }}</p>
                                    <p class="text-[11px] text-[#717a69]">{{ $activity->causer->email }}</p>
                                @else
                                    <span class="text-xs text-[#717a69] italic">Système</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-[11px] font-semibold px-2 py-0.5 bg-[#f5ece7] text-[#1e1b18] rounded border border-[#c1c9b6]">
                                    {{ $activity->description }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($activity->subject)
                                    <span class="text-xs text-[#41493b]">{{ class_basename($activity->subject_type) }}</span>
                                @else
                                    <span class="text-xs text-[#717a69]">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($activity->properties && $activity->properties->isNotEmpty())
                                    <div class="text-[11px] text-[#717a69] space-y-0.5">
                                        @foreach($activity->properties->except(['old', 'attributes'])->take(2) as $key => $val)
                                            <p><span class="font-semibold">{{ $key }}</span>: {{ is_string($val) ? $val : json_encode($val) }}</p>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-[#717a69]">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">history</span>
                                <p class="text-[#717a69] font-medium text-sm">Aucune entrée dans le journal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    {{ $activities->total() }} entrée(s) au total
                </p>
                {{ $activities->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#1e1b18] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>
</div>