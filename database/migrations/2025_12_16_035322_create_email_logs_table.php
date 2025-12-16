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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->string('message_id')->nullable();
            $table->string('status')->default('pending'); // pending, sent, delivered, opened, clicked, bounced, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->json('tracking_data')->nullable();
            $table->string('unsubscribe_token')->nullable();
            $table->boolean('unsubscribed')->default(false);
            $table->timestamps();

            $table->index('status')->nullable();
            $table->index('recipient_email')->nullable();
            $table->index('sent_at')->nullable();
            $table->index(['email_campaign_id', 'status'])->nullable();
            $table->index('unsubscribe_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
