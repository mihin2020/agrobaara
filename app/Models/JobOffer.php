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
        'reference', 'company_id', 'title', 'contract_type', 'duration', 'is_periodic',
        'economic_conditions', 'salary_range', 'mission_description', 'locations', 'other_requirements',
        'start_date', 'end_date', 'positions_count', 'status', 'published_at', 'archived_at',
        'created_by', 'published_by', 'renewed_from_offer_id',
    ];

    protected function casts(): array
    {
        return [
            'contract_type'   => ContractType::class,
            'status'          => OfferStatus::class,
            'locations'       => 'array',
            'start_date'      => 'date',
            'end_date'        => 'date',
            'published_at'    => 'datetime',
            'archived_at'     => 'datetime',
            'positions_count' => 'integer',
            'is_periodic'     => 'boolean',
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

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'offer_id');
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

    public function scopePendingValidation($query)
    {
        return $query->where('status', OfferStatus::EnAttente);
    }

    public function scopeWithList($query)
    {
        return $query->with('company')->withCount('matches');
    }

    public function scopeWithDetail($query)
    {
        return $query->with([
            'company.sites',
            'skills',
            'matches.candidate.commune',
            'matches.candidate.educationLevel',
            'createdBy',
            'contracts.candidate',
            'contracts.renewedFrom',
            'renewedFrom',
            'renewals',
        ]);
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

    public function unarchive(): void
    {
        $this->update([
            'status'      => OfferStatus::Brouillon,
            'archived_at' => null,
        ]);
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'renewed_from_offer_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(self::class, 'renewed_from_offer_id');
    }

    /**
     * Détecte une offre périodique à partir du libellé de durée.
     */
    public static function detectPeriodicFromDuration(?string $duration): bool
    {
        if (! $duration) {
            return false;
        }

        $d = mb_strtolower($duration);

        return (bool) preg_match(
            '/renouvel|p[eé]riod|saison|r[eé]current|annuel|chaque\s+an|chaque\s+saison/',
            $d
        );
    }

    public function isPeriodic(): bool
    {
        return (bool) $this->is_periodic || static::detectPeriodicFromDuration($this->duration);
    }

    /**
     * Période renseignée (début + fin, ou début + durée calculable).
     */
    public function hasPeriod(): bool
    {
        if (! $this->start_date) {
            return false;
        }

        if ($this->end_date) {
            return true;
        }

        return $this->duration
            && \App\Models\Contract::estimateEndDate($this->start_date->copy()->startOfDay(), $this->duration) !== null;
    }

    public function resolvedEndDate(): ?\Carbon\Carbon
    {
        if ($this->end_date) {
            return $this->end_date->copy();
        }

        if ($this->start_date && $this->duration) {
            return \App\Models\Contract::estimateEndDate($this->start_date->copy()->startOfDay(), $this->duration);
        }

        return null;
    }

    public function isExpired(): bool
    {
        $end = $this->resolvedEndDate();

        return $end !== null && $end->copy()->endOfDay()->isPast();
    }

    public function periodLabel(): string
    {
        $end = $this->resolvedEndDate();

        if ($this->start_date && $end) {
            return $this->start_date->format('d/m/Y') . ' → ' . $end->format('d/m/Y');
        }
        if ($this->start_date) {
            return 'À partir du ' . $this->start_date->format('d/m/Y');
        }

        return 'Période non définie';
    }

    /**
     * Relance possible uniquement si : périodique + période définie + (archivée OU expirée) + pas déjà relancée.
     */
    public function canRelaunch(): bool
    {
        if (! $this->isPeriodic()) {
            return false;
        }

        if (! $this->hasPeriod()) {
            return false;
        }

        $archivedOrExpired = $this->isArchived() || $this->isExpired();
        if (! $archivedOrExpired) {
            return false;
        }

        if (array_key_exists('renewals_count', $this->getAttributes())) {
            return (int) $this->renewals_count === 0;
        }

        if ($this->relationLoaded('renewals')) {
            return $this->renewals->isEmpty();
        }

        return ! $this->renewals()->exists();
    }

    public function renew(\App\Services\ReferenceService $referenceService, ?\App\Models\User $createdBy = null): self
    {
        return $this->relaunch($referenceService, $createdBy, [
            'match_mode' => 'fresh',
            'archive_old' => false,
        ]);
    }

    /**
     * Relance une offre (périodique) : clone + historisation de l’ancienne.
     *
     * @param  array{match_mode?: string, archive_old?: bool, start_date?: ?string, keep_periodic?: bool}  $options
     *         match_mode: fresh = nouveau matching | reconduce = reprendre candidats acceptés / sous contrat
     */
    public function relaunch(
        \App\Services\ReferenceService $referenceService,
        ?\App\Models\User $createdBy = null,
        array $options = []
    ): self {
        $this->loadMissing('skills', 'matches', 'contracts');

        $matchMode = $options['match_mode'] ?? 'fresh';
        $archiveOld = $options['archive_old'] ?? true;
        $keepPeriodic = $options['keep_periodic'] ?? true;

        $startDate = $options['start_date'] ?? null;
        if (! $startDate && $this->duration) {
            $from = $this->start_date?->copy() ?? now();
            $estimated = \App\Models\Contract::estimateEndDate($from->copy()->startOfDay(), $this->duration);
            $startDate = $estimated?->addDay()->toDateString();
        }

        $newStart = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;
        $newEnd = $newStart && $this->duration
            ? \App\Models\Contract::estimateEndDate($newStart, $this->duration)
            : null;

        $clone = self::create([
            'reference'             => $referenceService->generateOfferReference(),
            'company_id'            => $this->company_id,
            'title'                 => $this->title,
            'contract_type'         => $this->contract_type,
            'duration'              => $this->duration,
            'is_periodic'           => $keepPeriodic ? true : (bool) $this->is_periodic,
            'economic_conditions'   => $this->economic_conditions,
            'salary_range'          => $this->salary_range,
            'mission_description'   => $this->mission_description,
            'locations'             => $this->locations,
            'other_requirements'    => $this->other_requirements,
            'start_date'            => $newStart?->toDateString(),
            'end_date'              => $newEnd?->toDateString(),
            'positions_count'       => $this->positions_count,
            'status'                => OfferStatus::Brouillon,
            'created_by'            => $createdBy?->id ?? $this->created_by,
            'renewed_from_offer_id' => $this->id,
        ]);

        if ($this->skills->isNotEmpty()) {
            $clone->skills()->sync($this->skills->pluck('id'));
        }

        if ($matchMode === 'reconduce') {
            $candidateIds = $this->matches()
                ->where('status', \App\Enums\MatchStatus::Acceptee->value)
                ->pluck('candidate_id')
                ->merge($this->contracts()->pluck('candidate_id'))
                ->unique()
                ->filter();

            foreach ($candidateIds as $candidateId) {
                if (CandidateMatch::where('candidate_id', $candidateId)->where('offer_id', $clone->id)->exists()) {
                    continue;
                }

                $previous = $this->matches()
                    ->where('candidate_id', $candidateId)
                    ->latest()
                    ->first();

                CandidateMatch::create([
                    'candidate_id' => $candidateId,
                    'offer_id'     => $clone->id,
                    'status'       => \App\Enums\MatchStatus::Proposee,
                    'operator_id'  => $createdBy?->id ?? $this->created_by,
                    'score'        => $previous?->score,
                    'proposed_at'  => now(),
                    'notes'        => 'Reconduction depuis l’offre ' . ($this->reference ?? $this->id)
                        . ' (candidat ayant exécuté / matching accepté).',
                ]);
            }
        }

        if ($archiveOld && ! $this->isArchived()) {
            $this->archive();
        }

        return $clone;
    }

    public function isDraft(): bool
    {
        return $this->status === OfferStatus::Brouillon;
    }

    public function isPublished(): bool
    {
        return $this->status === OfferStatus::Publiee;
    }

    public function isArchived(): bool
    {
        return $this->status === OfferStatus::Archivee;
    }
}
