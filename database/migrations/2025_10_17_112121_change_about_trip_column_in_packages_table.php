<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            // Example: change 'about_trip' from string to text
            $table->text('about_trip')->change();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            // Rollback to original type (if it was string before)
            $table->string('about_trip', 255)->change();
        });
    }
};
