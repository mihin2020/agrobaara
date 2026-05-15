<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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

    public static function forSlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public static function allOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::orderBy('order_index')->get();
    }

    public function hasDraft(): bool
    {
        return !empty($this->draft_content);
    }

    public function publish(): void
    {
        $this->update([
            'content'       => $this->draft_content ?? $this->content,
            'draft_content' => null,
        ]);
    }
}