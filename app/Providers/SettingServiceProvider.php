<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('settings', function () {
            return Cache::rememberForever('settings', function () {
                $settings = Setting::first();
                if (! $settings) {
                    $settings = Setting::create([
                        'app_name' => 'Visa Office Chapai International',
                        'currency' => 'BDT',
                        'currency_symbol' => '৳',
                        'timezone' => 'Asia/Dhaka',
                    ]);
                }

                return $settings;
            });
        });
    }

    public function boot(): void
    {
        Setting::saved(function () {
            Cache::forget('settings');
        });

        Setting::deleted(function () {
            Cache::forget('settings');
        });
    }
}
