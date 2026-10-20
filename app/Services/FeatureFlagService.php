<?php

namespace App\Services;

use App\Models\FeatureFlag;

class FeatureFlagService
{
    public const MASTER = 'phase2_master';

    /** @var list<string> */
    public const FLAGS = [
        'phase2_master',
        'self_registration',
        'portal_candidate',
        'portal_company',
        'portal_training_center',
        'public_offers',
        'testimonials',
        'news_ribbon',
    ];

    public function enabled(string $key): bool
    {
        $flags = FeatureFlag::allCached();

        if ($key === self::MASTER) {
            return (bool) ($flags[self::MASTER] ?? false);
        }

        if (! ($flags[self::MASTER] ?? false)) {
            return false;
        }

        return (bool) ($flags[$key] ?? false);
    }

    public function masterEnabled(): bool
    {
        return $this->enabled(self::MASTER);
    }

    /** Matching auto top-5 : lié uniquement au master. */
    public function autoMatchEnabled(): bool
    {
        return $this->masterEnabled();
    }

    public function set(string $key, bool $enabled): void
    {
        FeatureFlag::query()->updateOrCreate(
            ['key' => $key],
            ['enabled' => $enabled]
        );
        FeatureFlag::flushCache();
    }

    public function setMany(array $map): void
    {
        foreach ($map as $key => $enabled) {
            if (! in_array($key, self::FLAGS, true)) {
                continue;
            }
            FeatureFlag::query()->updateOrCreate(
                ['key' => $key],
                ['enabled' => (bool) $enabled]
            );
        }
        FeatureFlag::flushCache();
    }

    public function allWithMeta(): array
    {
        return FeatureFlag::query()->orderBy('key')->get()->all();
    }
}
