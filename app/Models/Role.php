<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withTimestamps();
    }

    public function syncPermissions(array $permissionSlugs): void
    {
        $ids = Permission::whereIn('slug', $permissionSlugs)->pluck('id');
        $this->permissions()->sync($ids);
        $this->load('permissions');
    }

    public function givePermission(string $slug): void
    {
        $permission = Permission::where('slug', $slug)->firstOrFail();
        if (!$this->permissions->contains('id', $permission->id)) {
            $this->permissions()->attach($permission->id);
        }
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }
}
