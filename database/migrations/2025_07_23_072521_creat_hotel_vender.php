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
        Schema::create('hotel_vendors', function (Blueprint $table) {
            $table->id("hotel_vender_id");
            $table->string("users_id");
            $table->string("vendor_name");
            $table->string("vendor_email")->unique();
            $table->string("Mobilenumber");
            $table->string("vendor_password");
            $table->string("hotelname");
            $table->string("hoteltype");
            $table->string("totalrooms");
            $table->string("city");
            $table->string("state");
            $table->string("pincode");
            $table->string("address");
            $table->string("baseprice");
            $table->string("gstnumber")->nullable(); // GST can be optional
            $table->string("licensefile");
            $table->json("hotel_images")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_vendors');
    }
};
