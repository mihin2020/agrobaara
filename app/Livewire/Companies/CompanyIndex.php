<?php

namespace App\Livewire\Companies;

use App\Models\Company;
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

    public function updatedSearch(): void { $this->resetPage(); }

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

        return view('livewire.companies.company-index', compact('companies'));
    }
}
