<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->string('hotel_vendor_id');
            $table->id('hotel_roomId');
            $table->string('roomType');
            $table->unsignedInteger('numRooms')->default(1);
            $table->decimal('basePrice', 10, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('finalPrice', 10, 2)->default(0);
            $table->decimal('extraBedCharge', 10, 2)->default(0);
            $table->boolean('taxIncluded')->default(false);
            $table->unsignedInteger('maxAdults')->default(1);
            $table->unsignedInteger('maxChildren')->default(0);
            $table->unsignedInteger('numberOfBeds')->default(1);
            $table->string('bedType');
            $table->string('bookingStatus')->default('Available');
            $table->boolean('visibility')->default(true);
            $table->text('description')->nullable();
            $table->text('cancellationPolicy')->nullable();
            $table->decimal('cancellation_charges', 10, 2)->default(0);
            $table->time('checkInTime')->nullable();
            $table->time('checkOutTime')->nullable();
            $table->json('amenities')->nullable();

            // Store room images as JSON paths (array of filenames or URLs)
            $table->json('rooms_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
