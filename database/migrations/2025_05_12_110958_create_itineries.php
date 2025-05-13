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
        Schema::create('itineries', function (Blueprint $table) {
               $table->id('itinerary_id');             // INT AUTO_INCREMENT PRIMARY KEY
                $table->text('day_wise_details');       // TEXT (can store JSON or plain text)
             $table->unsignedBigInteger('package_id')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineries');
    }
};
