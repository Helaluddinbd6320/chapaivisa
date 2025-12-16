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
        Schema::create('s_m_s_marketings', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->nullable();
            $table->text('message')->nullable();
            $table->string('template_type')->default('promotional'); // promotional, transactional, reminder, alert
            $table->json('recipient_criteria')->nullable();
            $table->json('specific_recipients')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('status')->default('draft'); // draft, scheduled, processing, completed, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('timezone')->default('Asia/Dhaka');
            $table->boolean('unicode')->default(false);
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
        Schema::dropIfExists('s_m_s_marketings');
    }
};
