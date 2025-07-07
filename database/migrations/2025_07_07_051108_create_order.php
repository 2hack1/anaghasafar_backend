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
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->integer('userId');
            $table->integer('destinationId');
            $table->integer('subdesId');
            $table->integer('packagesId');
            $table->integer('monthId');
            $table->integer('dateId');
            $table->integer('itineryId');
            $table->integer('transportId');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
