<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateMatch extends Model
{
    use HasUuids;

    protected $table = 'matches';

    protected $fillable = [
        'candidate_id', 'offer_id', 'status', 'operator_id',
        'notes', 'score', 'proposed_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => MatchStatus::class,
            'score'       => 'float',
            'proposed_at' => 'datetime',
            'closed_at'   => 'datetime',
        ];
    }

    // -----------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class, 'offer_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [
            MatchStatus::Acceptee->value,
            MatchStatus::Refusee->value,
            MatchStatus::Abandonnee->value,
        ]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [
            MatchStatus::Acceptee->value,
            MatchStatus::Refusee->value,
            MatchStatus::Abandonnee->value,
        ]);
    }

    public function scopeByOperator($query, string $userId)
    {
        return $query->where('operator_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            MatchStatus::Proposee->value,
            MatchStatus::Contactee->value,
        ]);
    }

    // -----------------------------------------------------------------
    // Business logic
    // -----------------------------------------------------------------

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function close(MatchStatus $finalStatus, ?string $notes = null): void
    {
        $this->update([
            'status'    => $finalStatus,
            'closed_at' => now(),
            'notes'     => $notes ?? $this->notes,
        ]);
    }
}
