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
        Schema::create('sub_destination', function (Blueprint $table) {
            $table->id('sub_destination_id');              // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 100);                    // VARCHAR(100) NOT NULL
            $table->string('image_url', 255)->nullable();   // VARCHAR(255), optional

            $table->unsignedBigInteger('destination_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_destination');
    }
};
