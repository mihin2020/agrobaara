<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'enabled', 'label', 'description'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public static function flushCache(): void
    {
        Cache::forget('feature_flags_all');
        Cache::forget('feature_flags_meta');
    }

    public static function allCached(): array
    {
        return Cache::remember('feature_flags_all', 300, function () {
            return static::query()->pluck('enabled', 'key')->map(fn ($v) => (bool) $v)->all();
        });
    }

    /** @return \Illuminate\Support\Collection<string, self> */
    public static function metaCached(): \Illuminate\Support\Collection
    {
        $rows = Cache::get('feature_flags_meta');

        if (! is_array($rows)) {
            Cache::forget('feature_flags_meta');
            $rows = static::query()->get()->map->getAttributes()->values()->all();
            Cache::put('feature_flags_meta', $rows, 300);
        }

        return static::hydrate($rows)->keyBy('key');
    }
}
