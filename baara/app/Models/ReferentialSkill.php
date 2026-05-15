<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReferentialSkill extends Model
{
    use HasUuids;

    protected $table = 'referentials_skills';

    protected $fillable = ['name', 'category', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidate_skills', 'skill_id', 'candidate_id');
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffer::class, 'offer_skills', 'skill_id', 'offer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('name');
    }
}
