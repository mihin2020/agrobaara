<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Services\ReferenceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Contrat - Agro Eco BAARA')]
class ContractShow extends Component
{
    public Contract $contract;

    public string $newStatus = '';
    public string $negotiated_salary = '';
    public string $notes = '';
    public string $signed_at = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $duration_label = '';

    public bool $showRenewModal = false;

    /** @var string same|new */
    public string $renewMode = 'same';

    public string $renew_starts_at = '';
    public string $renew_ends_at = '';
    public string $renew_duration_label = '';
    public string $renew_negotiated_salary = '';
    public string $renew_notes = '';
    public string $renew_clauses = '';

    public function mount(Contract $contract): void
    {
        $this->authorize('view', $contract);
        $this->contract = $contract;
        $this->reloadRelations();
        $this->syncFormFromContract();
    }

    protected function reloadRelations(): void
    {
        $this->contract->loadMissing(
            'candidate.commune',
            'company',
            'offer',
            'match.operator',
            'createdBy',
            'renewedFrom',
            'renewals'
        );
    }

    protected function syncFormFromContract(): void
    {
        $this->newStatus = $this->contract->status->value;
        $this->negotiated_salary = $this->contract->negotiated_salary ?? '';
        $this->notes = $this->contract->notes ?? '';
        $this->signed_at = $this->contract->signed_at?->format('Y-m-d\TH:i') ?? '';
        $this->starts_at = $this->contract->starts_at?->format('Y-m-d') ?? '';
        $this->ends_at = $this->contract->ends_at?->format('Y-m-d') ?? '';
        $this->duration_label = $this->contract->duration_label ?? '';

        if ($this->signed_at === '' && $this->contract->status === ContractStatus::Actif) {
            $this->signed_at = now()->format('Y-m-d\TH:i');
        }

        if ($this->ends_at === '' && $this->starts_at !== '' && $this->duration_label !== '') {
            $this->recalculateEndsAt();
        }
    }

    public function updatedStartsAt(): void
    {
        $this->recalculateEndsAt();
    }

    public function updatedDurationLabel(): void
    {
        $this->recalculateEndsAt();
    }

