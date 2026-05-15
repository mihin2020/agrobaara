<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOffer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reference', 'company_id', 'title', 'contract_type', 'duration',
        'economic_conditions', 'mission_description', 'locations', 'other_requirements',
        'start_date', 'positions_count', 'status', 'published_at', 'archived_at',
        'created_by', 'published_by',
    ];

    protected function casts(): array
    {
        return [
            'contract_type'   => ContractType::class,
            'status'          => OfferStatus::class,
            'locations'       => 'array',
            'start_date'      => 'date',
            'published_at'    => 'datetime',
            'archived_at'     => 'datetime',
            'positions_count' => 'integer',
        ];
    }

    // -----------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(ReferentialSkill::class, 'offer_skills', 'offer_id', 'skill_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class, 'offer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('status', OfferStatus::Publiee);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', OfferStatus::Brouillon);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', OfferStatus::Archivee);
    }

    public function scopeByOperator($query, string $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%");
        });
    }

    public function scopeByContractType($query, string $type)
    {
        return $query->where('contract_type', $type);
    }

    public function scopeBySkills($query, array $skillIds)
    {
        return $query->whereHas('skills', fn($q) => $q->whereIn('referentials_skills.id', $skillIds));
    }

    // -----------------------------------------------------------------
    // Business logic
    // -----------------------------------------------------------------

    public function publish(User $publishedBy): void
    {
        $this->update([
            'status'       => OfferStatus::Publiee,
            'published_at' => now(),
            'published_by' => $publishedBy->id,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status'      => OfferStatus::Archivee,
            'archived_at' => now(),
        ]);
    }

    public function isDraft(): bool
    {
        return $this->status === OfferStatus::Brouillon;
    }

    public function isPublished(): bool
    {
        return $this->status === OfferStatus::Publiee;
    }
}
