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
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->longText('content');
            $table->string('message_id')->nullable();
            $table->string('status', 50)->default('pending'); // <-- 50 characters max
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

            // কমপোজিট ইন্ডেক্সের জন্য আলাদা আলাদা ইন্ডেক্স ব্যবহার করুন
            $table->index('email_campaign_id');
            $table->index('status');
            $table->index('recipient_email');
            $table->index('sent_at');
            $table->index('unsubscribe_token');
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
