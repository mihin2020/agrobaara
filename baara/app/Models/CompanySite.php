<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySite extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id', 'label', 'commune_id', 'address', 'gps_coordinates', 'is_main',
    ];

    protected function casts(): array
    {
        return ['is_main' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(ReferentialCommune::class, 'commune_id');
    }
}
