<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\UserInvitedNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Utilisateurs - Agro Eco BAARA')]
class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Suppression
    public bool    $confirmingDelete = false;
    public ?string $deleteUserId     = null;

    // Changement de rôle
    public bool    $showRoleModal      = false;
    public ?string $roleModalUserId    = null;
    public string  $roleModalUserName  = '';
    public string  $selectedRoleId     = '';

    // Changement de mot de passe
    public bool    $showPasswordModal     = false;
    public ?string $passwordModalUserId   = null;
    public string  $passwordModalUserName = '';
    public string  $newPassword           = '';
    public string  $newPasswordConfirm    = '';

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Suppression ──────────────────────────────────────────────────

    public function confirmDelete(string $userId): void
    {
        $this->authorize('delete', User::findOrFail($userId));
        $this->deleteUserId     = $userId;
        $this->confirmingDelete = true;
    }

    public function deleteUser(): void
    {
        if (!$this->deleteUserId) return;

        $user = User::findOrFail($this->deleteUserId);
        $this->authorize('delete', $user);

        if ($user->is_system) {
            session()->flash('error', 'Ce compte système ne peut pas être supprimé.');
            $this->reset(['confirmingDelete', 'deleteUserId']);
            return;
        }

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

    // ── Statut ───────────────────────────────────────────────────────

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

    // ── Changement de rôle ───────────────────────────────────────────

    public function openRoleModal(string $userId): void
    {
        $me = Auth::user();
        if (!$me->hasPermission('roles.manage')) {
            abort(403);
        }

        $user = User::with('roles')->findOrFail($userId);

        // Un non-super-admin ne peut pas modifier le rôle d'un super admin
        if ($user->isSuperAdmin() && !$me->isSuperAdmin()) {
            session()->flash('error', 'Vous ne pouvez pas modifier le rôle d\'un super administrateur.');
            return;
        }

        $this->roleModalUserId   = $userId;
        $this->roleModalUserName = $user->full_name;
        $this->selectedRoleId   = $user->roles->first()?->id ?? '';
        $this->showRoleModal     = true;
    }

    public function saveRole(): void
    {
        $me = Auth::user();
        if (!$me->hasPermission('roles.manage')) abort(403);

        $this->validate([
            'selectedRoleId' => 'required|uuid|exists:roles,id',
        ], [
            'selectedRoleId.required' => 'Veuillez sélectionner un rôle.',
        ]);

        $user = User::findOrFail($this->roleModalUserId);

        if ($user->isSuperAdmin() && !$me->isSuperAdmin()) {
            abort(403);
        }

        $role = Role::findOrFail($this->selectedRoleId);

        // Garde : on ne peut pas se retirer le rôle super_admin à soi-même
        if ($user->id === $me->id && $me->isSuperAdmin() && $role->slug !== 'super_admin') {
            session()->flash('error', 'Vous ne pouvez pas retirer votre propre rôle super administrateur.');
            $this->showRoleModal = false;
            return;
        }

        $user->roles()->sync([$role->id]);

        activity()
            ->causedBy($me)
            ->performedOn($user)
            ->withProperties(['new_role' => $role->name])
            ->log('user_role_changed');

        $this->reset(['showRoleModal', 'roleModalUserId', 'roleModalUserName', 'selectedRoleId']);
        session()->flash('success', "Rôle de {$user->full_name} mis à jour : {$role->name}.");
    }

    // ── Renvoi de l'invitation ────────────────────────────────────────

    public function resendInvitation(string $userId): void
    {
        $user = User::findOrFail($userId);

        $this->authorize('create', User::class);

        if ($user->is_system) {
            session()->flash('error', 'Ce compte système ne peut pas recevoir d\'invitation.');
            return;
        }

        $token = Password::createToken($user);
        $user->notify(new UserInvitedNotification($token, Auth::user()));

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log('user_invitation_resent');

        session()->flash('success', "Invitation renvoyée à {$user->email}.");
    }

    // ── Changement de mot de passe ───────────────────────────────────

    public function openPasswordModal(string $userId): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($userId);
        $this->passwordModalUserId   = $userId;
        $this->passwordModalUserName = $user->full_name;
        $this->newPassword           = '';
        $this->newPasswordConfirm    = '';
        $this->resetValidation(['newPassword', 'newPasswordConfirm']);
        $this->showPasswordModal = true;
    }

    public function savePassword(): void
    {
        if (!Auth::user()->isSuperAdmin()) abort(403);

        $this->validate([
            'newPassword'        => 'required|string|min:8',
            'newPasswordConfirm' => 'required|same:newPassword',
        ], [
            'newPassword.required'        => 'Le mot de passe est obligatoire.',
            'newPassword.min'             => 'Minimum 8 caractères.',
            'newPasswordConfirm.required' => 'La confirmation est obligatoire.',
            'newPasswordConfirm.same'     => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::findOrFail($this->passwordModalUserId);
        $user->update(['password' => Hash::make($this->newPassword)]);

        // Si le compte était en attente de mot de passe, on l'active
        if ($user->status === UserStatus::PendingPassword) {
            $user->update(['status' => UserStatus::Active]);
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log('user_password_reset');

        $this->reset(['showPasswordModal', 'passwordModalUserId', 'passwordModalUserName', 'newPassword', 'newPasswordConfirm']);
        session()->flash('success', "Mot de passe de {$user->full_name} modifié avec succès.");
    }

    // ── Render ───────────────────────────────────────────────────────

    public function render()
    {
        $users = User::with('roles')
            ->where('is_system', false)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(10);

        $roles = Role::orderBy('name')->get();

        return view('livewire.admin.users.user-index', compact('users', 'roles'));
    }
}
