<?php

namespace App\Livewire\Companies;

use App\Enums\CompanyStatus;
use App\Exports\CompaniesExport;
use App\Models\Company;
use App\Models\ReferentialCommune;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Entreprises — Agro Eco BAARA')]
class CompanyIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';
    public string $exportFrom = '';
    public string $exportTo = '';
    public string $exportCommune = '';
    public string $exportStatus = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new CompaniesExport(
                from: $this->exportFrom ?: null,
                to: $this->exportTo ?: null,
                commune: $this->exportCommune ?: null,
                status: $this->exportStatus ?: null,
            ),
            'entreprises_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $this->authorize('viewAny', Company::class);

        $user = Auth::user();

        $companies = Company::with(['sites.commune'])
            ->when($user->isOperator(), fn($q) => $q->byOperator($user->id))
            ->when($this->search, fn($q) => $q->search($this->search))
            ->withCount('publishedOffers')
            ->latest()
            ->paginate(10);

        $communes = ReferentialCommune::active()->get();
        $statuses = collect(CompanyStatus::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]);

        return view('livewire.companies.company-index', compact('companies', 'communes', 'statuses'));
    }
}
