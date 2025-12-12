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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name')->unique();
            $table->string('rl_number')->unique();

            // Contact Persons
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->string('manager_name')->nullable();
            $table->string('manager_phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable()->default('Bangladesh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
