<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReferentialEmploymentType extends Model
{
    use Concerns\FlushesReferentialCache, HasUuids;

    protected $table = 'referentials_employment_types';

    protected $fillable = ['code', 'name', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
