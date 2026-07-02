<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasUuids;

    protected $fillable = [
        'full_name', 'email', 'phone', 'profile',
        'subject', 'message', 'rgpd_consent',
        'is_read', 'read_at', 'ip_address',
        'reply_message', 'replied_at', 'replied_by',
    ];

    protected function casts(): array
    {
        return [
            'rgpd_consent' => 'boolean',
            'is_read'      => 'boolean',
            'read_at'      => 'datetime',
            'replied_at'   => 'datetime',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAsReplied(string $reply, ?string $userId = null): void
    {
        $this->update([
            'reply_message' => $reply,
            'replied_at'    => now(),
            'replied_by'    => $userId,
        ]);
    }
}
