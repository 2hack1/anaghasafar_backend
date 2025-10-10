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
        Schema::table('order', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('id');
            $table->string('payment_status')->default('pending')->after('transaction_id'); // pending, completed, failed
            $table->string('payment_method')->nullable()->after('payment_status'); // e.g., UPI, Card
            $table->decimal('total_amount', 10, 2)->default(0)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_status', 'payment_method', 'total_amount']);
        });
    }
};
