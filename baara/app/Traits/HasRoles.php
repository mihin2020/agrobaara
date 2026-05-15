<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withTimestamps();
    }

    // -----------------------------------------------------------------
    // Role checks
    // -----------------------------------------------------------------

    public function hasRole(string|UserRole $role): bool
    {
        $slug = $role instanceof UserRole ? $role->value : $role;

        return $this->roles->contains('slug', $slug);
    }

    public function hasAnyRole(array $roles): bool
    {
        $slugs = array_map(
            fn($r) => $r instanceof UserRole ? $r->value : $r,
            $roles
        );

        return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SuperAdmin);
    }

    public function isModerator(): bool
    {
        return $this->hasRole(UserRole::Moderateur);
    }

    public function isOperator(): bool
    {
        return $this->hasRole(UserRole::Operateur);
    }

    // -----------------------------------------------------------------
    // Permission checks (direct + via roles)
    // -----------------------------------------------------------------

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Direct user permission
        if ($this->permissions->contains('slug', $permission)) {
            return true;
        }

        // Via roles
        return $this->roles->flatMap(
            fn(Role $role) => $role->permissions
        )->contains('slug', $permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    // -----------------------------------------------------------------
    // Assign / revoke
    // -----------------------------------------------------------------

    public function assignRole(string|UserRole $role): void
    {
        $slug = $role instanceof UserRole ? $role->value : $role;
        $roleModel = Role::where('slug', $slug)->firstOrFail();

        if (!$this->roles->contains('id', $roleModel->id)) {
            $this->roles()->attach($roleModel->id);
            $this->load('roles.permissions', 'permissions');
        }
    }

    public function removeRole(string|UserRole $role): void
    {
        $slug = $role instanceof UserRole ? $role->value : $role;
        $roleModel = Role::where('slug', $slug)->first();

        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
            $this->load('roles.permissions', 'permissions');
        }
    }

    public function syncRoles(array $roles): void
    {
        $slugs = array_map(
            fn($r) => $r instanceof UserRole ? $r->value : $r,
            $roles
        );

        $ids = Role::whereIn('slug', $slugs)->pluck('id');
        $this->roles()->sync($ids);
        $this->load('roles.permissions', 'permissions');
    }

    public function givePermission(string $permission): void
    {
        $perm = Permission::where('slug', $permission)->firstOrFail();

        if (!$this->permissions->contains('id', $perm->id)) {
            $this->permissions()->attach($perm->id);
            $this->load('permissions');
        }
    }

    public function revokePermission(string $permission): void
    {
        $perm = Permission::where('slug', $permission)->first();

        if ($perm) {
            $this->permissions()->detach($perm->id);
            $this->load('permissions');
        }
    }

    // -----------------------------------------------------------------
    // All effective permissions (roles + direct)
    // -----------------------------------------------------------------

    public function allPermissions(): Collection
    {
        $fromRoles = $this->roles->flatMap(
            fn(Role $role) => $role->permissions
        );

        return $fromRoles->merge($this->permissions)->unique('slug');
    }

    // -----------------------------------------------------------------
    // Eager load helper
    // -----------------------------------------------------------------

    public function loadRolesAndPermissions(): static
    {
        return $this->load('roles.permissions', 'permissions');
    }
}
