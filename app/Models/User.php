<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'              => 'hashed',
            'status'                => UserStatus::class,
            'last_login_at'         => 'datetime',
            'locked_until'          => 'datetime',
            'failed_login_attempts' => 'integer',
        ];
    }

    // -----------------------------------------------------------------
    // Accessors
    // -----------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::Inactive);
    }

    public function scopeLocked($query)
    {
        return $query->where('status', UserStatus::Locked);
    }

    // -----------------------------------------------------------------
    // Business logic helpers
    // -----------------------------------------------------------------

    public function isLocked(): bool
    {
        if ($this->status === UserStatus::Locked && $this->locked_until) {
            return now()->lessThan($this->locked_until);
        }

        return false;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function needsPasswordChange(): bool
    {
        return $this->status === UserStatus::PendingPassword;
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');

        if ($this->failed_login_attempts >= 5) {
            $this->update([
                'status'       => UserStatus::Locked,
                'locked_until' => now()->addMinutes(10),
            ]);
        }
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }

    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at'         => now(),
            'last_login_ip'         => $ip,
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }
}
