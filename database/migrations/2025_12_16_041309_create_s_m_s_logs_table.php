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
        Schema::create('s_m_s_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_phone', 20); // 20 characters
            $table->string('recipient_name', 100)->nullable(); // 100 characters
            $table->text('message');
            $table->string('message_id', 100)->nullable(); // 100 characters
            $table->string('status', 20)->default('pending'); // 20 characters (important!)
            $table->text('error_message')->nullable();
            $table->string('gateway_response', 255)->nullable();
            $table->decimal('cost', 8, 2)->default(0);
            $table->integer('segments')->default(1);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            // আলাদা আলাদা ইন্ডেক্স ব্যবহার করুন (কমপোজিট ইন্ডেক্স নয়)
            $table->index('sms_campaign_id');
            $table->index('status');
            $table->index('recipient_phone');
            $table->index('sent_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_m_s_logs');
    }
};
