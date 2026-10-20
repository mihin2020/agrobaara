<?php

namespace App\Support;

use App\Services\FeatureFlagService;

class Feature
{
    public static function enabled(string $key): bool
    {
        return app(FeatureFlagService::class)->enabled($key);
    }

    public static function master(): bool
    {
        return app(FeatureFlagService::class)->masterEnabled();
    }

    public static function autoMatch(): bool
    {
        return app(FeatureFlagService::class)->autoMatchEnabled();
    }
}
