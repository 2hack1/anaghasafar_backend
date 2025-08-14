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
    Schema::create('booking_details', function (Blueprint $table) {
       
        $table->id('id');
        // Relationships
        $table->unsignedBigInteger('user_id');        // Who booked
        $table->unsignedBigInteger('hotel_vendor_id');       // Which hotel
        $table->unsignedBigInteger('hotel_roomId');        // Which room

        // Booking Dates
        $table->date('check_in_date');
        $table->date('check_out_date');

        // Guest Info
        $table->integer('adults')->nullable();;
        $table->integer('children')->nullable();
        $table->integer('rooms_booked')->default(1);
         $table->string('roomType');

        // Payment & Pricing
        $table->decimal('price_per_night', 10, 2);
        $table->decimal('total_amount', 10, 2);
        $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
        $table->string('payment_method')->nullable(); // e.g., credit_card, UPI, etc.
        $table->string('transaction_id')->nullable();

        // Booking Status
        $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

        // Special Requests
        $table->text('special_requests')->nullable();

        // Foreign key constraints
        // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // $table->foreign('hotel_vendor_id')->references('id')->on('hotel_vendors')->onDelete('cascade');
        // $table->foreign('hotel_roomId')->references('id')->on('hotel_rooms')->onDelete('cascade');

        $table->timestamps();
    });
}

};