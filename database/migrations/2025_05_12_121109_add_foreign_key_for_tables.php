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
        // Add foreign key to monthtour
        Schema::table('datestour', function (Blueprint $table) {
            $table->foreign('tour_month_id')
                ->references('tour_month_id')
                ->on('monthtour')
                  ->onDelete('cascade'); ;
        });

        // Add foreign key to sub_destination
        Schema::table('sub_destination', function (Blueprint $table) {
            $table->foreign('destination_id')
                ->references('destination_id')
                ->on('destination')
                  ->onDelete('cascade'); ;
        });

        // Add foreign keys to packages
        Schema::table('pacimgs', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('set null');
        });

        Schema::table('itineries', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('set null');
        });

        Schema::table('monthtour', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('set null');
        });

        Schema::table('transport', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('set null');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->foreign('sub_destination_id')
                ->references('sub_destination_id')
                ->on('sub_destination')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datestour', function (Blueprint $table) {
            $table->dropForeign(['tour_month_id']);
        });

        Schema::table('sub_destination', function (Blueprint $table) {
            $table->dropForeign(['destination_id']);
        });

        Schema::table('pacimgs', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('itineries', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('monthtour', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('transport', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['sub_destination_id']);
        });
    }
};
