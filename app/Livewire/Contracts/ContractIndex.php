<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Contrats - Agro Eco BAARA')]
class ContractIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $candidate_id = '';

    #[Url]
    public string $company_id = '';

    #[Url]
    public string $offer_id = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Contract::class);

        $user = Auth::user();

        $contracts = Contract::withList()
            ->with(['match'])
            ->withCount('renewals')
            ->when($user->isOperator(), function ($q) use ($user) {
                $q->where(function ($inner) use ($user) {
                    $inner->where('created_by', $user->id)
                        ->orWhereHas('match', fn ($m) => $m->where('operator_id', $user->id));
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->candidate_id, fn ($q) => $q->where('candidate_id', $this->candidate_id))
            ->when($this->company_id, fn ($q) => $q->where('company_id', $this->company_id))
            ->when($this->offer_id, fn ($q) => $q->where('offer_id', $this->offer_id))
            ->latest()
            ->paginate(12);

        $statuses = ContractStatus::cases();

        return view('livewire.contracts.contract-index', compact('contracts', 'statuses'));
    }
}
