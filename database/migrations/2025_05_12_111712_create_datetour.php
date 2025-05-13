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
        Schema::create('monthtour', function (Blueprint $table) {
            $table->id('tour_month_id');
            $table->string('month');
            $table->string('year');
            $table->unsignedBigInteger('package_id')->nullable();
            $table->timestamps();

            // $table->unsignedBigInteger('date_id');
            //  $table->index('date_id');

            //   $table->foreign('date_id')
            //   ->references('date_id')
            //   ->on('monthtour')
            //   ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthtour');
    }
};
