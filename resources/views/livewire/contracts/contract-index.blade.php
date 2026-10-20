<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Contrats</h2>
            <p class="text-[#41493b] mt-1 text-sm">Fiches contrats — période, renouvellement et suivi.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create', App\Models\Contract::class)
                <a href="{{ route('admin.contracts.create') }}" wire:navigate
                   class="flex items-center gap-1.5 px-4 py-2 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                    <span class="material-symbols-outlined text-base">add</span>
                    Nouveau contrat
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold text-[#1e1b18] whitespace-nowrap">Statut :</span>
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

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($contracts as $contract)
            <a href="{{ route('admin.contracts.show', $contract) }}" wire:navigate
               class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm hover:border-[#2c6904]/40 hover:shadow-md transition-all p-5 flex flex-col gap-3 group">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-mono text-[11px] font-bold text-[#2c6904] bg-[#aef585]/20 px-2 py-0.5 rounded-lg inline-block">
                            {{ $contract->reference ?? 'Sans réf.' }}
                        </p>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18] mt-2 truncate group-hover:text-[#2c6904]">
                            {{ $contract->candidate->full_name }}
                        </h3>
                        <p class="text-xs text-[#875212] font-semibold truncate mt-0.5">{{ $contract->company->name }}</p>
                    </div>
                    <x-status-badge :status="$contract->status" class="flex-shrink-0" />
                </div>

                <div class="text-xs text-[#41493b] space-y-1.5 border-t border-[#c1c9b6]/40 pt-3">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-[#717a69]">work</span>
                        <span class="truncate">{{ $contract->offer?->title ?? 'Offre non renseignée' }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-[#717a69]">date_range</span>
                        <span>{{ $contract->periodLabel() }}</span>
                    </div>
                    @if($contract->duration_label)
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-sm text-[#717a69]">schedule</span>
                            <span>Durée : {{ $contract->duration_label }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($contract->isExpired())
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold">Expiré</span>
                        @endif
                        @if($contract->canRenew())
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">À renouveler</span>
                        @endif
                        @if($contract->renewed_from_contract_id)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#aef585]/40 text-[#2c6904] font-bold">Issu d’un renouvellement</span>
                        @endif
                    </div>
                    @if($contract->negotiated_salary)
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-sm text-[#717a69]">payments</span>
                            <span>{{ $contract->negotiated_salary }}</span>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-[#c1c9b6] py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">description</span>
                <p class="text-[#717a69] font-medium text-sm">Aucun contrat trouvé.</p>
            </div>
        @endforelse
    </div>

    @if($contracts->hasPages())
        <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl border border-[#c1c9b6]">
            <p class="text-xs text-[#41493b]">
                Affichage de {{ $contracts->firstItem() }} à {{ $contracts->lastItem() }} sur {{ $contracts->total() }} fiches
            </p>
            {{ $contracts->links('livewire.partials.pagination') }}
        </div>
    @endif

    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#2c6904] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>
</div>
