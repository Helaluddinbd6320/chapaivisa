<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Simple implementation without cache
        $this->app->singleton('settings', function () {
            return \App\Models\Setting::first() ?? \App\Models\Setting::create([
                'app_name' => 'Visa Office Chapai International'
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}