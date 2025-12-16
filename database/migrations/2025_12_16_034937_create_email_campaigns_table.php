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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->nullable();
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->string('template_type')->default('newsletter'); // newsletter, promotional, welcome, reminder
            $table->json('recipient_criteria')->nullable();
            $table->json('specific_recipients')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->string('status')->default('draft'); // draft, scheduled, processing, completed, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('timezone')->default('Asia/Dhaka');
            $table->boolean('track_opens')->default(true);
            $table->boolean('track_clicks')->default(true);
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('status')->nullable();
            $table->index('scheduled_at')->nullable();
            $table->index('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
