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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_mob_no1', 15)
                ->nullable()
                ->after('email');

            $table->string('user_mob_no2', 15)
                ->nullable()
                ->after('user_mob_no1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_mob_no1', 'user_mob_no2']);
        });
    }
};
