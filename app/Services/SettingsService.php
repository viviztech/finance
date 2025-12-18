<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // Cache all settings for 24 hours (or forever until cleared)
        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::all()->keyBy('key');
        });

        if ($setting = $settings->get($key)) {
            return $this->castValue($setting->value, $setting->type);
        }

        return $default;
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @return Setting
     */
    public function set(string $key, $value, string $group = 'general', string $type = 'string')
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget('app_settings');

        return $setting;
    }

    /**
     * Cast value based on type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return intval($value);
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
            case 'array':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }
}
