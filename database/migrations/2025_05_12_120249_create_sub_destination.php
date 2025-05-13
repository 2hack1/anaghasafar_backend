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

          // Foreign key to destinations

        //  $table->index('package_id');
        //     // Foreign key constraint
        //     $table->foreign('package_id')
        //           ->references('package_id')
        //           ->on('packages')
        //           ->onDelete('cascade');

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
