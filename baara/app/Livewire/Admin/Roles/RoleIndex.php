<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Rôles & Permissions — Agro Eco BAARA')]
class RoleIndex extends Component
{
    // Edition permissions
    public ?string $editingRoleId       = null;
    public array   $checkedPermissions  = [];

    // Création de rôle
    public bool   $showCreateModal  = false;
    public string $newRoleName      = '';
    public string $newRoleDesc      = '';

    // Suppression
    public ?string $confirmingDeleteId = null;

    public function mount(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
    }

    // ── Edition permissions ────────────────────────────────────────────────────

    public function editRole(string $roleId): void
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        if ($role->slug === 'super_admin') {
            session()->flash('error', 'Le rôle super_admin ne peut pas être modifié.');
            return;
        }

        $this->editingRoleId     = $roleId;
        $this->checkedPermissions = $role->permissions->pluck('id')->toArray();
    }

    public function saveRolePermissions(): void
    {
        $role = Role::findOrFail($this->editingRoleId);

        if ($role->slug === 'super_admin') {
            return;
        }

        $role->permissions()->sync($this->checkedPermissions);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->withProperties(['permissions_count' => count($this->checkedPermissions)])
            ->log('role_permissions_updated');

        $this->reset(['editingRoleId', 'checkedPermissions']);
        session()->flash('success', 'Permissions mises à jour.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingRoleId', 'checkedPermissions']);
    }

    public function selectGroup(string $group): void
    {
        $ids = Permission::where('group', $group)->pluck('id')->toArray();
        $this->checkedPermissions = array_unique(array_merge($this->checkedPermissions, $ids));
    }

    public function deselectGroup(string $group): void
    {
        $ids = Permission::where('group', $group)->pluck('id')->toArray();
        $this->checkedPermissions = array_values(array_diff($this->checkedPermissions, $ids));
    }

    public function selectAll(): void
    {
        $this->checkedPermissions = Permission::pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->checkedPermissions = [];
    }

    // ── Création de rôle ──────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset(['newRoleName', 'newRoleDesc']);
        $this->showCreateModal = true;
    }

    public function createRole(): void
    {
        $this->validate([
            'newRoleName' => 'required|string|min:2|max:100|unique:roles,name',
        ], [
            'newRoleName.required' => 'Le nom du rôle est obligatoire.',
            'newRoleName.unique'   => 'Un rôle avec ce nom existe déjà.',
            'newRoleName.min'      => 'Le nom doit contenir au moins 2 caractères.',
        ]);

        $role = Role::create([
            'name'        => $this->newRoleName,
            'slug'        => Str::slug($this->newRoleName, '_'),
            'description' => $this->newRoleDesc ?: null,
            'is_system'   => false,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('role_created');

        $this->showCreateModal = false;
        $this->reset(['newRoleName', 'newRoleDesc']);
        session()->flash('success', "Rôle \"{$role->name}\" créé. Vous pouvez maintenant lui attribuer des permissions.");

        // Ouvre directement l'éditeur de permissions du nouveau rôle
        $this->editRole($role->id);
    }

    // ── Suppression de rôle ───────────────────────────────────────────────────

    public function confirmDelete(string $roleId): void
    {
        $role = Role::findOrFail($roleId);

        if ($role->is_system) {
            session()->flash('error', 'Les rôles système ne peuvent pas être supprimés.');
            return;
        }

        $this->confirmingDeleteId = $roleId;
    }

    public function deleteRole(): void
    {
        $role = Role::withCount('users')->findOrFail($this->confirmingDeleteId);

        if ($role->is_system) {
            session()->flash('error', 'Les rôles système ne peuvent pas être supprimés.');
            $this->confirmingDeleteId = null;
            return;
        }

        if ($role->users_count > 0) {
            session()->flash('error', "Ce rôle est assigné à {$role->users_count} utilisateur(s). Réassignez-les d'abord.");
            $this->confirmingDeleteId = null;
            return;
        }

        $roleName = $role->name;
        $role->permissions()->detach();
        $role->delete();

        activity()
            ->causedBy(Auth::user())
            ->log("role_deleted: {$roleName}");

        $this->reset(['confirmingDeleteId', 'editingRoleId', 'checkedPermissions']);
        session()->flash('success', "Rôle \"{$roleName}\" supprimé.");
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $roles          = Role::with('permissions')->withCount('users')->orderBy('is_system', 'desc')->get();
        $allPermissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('livewire.admin.roles.role-index', compact('roles', 'allPermissions'));
    }
}