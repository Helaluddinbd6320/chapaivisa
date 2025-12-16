<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'logo',
        'favicon',
        'office_phone',
        'office_phone2',
        'office_address',
        'office_email',
        'whatsapp_number',
        'facebook_url',
        'instagram_url',
        'primary_color',
        'secondary_color',
        'tertiary_color',
        'currency',
        'currency_symbol',
        'timezone',
        'date_format',
        'maintenance_mode',
        'maintenance_message',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'enable_email_marketing',
        'welcome_email_template',
        'newsletter_template',
        'promotional_template',
        'reminder_template',
        'email_marketing_schedule_days',
        'email_marketing_schedule_time',
        'email_daily_limit',
        'email_tracking',
        'enable_sms_marketing',
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_sender_id',
        'sms_api_url',
        'welcome_sms_template',
        'promotional_sms_template',
        'reminder_sms_template',
        'sms_daily_limit',
        'sms_unit_price',
        'invoice_footer',
        'terms_conditions',
        'privacy_policy',
        'invoice_prefix',
        'google_analytics_id',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'enable_email_marketing' => 'boolean',
        'email_tracking' => 'boolean',
        'enable_sms_marketing' => 'boolean',
        'email_marketing_schedule_days' => 'array',
        'smtp_port' => 'integer',
        'email_daily_limit' => 'integer',
        'sms_daily_limit' => 'integer',
        'sms_unit_price' => 'float',
        'email_marketing_schedule_time' => 'datetime',
    ];

    protected $attributes = [
        'app_name' => 'Visa Office Chapai International',
        'primary_color' => '#3b82f6',
        'secondary_color' => '#1e40af',
        'tertiary_color' => '#10b981',
        'currency' => 'BDT',
        'currency_symbol' => '৳',
        'timezone' => 'Asia/Dhaka',
        'date_format' => 'd/m/Y',
        'maintenance_mode' => false,
        'enable_email_marketing' => true,
        'email_tracking' => true,
        'enable_sms_marketing' => true,
        'email_daily_limit' => 500,
        'sms_daily_limit' => 100,
        'sms_unit_price' => 0.5,
        'invoice_prefix' => 'INV-',
    ];

    /**
     * Get the first settings record or create a new one
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'app_name' => 'Visa Office Chapai International',
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'timezone' => 'Asia/Dhaka',
            'date_format' => 'd/m/Y',
        ]);
    }

    /**
     * Get a specific setting value
     */
    public static function get($key, $default = null)
    {
        $settings = static::getSettings();
        return $settings->{$key} ?? $default;
    }

    /**
     * Update a specific setting
     */
    public static function updateSetting($key, $value)
    {
        $settings = static::getSettings();
        $settings->update([$key => $value]);
        return $settings;
    }

    /**
     * Get email marketing settings
     */
    public function getEmailMarketingSettings()
    {
        return [
            'enabled' => $this->enable_email_marketing,
            'daily_limit' => $this->email_daily_limit,
            'tracking' => $this->email_tracking,
            'schedule_days' => $this->email_marketing_schedule_days ?? [],
            'schedule_time' => $this->email_marketing_schedule_time,
            'templates' => [
                'welcome' => $this->welcome_email_template,
                'newsletter' => $this->newsletter_template,
                'promotional' => $this->promotional_template,
                'reminder' => $this->reminder_template,
            ]
        ];
    }

    /**
     * Get SMS marketing settings
     */
    public function getSmsMarketingSettings()
    {
        return [
            'enabled' => $this->enable_sms_marketing,
            'daily_limit' => $this->sms_daily_limit,
            'unit_price' => $this->sms_unit_price,
            'provider' => $this->sms_provider,
            'sender_id' => $this->sms_sender_id,
            'templates' => [
                'welcome' => $this->welcome_sms_template,
                'promotional' => $this->promotional_sms_template,
                'reminder' => $this->reminder_sms_template,
            ]
        ];
    }

    /**
     * Get SMTP settings
     */
    public function getSmtpSettings()
    {
        return [
            'host' => $this->smtp_host,
            'port' => $this->smtp_port,
            'username' => $this->smtp_username,
            'password' => $this->smtp_password,
            'encryption' => $this->smtp_encryption,
            'from_address' => $this->smtp_from_address,
            'from_name' => $this->smtp_from_name,
        ];
    }

    /**
     * Check if maintenance mode is enabled
     */
    public function isMaintenanceMode()
    {
        return $this->maintenance_mode;
    }

    /**
     * Get maintenance message
     */
    public function getMaintenanceMessage()
    {
        return $this->maintenance_message ?? 'System is under maintenance. Please try again later.';
    }

    /**
     * Get contact information
     */
    public function getContactInfo()
    {
        return [
            'phones' => array_filter([$this->office_phone, $this->office_phone2]),
            'email' => $this->office_email,
            'address' => $this->office_address,
            'whatsapp' => $this->whatsapp_number,
        ];
    }

    /**
     * Get social media links
     */
    public function getSocialLinks()
    {
        return [
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
        ];
    }

    /**
     * Get theme colors
     */
    public function getThemeColors()
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'tertiary' => $this->tertiary_color,
        ];
    }

    /**
     * Get localization settings
     */
    public function getLocalizationSettings()
    {
        return [
            'currency' => $this->currency,
            'currency_symbol' => $this->currency_symbol,
            'timezone' => $this->timezone,
            'date_format' => $this->date_format,
        ];
    }

    /**
     * Get invoice settings
     */
    public function getInvoiceSettings()
    {
        return [
            'prefix' => $this->invoice_prefix,
            'footer' => $this->invoice_footer,
            'terms' => $this->terms_conditions,
            'privacy' => $this->privacy_policy,
        ];
    }
}