<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_vendors', function (Blueprint $table) {
            $table->text('cancellation_refund_policy')->nullable();
            $table->text('payment_policy')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('terms_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_vendors', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_refund_policy',
                'payment_policy',
                'privacy_policy',
                'terms_conditions'
            ]);
        });
    }
};
