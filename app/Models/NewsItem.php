<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'title', 'slug', 'kind', 'excerpt', 'body',
        'starts_at', 'ends_at', 'published_at', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (NewsItem $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title) . '-' . Str::lower(Str::random(4));
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeRibbon($query)
    {
        return $query->published()
            ->whereIn('kind', ['annonce', 'evenement'])
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('published_at');
    }

    public function kindLabel(): string
    {
        return match ($this->kind) {
            'actu'       => 'Actualité',
            'annonce'    => 'Annonce',
            'evenement'  => 'Événement',
            default      => $this->kind,
        };
    }
}
