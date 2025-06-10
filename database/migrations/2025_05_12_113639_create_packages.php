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
        Schema::create('packages', function (Blueprint $table) {
            $table->id('package_id');
            $table->string('package_code', 50)->unique();
            $table->string('place_name', 100);
            $table->integer('price_trip');
            $table->integer('duration_days');
            $table->string('origin', 100)->nullable();
            $table->string('departure_point', 100)->nullable();
            $table->string('about_trip');
            $table->unsignedBigInteger('sub_destination_id'); 
            $table->timestamps();
            $table->softDeletes(); // <-- Adds soft delete column

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
