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
        Schema::create('destination', function (Blueprint $table) {
         $table->id("destination_id");
            $table->string('name');
            $table->enum('type', ['inbound', 'outbound']);
            $table->timestamps();
        //    $table->unsignedBigInteger('sub_destination_id');
// currently write on it
            //  $table->index('sub_destination_id');

            //   $table->foreign('sub_destination_id')
            //       ->references('sub_destination_id')
            //       ->on('sub_destinations')
            //       ->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination');
    }
};
