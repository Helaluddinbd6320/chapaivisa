<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visas', function (Blueprint $table) {
            $table->id();

            // Applicant Information
            $table->string('name')->nullable();
            $table->string('passenger_image')->nullable();
            $table->string('passport')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();

            // Foreign Keys - User টেবিলের id এর সাথে relation
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Agent/User who created this visa');
            $table->foreignId('medical_center_id')->nullable()->constrained('medical_centers')->onDelete('set null');
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('set null');

            // Processing Information
            $table->string('takamul_category')->nullable();
            $table->enum('takamul', ['yes', 'no'])->default('no')->nullable();
            $table->enum('tasheer', ['yes', 'no'])->default('no')->nullable();
            $table->enum('ttc', ['yes', 'no'])->default('no')->nullable();
            $table->enum('bmet', ['yes', 'no'])->default('no')->nullable();
            $table->string('iqama')->nullable();
            $table->enum('embassy', ['yes', 'no'])->default('no')->nullable();
            $table->string('pc_ref')->nullable();
            $table->string('visa_type')->nullable();

            // Medical Information
            $table->string('medical_status')->nullable();
            $table->date('medical_date')->nullable();
            $table->string('mofa_number')->nullable();

            // Visa Information
            $table->string('visa_number')->nullable();
            $table->string('visa_id_number')->nullable();
            $table->date('visa_date')->nullable();
            $table->string('visa_condition')->nullable();

            // Documents & Images
            $table->string('passport_image')->nullable();
            $table->string('slip_image')->nullable();
            $table->string('visa_image')->nullable();
            $table->string('slip_url')->nullable();

            // Report & Cost
            $table->enum('report', ['pending', 'approved', 'completed'])->default('pending');
            $table->decimal('visa_cost', 12, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visas');
    }
};
