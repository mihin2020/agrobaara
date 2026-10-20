<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\CompanyStatus;
use App\Enums\EntityType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reference', 'name', 'logo_path', 'status', 'legal_rep_first_name', 'legal_rep_last_name',
        'activity_types', 'description', 'phone', 'email', 'website', 'social_links',
        'need_training', 'need_financing', 'need_contract_support', 'other_needs', 'operator_notes',
        'user_id', 'entity_type', 'approval_status', 'approved_at', 'approved_by', 'rejection_reason',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status'               => CompanyStatus::class,
            'entity_type'          => EntityType::class,
            'approval_status'      => ApprovalStatus::class,
            'approved_at'          => 'datetime',
            'activity_types'       => 'array',
            'social_links'         => 'array',
            'need_training'        => 'boolean',
            'need_financing'       => 'boolean',
            'need_contract_support' => 'boolean',
        ];
    }

    // -----------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainedCandidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'training_center_id');
    }

    public function isTrainingCenter(): bool
    {
        return $this->entity_type === EntityType::CentreFormation;
    }

    public function sites(): HasMany
    {
        return $this->hasMany(CompanySite::class);
    }

    public function mainSite()
    {
        return $this->sites()->where('is_main', true)->first();
    }

    public function offers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function publishedOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class)->where('status', 'publiee');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopeApproved($query)
    {
        return $query->where('approval_status', ApprovalStatus::Approved);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', ApprovalStatus::Pending);
    }

    public function scopeTrainingCenters($query)
    {
        return $query->where('entity_type', EntityType::CentreFormation)
            ->approved();
    }

    public function scopeWithList($query)
    {
        return $query->with(['sites.commune']);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOperator($query, string $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeWithActiveOffers($query)
    {
        return $query->whereHas('offers', fn($q) => $q->where('status', 'publiee'));
    }

    /**
     * Permet d'ouvrir la fiche d'une entreprise archivée (soft-deleted).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->withTrashed()
            ->firstOrFail();
    }
}
