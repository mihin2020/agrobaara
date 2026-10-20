<?php

namespace App\Support;

use App\Models\ReferentialActivityType;
use App\Models\ReferentialCommune;
use App\Models\ReferentialEducationLevel;
use App\Models\ReferentialEmploymentType;
use App\Models\ReferentialFormationType;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialLicense;
use App\Models\ReferentialNationality;
use App\Models\ReferentialSkill;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Cache court des référentiels actifs (attributs en tableau → hydrate).
 */
class ReferentialCache
{
    private const TTL = 300;

    public static function communes(): EloquentCollection
    {
        return self::remember('refs.communes.active', ReferentialCommune::class, fn () => ReferentialCommune::active()->get());
    }

    public static function skills(): EloquentCollection
    {
        return self::remember('refs.skills.active', ReferentialSkill::class, fn () => ReferentialSkill::active()->get());
    }

    public static function licenses(): EloquentCollection
    {
        return self::remember('refs.licenses.active', ReferentialLicense::class, fn () => ReferentialLicense::active()->get());
    }

    public static function educations(): EloquentCollection
    {
        return self::remember('refs.educations.active', ReferentialEducationLevel::class, fn () => ReferentialEducationLevel::active()->get());
    }

    public static function languages(): EloquentCollection
    {
        return self::remember('refs.languages', ReferentialLanguage::class, fn () => ReferentialLanguage::active()->get());
    }

    public static function nationalities(): EloquentCollection
    {
        return self::remember('refs.nationalities', ReferentialNationality::class, fn () => ReferentialNationality::active()->get());
    }

    public static function employmentTypes(): EloquentCollection
    {
        return self::remember('refs.employment_types', ReferentialEmploymentType::class, fn () => ReferentialEmploymentType::active()->get());
    }

    public static function formationTypes(): EloquentCollection
    {
        return self::remember('refs.formation_types', ReferentialFormationType::class, fn () => ReferentialFormationType::active()->get());
    }

    public static function activityTypes(): EloquentCollection
    {
        return self::remember('refs.activity_types', ReferentialActivityType::class, fn () => ReferentialActivityType::active()->get());
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  \Closure(): EloquentCollection  $loader
     */
    private static function remember(string $key, string $modelClass, \Closure $loader): EloquentCollection
    {
        $rows = Cache::get($key);

        if (! is_array($rows)) {
            Cache::forget($key);
            $rows = $loader()->map->getAttributes()->values()->all();
            Cache::put($key, $rows, self::TTL);
        }

        return $modelClass::hydrate($rows);
    }

    public static function flush(): void
    {
        foreach ([
            'refs.communes.active',
            'refs.skills.active',
            'refs.licenses.active',
            'refs.educations.active',
            'refs.languages',
            'refs.nationalities',
            'refs.employment_types',
            'refs.formation_types',
            'refs.activity_types',
        ] as $key) {
            Cache::forget($key);
        }
    }
}
