<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug',
        'name',
        'group',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')
                    ->withTimestamps();
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public static function allGrouped(): array
    {
        return static::all()
            ->groupBy('group')
            ->map(fn($perms) => $perms->values())
            ->toArray();
    }
}
