<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Utilisateurs — Agro Eco BAARA')]
class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool   $confirmingDelete = false;
    public ?string $deleteUserId = null;

    public function updatedSearch(): void { $this->resetPage(); }

    public function confirmDelete(string $userId): void
    {
        $this->authorize('delete', User::findOrFail($userId));
        $this->deleteUserId    = $userId;
        $this->confirmingDelete = true;
    }

    public function deleteUser(): void
    {
        if (!$this->deleteUserId) return;

        $user = User::findOrFail($this->deleteUserId);
        $this->authorize('delete', $user);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->reset(['confirmingDelete', 'deleteUserId']);
            return;
        }

        $user->delete();
        activity()->causedBy(Auth::user())->performedOn($user)->log('user_deleted');

        $this->reset(['confirmingDelete', 'deleteUserId']);
        session()->flash('success', 'Utilisateur supprimé.');
    }

    public function toggleStatus(string $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->status === UserStatus::Active) {
            $user->update(['status' => UserStatus::Inactive]);
        } else {
            $user->update(['status' => UserStatus::Active]);
        }

        activity()->causedBy(Auth::user())->performedOn($user)->log('user_status_toggled');
    }

    public function unlockUser(string $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update([
            'status'                => UserStatus::Active,
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);

        activity()->causedBy(Auth::user())->performedOn($user)->log('user_unlocked');
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.users.user-index', compact('users'));
    }
}
