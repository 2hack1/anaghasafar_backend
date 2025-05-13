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
        Schema::create('transport', function (Blueprint $table) {
          $table->id('transport_id');         // INT AUTO_INCREMENT PRIMARY KEY 
            $table->json('mode');      // VARCHAR(50) NOT NULL
            $table->text('details')->nullable(); // TEXT, optional
            $table->unsignedBigInteger('package_id')->nullable();
            $table->timestamps();     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport');
    }
};
