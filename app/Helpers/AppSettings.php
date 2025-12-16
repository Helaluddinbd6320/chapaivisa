<?php

namespace App\Helpers;

class AppSettings
{
    public static function info(): array
    {
        $settings = app('settings');

        return [
            'app_name'       => $settings->get('app_name', config('app.name')),
            'office_phone'   => $settings->get('office_phone'),
            'office_phone2'  => $settings->get('office_phone2'),
            'office_address' => $settings->get('office_address'),
            'office_email'   => $settings->get('office_email'),
        ];
    }
}
