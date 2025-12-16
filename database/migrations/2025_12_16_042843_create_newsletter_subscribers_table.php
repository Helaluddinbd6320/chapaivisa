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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('subscription_type')->default('email'); // email, sms, both
            $table->boolean('is_active')->default(true);
            $table->string('source')->nullable(); // website, form, import, manual
            $table->json('tags')->nullable();
            $table->string('unsubscribe_token')->nullable();
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->integer('email_count')->default(0);
            $table->integer('sms_count')->default(0);
            $table->timestamps();

            $table->index('email');
            $table->index('is_active');
            $table->index('subscription_type');
            $table->index('unsubscribe_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
