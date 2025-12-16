<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['app_name' => 'Visa Office Chapai International'],
            [
                'office_phone' => '+880 1234 567890',
                'office_phone2' => '+880 9876 543210',
                'office_address' => 'Chapai Nawabganj, Rajshahi, Bangladesh',
                'office_email' => 'info@visachapai.com',
                'whatsapp_number' => '+880 1234 567890',
                'facebook_url' => 'https://facebook.com/visachapai',
                'instagram_url' => 'https://instagram.com/visachapai',
                'primary_color' => '#3b82f6',
                'secondary_color' => '#1e40af',
                'tertiary_color' => '#10b981',
                'currency' => 'BDT',
                'currency_symbol' => '৳',
                'timezone' => 'Asia/Dhaka',
                'date_format' => 'd/m/Y',
                'smtp_from_address' => 'noreply@visachapai.com',
                'smtp_from_name' => 'Visa Office Chapai International',
                'enable_email_marketing' => true,
                'enable_sms_marketing' => true,
                'sms_provider' => 'bulkbd',
                'sms_sender_id' => 'VISACHAP',
                'sms_unit_price' => 0.50,
                'email_daily_limit' => 500,
                'sms_daily_limit' => 100,
                'invoice_prefix' => 'INV-',
                'invoice_footer' => 'Thank you for your business!',
            ]
        );
    }
}