    public function updatedNewStatus(string $value): void
    {
        if ($value === ContractStatus::Actif->value && ! $this->contract->signed_at) {
            $this->signed_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function updatedRenewMode(): void
    {
        $this->prepareRenewForm();
    }

    public function updatedRenewStartsAt(): void
    {
        $this->recalculateRenewEndsAt();
    }

    public function updatedRenewDurationLabel(): void
    {
        $this->recalculateRenewEndsAt();
    }

    public function recalculateEndsAt(): void
    {
        if ($this->starts_at === '' || trim((string) $this->duration_label) === '') {
            return;
        }

        try {
            $end = Contract::estimateEndDate(
                \Carbon\Carbon::parse($this->starts_at)->startOfDay(),
                $this->duration_label
            );
            if ($end) {
                $this->ends_at = $end->format('Y-m-d');
            }
        } catch (\Throwable) {
            // ignore
        }
    }

    protected function recalculateRenewEndsAt(): void
    {
        if ($this->renew_starts_at === '' || trim((string) $this->renew_duration_label) === '') {
            return;
        }

        try {
            $end = Contract::estimateEndDate(
                \Carbon\Carbon::parse($this->renew_starts_at)->startOfDay(),
                $this->renew_duration_label
            );
            if ($end) {
                $this->renew_ends_at = $end->format('Y-m-d');
            }
        } catch (\Throwable) {
            // ignore
        }
    }

    public function save(): void
    {
        $this->authorize('update', $this->contract);

        $this->recalculateEndsAt();

        $this->validate([
            'newStatus'         => 'required|in:brouillon,actif,clos',
            'negotiated_salary' => 'nullable|string|max:150',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after_or_equal:starts_at',
            'duration_label'    => 'nullable|string|max:150',
            'notes'             => 'nullable|string|max:5000',
            'signed_at'         => 'nullable|date',
        ], [
            'ends_at.after_or_equal' => 'La fin doit être postérieure ou égale au début.',
        ]);

        $statusEnum = ContractStatus::from($this->newStatus);

        if ($statusEnum === ContractStatus::Actif && $this->signed_at === '') {
            $this->signed_at = now()->format('Y-m-d\TH:i');
        }

        // Auto-clôturer si déjà expiré
        if ($this->ends_at !== '' && \Carbon\Carbon::parse($this->ends_at)->endOfDay()->isPast()
            && $statusEnum === ContractStatus::Actif) {
            $statusEnum = ContractStatus::Clos;
            $this->newStatus = ContractStatus::Clos->value;
        }

        $this->contract->update([
            'status'            => $statusEnum,
            'negotiated_salary' => $this->negotiated_salary ?: null,
            'starts_at'         => $this->starts_at ?: null,
            'ends_at'           => $this->ends_at ?: null,
            'duration_label'    => $this->duration_label ?: null,
            'notes'             => $this->notes ?: null,
            'signed_at'         => $this->signed_at !== '' ? $this->signed_at : null,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->contract)
            ->withProperties(['new_status' => $this->newStatus])
            ->log('contract_updated');

        $this->dispatch('notify', type: 'success', message: 'Contrat mis à jour.');
        $this->contract->refresh();
        $this->reloadRelations();
        $this->syncFormFromContract();
    }

    public function openRenewModal(): void
    {
        $this->authorize('update', $this->contract);

        if (! $this->contract->canRenew()) {
            $this->dispatch('notify', type: 'warning', message: 'Le renouvellement n’est possible qu’après expiration du contrat.');
            return;
        }

        $this->renewMode = 'same';
        $this->prepareRenewForm();
        $this->showRenewModal = true;
    }

    public function closeRenewModal(): void
    {
        $this->showRenewModal = false;
    }

    protected function prepareRenewForm(): void
    {
        $newStart = $this->contract->ends_at
            ? $this->contract->ends_at->copy()->addDay()
            : now()->startOfDay();

        $this->renew_starts_at = $newStart->format('Y-m-d');
        $this->renew_duration_label = $this->contract->duration_label ?? $this->contract->offer?->duration ?? '';
        $this->renew_negotiated_salary = $this->contract->negotiated_salary ?? '';
        $this->renew_notes = $this->contract->notes ?? '';
        $this->renew_clauses = $this->contract->clauses_snapshot
            ?? Contract::buildClausesSnapshot($this->contract->offer)
            ?? '';

        $this->recalculateRenewEndsAt();
    }

    public function confirmRenew(ReferenceService $referenceService): void
    {
        $this->authorize('update', $this->contract);

        if (! $this->contract->canRenew()) {
            $this->dispatch('notify', type: 'warning', message: 'Ce contrat ne peut pas être renouvelé.');
            return;
        }

        $keepClauses = $this->renewMode === 'same';

        if (! $keepClauses) {
            $this->validate([
                'renew_starts_at'         => 'required|date',
                'renew_ends_at'           => 'nullable|date|after_or_equal:renew_starts_at',
                'renew_duration_label'    => 'nullable|string|max:150',
                'renew_negotiated_salary' => 'nullable|string|max:150',
                'renew_notes'             => 'nullable|string|max:5000',
                'renew_clauses'           => 'nullable|string|max:20000',
            ]);
        }

        $options = [
            'keep_clauses' => $keepClauses,
            'starts_at'    => $this->renew_starts_at ?: null,
            'ends_at'      => $this->renew_ends_at ?: null,
            'duration_label' => $this->renew_duration_label ?: null,
        ];

        if (! $keepClauses) {
            $options['negotiated_salary'] = $this->renew_negotiated_salary ?: null;
            $options['notes'] = $this->renew_notes ?: null;
            $options['clauses_snapshot'] = $this->renew_clauses ?: null;
        }

        $clone = $this->contract->renew($referenceService, Auth::user(), $options);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($clone)
            ->withProperties([
                'renewed_from'  => $this->contract->id,
                'old_reference' => $this->contract->reference,
                'new_reference' => $clone->reference,
                'keep_clauses'  => $keepClauses,
                'period'        => $clone->periodLabel(),
                'candidate_id'  => $clone->candidate_id,
                'offer_id'      => $clone->offer_id,
            ])
            ->log('contract_renewed');

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->contract)
            ->withProperties(['renewed_as' => $clone->id, 'historized' => true])
            ->log('contract_expired_historized');

        $this->showRenewModal = false;

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Contrat renouvelé (' . ($clone->reference ?? '') . '). L’ancien est historisé (candidat & offre).'
        );

        $this->redirect(route('admin.contracts.show', $clone), navigate: true);
    }

    public function exportPdf()
    {
        $this->authorize('view', $this->contract);

        $this->contract->loadMissing(
            'candidate.commune',
            'company.sites.commune',
            'offer',
            'createdBy',
            'renewedFrom',
            'match'
        );

        $pdf = Pdf::loadView('pdf.contract', [
            'contract' => $this->contract,
            'history'  => $this->contract->renewalChain(),
        ])->setPaper('a4');

        $filename = 'contrat_' . ($this->contract->reference ?? $this->contract->id) . '.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render()
    {
        return view('livewire.contracts.contract-show', [
            'statuses' => ContractStatus::cases(),
            'history'  => $this->contract->renewalChain(),
        ]);
    }
}
