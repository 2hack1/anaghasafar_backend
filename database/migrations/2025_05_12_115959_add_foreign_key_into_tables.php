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
        // Schema::table('name_here', function (Blueprint $table) {

        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('name_here', function (Blueprint $table) {
            // $table->dropForeign('name_here_fk');
        });
        //
    }
};
