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
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->decimal('invoice_control_amount',20,2)->nullable();
        });

        Schema::table('wa_receive_purchase_orders', function (Blueprint $table) {
            $table->decimal('invoice_control_amount',20,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('invoice_control_amount');
        });

        Schema::table('wa_receive_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('invoice_control_amount');
        });
    }
};
