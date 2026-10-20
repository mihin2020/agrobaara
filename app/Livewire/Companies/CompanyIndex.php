<?php

namespace App\Livewire\Companies;

use App\Enums\CompanyStatus;
use App\Enums\OfferStatus;
use App\Exports\CompaniesExport;
use App\Models\Company;
use App\Support\ReferentialCache;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Entreprises - Agro Eco BAARA')]
class CompanyIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public bool $showArchived = false;

    public string $exportFrom = '';
    public string $exportTo = '';
    public string $exportCommune = '';
    public string $exportStatus = '';

    public ?string $archivingCompanyId = null;

    /** @var string company_only|company_and_offers */
    public string $archiveMode = 'company_only';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedShowArchived(): void
    {
        $this->resetPage();
        $this->closeArchiveModal();
    }

    public function export()
    {
        $this->authorize('export', Company::class);

        $user = Auth::user();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new CompaniesExport(
                from: $this->exportFrom ?: null,
                to: $this->exportTo ?: null,
                commune: $this->exportCommune ?: null,
                status: $this->exportStatus ?: null,
                operatorId: $user->isOperator() ? $user->id : null,
            ),
            'entreprises_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function openArchiveModal(string $companyId): void
    {
        $company = Company::findOrFail($companyId);
        $this->authorize('delete', $company);
        $this->archivingCompanyId = $companyId;
        $this->archiveMode = 'company_only';
    }

    public function closeArchiveModal(): void
    {
        $this->archivingCompanyId = null;
        $this->archiveMode = 'company_only';
    }

    public function confirmArchive(): void
    {
        if (! $this->archivingCompanyId) {
            return;
        }

        $company = Company::with('offers')->findOrFail($this->archivingCompanyId);
        $this->authorize('delete', $company);

        if ($this->archiveMode === 'company_and_offers') {
            $company->offers()
                ->where('status', '!=', OfferStatus::Archivee->value)
                ->get()
                ->each(fn ($offer) => $offer->archive());
        }

        $company->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($company)
            ->withProperties(['with_offers' => $this->archiveMode === 'company_and_offers'])
            ->log('company_archived');

        $this->closeArchiveModal();
        $this->dispatch('notify', type: 'success', message: 'Entreprise archivée.');
    }

    public function restoreCompany(string $companyId): void
    {
        $company = Company::onlyTrashed()->findOrFail($companyId);
        $this->authorize('restore', $company);
        $company->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($company)
            ->log('company_restored');

        $this->dispatch('notify', type: 'success', message: 'Entreprise restaurée.');
    }

    public function render()
    {
        $this->authorize('viewAny', Company::class);

        $user = Auth::user();

        $companies = Company::query()
            ->when($this->showArchived, fn ($q) => $q->onlyTrashed(), fn ($q) => $q->withoutTrashed())
            ->withList()
            ->when($user->isOperator(), fn ($q) => $q->byOperator($user->id))
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->withCount('publishedOffers')
            ->latest()
            ->paginate(10);

        $communes = ReferentialCache::communes();
        $statuses = collect(CompanyStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]);

        $archivingCompany = $this->archivingCompanyId
            ? Company::withCount('offers')->find($this->archivingCompanyId)
            : null;

        return view('livewire.companies.company-index', compact('companies', 'communes', 'statuses', 'archivingCompany'));
    }
}
