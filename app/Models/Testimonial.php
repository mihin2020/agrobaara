<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasUuids;

    protected $fillable = [
        'candidate_id', 'author_name', 'role_label', 'body',
        'status', 'published_at', 'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function publish(User $by): void
    {
        $this->update([
            'status'       => 'published',
            'published_at' => now(),
            'reviewed_by'  => $by->id,
        ]);
    }

    public function unpublish(User $by): void
    {
        $this->update([
            'status'      => 'unpublished',
            'reviewed_by' => $by->id,
        ]);
    }
}
