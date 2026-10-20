<?php

namespace App\Livewire\Admin\Registrations;

use App\Enums\ApprovalStatus;
use App\Enums\UserStatus;
use App\Models\Candidate;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Demandes d\'inscription - Agro Eco BAARA')]
class RegistrationIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'candidates';

    public string $rejectReason = '';

    public ?string $rejectId = null;

    public string $rejectType = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->hasPermission('registrations.view'), 403);
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function approveCandidate(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('registrations.approve'), 403);
        $c = Candidate::findOrFail($id);
        $c->update([
            'approval_status'  => ApprovalStatus::Approved,
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
            'rejection_reason' => null,
        ]);
        $c->user?->update(['status' => UserStatus::Active]);
        session()->flash('success', 'Candidat approuvé.');
    }

    public function approveCompany(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('registrations.approve'), 403);
        $c = Company::findOrFail($id);
        $c->update([
            'approval_status'  => ApprovalStatus::Approved,
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
            'rejection_reason' => null,
        ]);
        $c->user?->update(['status' => UserStatus::Active]);
        session()->flash('success', 'Organisation approuvée.');
    }

    public function openReject(string $type, string $id): void
    {
        $this->rejectType = $type;
        $this->rejectId = $id;
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        abort_unless(Auth::user()->hasPermission('registrations.reject'), 403);
        $this->validate(['rejectReason' => 'required|string|min:5|max:500']);

        if ($this->rejectType === 'candidate') {
            $c = Candidate::findOrFail($this->rejectId);
            $c->update([
                'approval_status'  => ApprovalStatus::Rejected,
                'rejection_reason' => $this->rejectReason,
                'approved_by'      => Auth::id(),
            ]);
            $c->user?->update(['status' => UserStatus::Inactive]);
        } else {
            $c = Company::findOrFail($this->rejectId);
            $c->update([
                'approval_status'  => ApprovalStatus::Rejected,
                'rejection_reason' => $this->rejectReason,
                'approved_by'      => Auth::id(),
            ]);
            $c->user?->update(['status' => UserStatus::Inactive]);
        }

        $this->rejectId = null;
        session()->flash('success', 'Demande refusée.');
    }

    public function render()
    {
        return view('livewire.admin.registrations.registration-index', [
            'candidates' => Candidate::with('user:id,email,status')
                ->pendingApproval()
                ->latest()
                ->paginate(15, pageName: 'candPage'),
            'companies' => Company::with('user:id,email,status')
                ->pendingApproval()
                ->latest()
                ->paginate(15, pageName: 'orgPage'),
        ]);
    }
}
