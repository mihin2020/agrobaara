<?php

namespace App\Models\Concerns;

use App\Support\ReferentialCache;

trait FlushesReferentialCache
{
    protected static function bootFlushesReferentialCache(): void
    {
        static::saved(fn () => ReferentialCache::flush());
        static::deleted(fn () => ReferentialCache::flush());
    }
}
