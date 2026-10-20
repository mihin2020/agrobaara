<?php

namespace App\Livewire\Companies;

use App\Enums\OfferStatus;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Entreprise - Agro Eco BAARA')]
class CompanyShow extends Component
{
    public Company $company;

    public bool $showArchiveModal = false;

    /** @var string company_only|company_and_offers */
    public string $archiveMode = 'company_only';

    public function mount(Company $company): void
    {
        // Recharger avec soft-deletes pour permettre la fiche des entreprises archivées
        $model = Company::withTrashed()->findOrFail($company->getKey());
        $this->authorize('view', $model);
        $model->loadMissing('sites.commune', 'createdBy');
        $model->load([
            'offers' => fn ($q) => $q->latest()->limit(50),
        ]);
        $model->loadCount(['contracts', 'offers']);
        $this->company = $model;
    }

    public function openArchiveModal(): void
    {
        $this->authorize('delete', $this->company);
        $this->archiveMode = 'company_only';
        $this->showArchiveModal = true;
    }

    public function closeArchiveModal(): void
    {
        $this->showArchiveModal = false;
    }

    public function confirmArchive(): void
    {
        $this->authorize('delete', $this->company);

        if ($this->company->trashed()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette entreprise est déjà archivée.');
            $this->showArchiveModal = false;
            return;
        }

        if ($this->archiveMode === 'company_and_offers') {
            $this->company->offers()
                ->where('status', '!=', OfferStatus::Archivee->value)
                ->get()
                ->each(fn ($offer) => $offer->archive());
        }

        $this->company->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->company)
            ->withProperties(['with_offers' => $this->archiveMode === 'company_and_offers'])
            ->log('company_archived');

        $this->showArchiveModal = false;
        $this->dispatch('notify', type: 'success', message: 'Entreprise archivée.');

        $this->redirect(route('admin.companies.index'), navigate: true);
    }

    public function restore(): void
    {
        $this->authorize('restore', $this->company);

        if (! $this->company->trashed()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette entreprise n\'est pas archivée.');
            return;
        }

        $this->company->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->company)
            ->log('company_restored');

        $this->company->refresh();
        $this->company->loadMissing('sites.commune', 'createdBy');
        $this->company->load([
            'offers' => fn ($q) => $q->latest()->limit(50),
        ]);
        $this->company->loadCount(['contracts', 'offers']);

        $this->dispatch('notify', type: 'success', message: 'Entreprise restaurée.');
    }

    public function render()
    {
        return view('livewire.companies.company-show');
    }
}
