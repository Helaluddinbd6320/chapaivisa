<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone1')->nullable()->after('email');
            $table->string('phone2')->nullable()->after('phone1');
            $table->text('address')->nullable()->after('phone2');
            $table->string('photo')->nullable()->after('address');
            $table->string('reference')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone1', 'phone2', 'address', 'photo', 'reference']);
        });
    }
};
