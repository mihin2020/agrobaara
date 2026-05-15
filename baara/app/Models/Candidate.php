<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\TransportMode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reference', 'first_name', 'last_name', 'gender', 'marital_status',
        'birth_date', 'birth_place', 'nationality', 'commune_id', 'address',
        'transport_mode', 'phone', 'phone_secondary', 'email', 'education_level',
        'agro_training_text', 'agro_training_place', 'other_skills_text',
        'has_previous_jobs', 'need_types', 'need_employment_types', 'need_formation_types',
        'need_training', 'need_financing', 'need_cv_support', 'operator_notes',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'gender'         => Gender::class,
            'transport_mode' => TransportMode::class,
            'birth_date'      => 'date',
            'has_previous_jobs' => 'boolean',
            'need_training'   => 'boolean',
            'need_financing'  => 'boolean',
            'need_cv_support' => 'boolean',
            'need_types'             => 'array',
            'need_employment_types'  => 'array',
            'need_formation_types'   => 'array',
        ];
    }

    // -----------------------------------------------------------------
    // Accessors
    // -----------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return mb_strtoupper($this->last_name) . ' ' . ucfirst(mb_strtolower($this->first_name));
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? now()->diffInYears($this->birth_date) : 0;
    }

    // -----------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(ReferentialEducationLevel::class, 'education_level', 'code');
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(ReferentialCommune::class, 'commune_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(ReferentialLanguage::class, 'candidate_languages', 'candidate_id', 'language_id');
    }

    public function licenses(): BelongsToMany
    {
        return $this->belongsToMany(ReferentialLicense::class, 'candidate_licenses', 'candidate_id', 'license_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(ReferentialSkill::class, 'candidate_skills', 'candidate_id', 'skill_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class);
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopeByCommune($query, string $communeId)
    {
        return $query->where('commune_id', $communeId);
    }

    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByEducation($query, string $level)
    {
        return $query->where('education_level', $level);
    }

    public function scopeBySkills($query, array $skillIds)
    {
        return $query->whereHas('skills', fn($q) => $q->whereIn('referentials_skills.id', $skillIds));
    }

    public function scopeByOperator($query, string $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
