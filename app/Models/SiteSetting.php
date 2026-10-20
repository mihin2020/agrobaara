<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::allCached();

        if (! array_key_exists($key, $settings)) {
            return $default;
        }

        $row = $settings[$key];

        return match ($row['type']) {
            'boolean' => filter_var($row['value'], FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $row['value'],
            'json'    => json_decode($row['value'] ?? 'null', true),
            default   => $row['value'],
        };
    }

    /**
     * @return array<string, array{value: mixed, type: string}>
     */
    public static function allCached(): array
    {
        $settings = Cache::get('site_settings_all');

        // Invalide l'ancien format (pluck value seul) ou toute payload corrompue
        if (! is_array($settings) || (count($settings) > 0 && ! self::isTypedPayload($settings))) {
            Cache::forget('site_settings_all');
            $settings = null;
        }

        if ($settings === null) {
            $settings = static::query()
                ->get(['key', 'value', 'type'])
                ->keyBy('key')
                ->map(fn (self $row) => [
                    'value' => $row->value,
                    'type'  => $row->type ?? 'string',
                ])
                ->all();

            Cache::put('site_settings_all', $settings, 300);
        }

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private static function isTypedPayload(array $settings): bool
    {
        $first = reset($settings);

        return is_array($first)
            && array_key_exists('value', $first)
            && array_key_exists('type', $first);
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json'    => json_encode($value),
            default   => (string) $value,
        };

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type]
        );

        Cache::forget('site_settings_all');
    }

    public static function showAgroecologyMention(): bool
    {
        return (bool) static::get('show_agroecology_mention', true);
    }

    public static function twoFactorEmailEnabled(): bool
    {
        return (bool) static::get('two_factor_email_enabled', true);
    }
}
