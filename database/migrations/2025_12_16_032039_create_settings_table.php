<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // General Settings
            $table->string('app_name')->default('Visa Office Chapai International');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Contact Information
            $table->string('office_phone')->nullable();
            $table->string('office_phone2')->nullable();
            $table->text('office_address')->nullable();
            $table->string('office_email')->nullable();
            $table->string('whatsapp_number')->nullable();

            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();

            // Appearance
            $table->string('primary_color')->default('#3b82f6');
            $table->string('secondary_color')->default('#1e40af');
            $table->string('tertiary_color')->default('#10b981');

            // Regional Settings
            $table->string('currency')->default('BDT');
            $table->string('currency_symbol')->default('৳');
            $table->string('timezone')->default('Asia/Dhaka');
            $table->string('date_format')->default('d/m/Y');

            // Maintenance
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();

            // Email Settings (SMTP)
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            // Email Marketing
            $table->boolean('enable_email_marketing')->default(true);
            $table->text('welcome_email_template')->nullable();
            $table->text('newsletter_template')->nullable();
            $table->text('promotional_template')->nullable();
            $table->text('reminder_template')->nullable();
            $table->json('email_marketing_schedule_days')->nullable();
            $table->time('email_marketing_schedule_time')->nullable();
            $table->integer('email_daily_limit')->default(500);
            $table->boolean('email_tracking')->default(true);

            // SMS Settings
            $table->boolean('enable_sms_marketing')->default(true);
            $table->string('sms_provider')->nullable(); // twilio, nexmo, greensms, bulkbd, custom
            $table->string('sms_api_key')->nullable();
            $table->string('sms_api_secret')->nullable();
            $table->string('sms_sender_id')->nullable();
            $table->string('sms_api_url')->nullable();
            $table->text('welcome_sms_template')->nullable();
            $table->text('promotional_sms_template')->nullable();
            $table->text('reminder_sms_template')->nullable();
            $table->integer('sms_daily_limit')->default(100);
            $table->decimal('sms_unit_price', 8, 2)->default(0.50);

            // Invoice Settings
            $table->text('invoice_footer')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->string('invoice_prefix')->default('INV-');

            // Analytics
            $table->string('google_analytics_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
