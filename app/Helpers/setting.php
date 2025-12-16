<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        static $settings = null;

        if (is_null($settings)) {
            $settings = Setting::first();
        }

        if (is_null($key)) {
            return $settings;
        }

        return $settings->{$key} ?? $default;
    }
}

if (! function_exists('app_settings')) {
    function app_settings()
    {
        return Setting::first();
    }
}
