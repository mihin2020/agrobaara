<div class="space-y-6">
    <x-page-header title="Demandes d'inscription" />

    <div class="flex gap-2">
        <button type="button" wire:click="$set('tab','candidates')"
                class="px-4 py-2 rounded-xl text-sm font-semibold {{ $tab === 'candidates' ? 'bg-[#2c6904] text-white' : 'bg-white border border-[#c1c9b6]' }}">
            Candidats ({{ $candidates->total() }})
        </button>
        <button type="button" wire:click="$set('tab','companies')"
                class="px-4 py-2 rounded-xl text-sm font-semibold {{ $tab === 'companies' ? 'bg-[#2c6904] text-white' : 'bg-white border border-[#c1c9b6]' }}">
            Organisations ({{ $companies->total() }})
        </button>
    </div>

    @if($tab === 'candidates')
        <div class="bg-white border border-[#c1c9b6] rounded-2xl divide-y divide-[#c1c9b6]/50">
            @forelse($candidates as $c)
                <div class="p-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ $c->full_name }}</p>
                        <p class="text-xs text-[#717a69]">{{ $c->email }} · {{ $c->phone }} · {{ $c->reference }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="approveCandidate('{{ $c->id }}')" class="px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg">Approuver</button>
                        <button wire:click="openReject('candidate','{{ $c->id }}')" class="px-3 py-1.5 border border-red-300 text-red-700 text-xs font-semibold rounded-lg">Refuser</button>
                    </div>
                </div>
            @empty
                <x-empty-state icon="person_off" title="Aucune demande candidat." />
            @endforelse
        </div>
        @if($candidates->hasPages())
            <div>{{ $candidates->links() }}</div>
        @endif
    @else
        <div class="bg-white border border-[#c1c9b6] rounded-2xl divide-y divide-[#c1c9b6]/50">
            @forelse($companies as $c)
                <div class="p-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ $c->name }}</p>
                        <p class="text-xs text-[#717a69]">{{ $c->entity_type?->label() }} · {{ $c->email }} · {{ $c->reference }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="approveCompany('{{ $c->id }}')" class="px-3 py-1.5 bg-[#2c6904] text-white text-xs font-semibold rounded-lg">Approuver</button>
                        <button wire:click="openReject('company','{{ $c->id }}')" class="px-3 py-1.5 border border-red-300 text-red-700 text-xs font-semibold rounded-lg">Refuser</button>
                    </div>
                </div>
            @empty
                <x-empty-state icon="domain_disabled" title="Aucune demande organisation." />
            @endforelse
        </div>
        @if($companies->hasPages())
            <div>{{ $companies->links() }}</div>
        @endif
    @endif

    @if($rejectId)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full space-y-4">
                <h2 class="font-semibold">Motif du refus</h2>
                <textarea wire:model="rejectReason" rows="3" class="w-full border border-[#c1c9b6] rounded-xl p-3 text-sm"></textarea>
                @error('rejectReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="flex gap-2 justify-end">
                    <button wire:click="$set('rejectId', null)" class="px-3 py-2 text-sm border rounded-xl">Annuler</button>
                    <button wire:click="confirmReject" class="px-3 py-2 text-sm bg-red-600 text-white rounded-xl">Confirmer</button>
                </div>
            </div>
        </div>
    @endif
</div>
