<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LandingSection extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug',
        'type',
        'title',
        'content',
        'draft_content',
        'order_index',
        'is_active',
        'always_visible',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'content'        => 'array',
            'draft_content'  => 'array',
            'is_active'      => 'boolean',
            'always_visible' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public static function flushCache(): void
    {
        Cache::forget('landing_sections_ordered');
        Cache::forget('landing_sections_by_slug');
    }

    public static function forSlug(string $slug): ?self
    {
        return static::allOrdered()->firstWhere('slug', $slug);
    }

    /**
     * Cache des attributs (tableaux) — jamais d'instances Eloquent sérialisées
     * (évite __PHP_Incomplete_Class avec le store database/file).
     */
    public static function allOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        $rows = Cache::get('landing_sections_ordered');

        // Ancien cache = Collection / Incomplete_Class / autre → rebuild
        if (! is_array($rows)) {
            Cache::forget('landing_sections_ordered');
            $rows = static::orderBy('order_index')->get()->map->getAttributes()->values()->all();
            Cache::put('landing_sections_ordered', $rows, 300);
        }

        return static::hydrate($rows);
    }

    public function hasDraft(): bool
    {
        return ! empty($this->draft_content);
    }

    public function publish(): void
    {
        $this->update([
            'content'       => $this->draft_content ?? $this->content,
            'draft_content' => null,
        ]);
    }
}
