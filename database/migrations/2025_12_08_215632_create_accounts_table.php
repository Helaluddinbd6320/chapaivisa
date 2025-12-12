<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Payment Information
            $table->string('transaction_id')->unique()->nullable();
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'refund'])->default('deposit');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank', 'mobile_banking', 'card'])->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mobile_banking_provider')->nullable();
            $table->string('mobile_number')->nullable();

            // Payment Details
            $table->date('payment_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('receipt_image')->nullable();
            $table->string('reference_number')->nullable();

            // Status & Verification
            $table->enum('status', ['pending', 'verified', 'cancelled'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            // Description
            $table->text('description')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
