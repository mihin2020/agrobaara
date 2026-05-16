<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Entreprise — Agro Eco BAARA')]
class CompanyShow extends Component
{
    public Company $company;

    public function mount(Company $company): void
    {
        $this->authorize('view', $company);
        $company->loadMissing('sites.commune', 'offers', 'createdBy');
        $this->company = $company;
    }

    public function render()
    {
        return view('livewire.companies.company-show');
    }
}
