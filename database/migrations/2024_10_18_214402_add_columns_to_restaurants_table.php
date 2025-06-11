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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->renameColumn('kcb_paybill', 'kcb_mpesa_paybill');
            $table->renameColumn('vooma_paybill', 'kcb_vooma_paybill');
            $table->integer('mpesa_payment_method_id')->nullable();
            $table->integer('equity_payment_method_id')->nullable();
            $table->integer('kcb_payment_method_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumns(['mpesa_payment_method_id', 'equity_payment_method_id', 'kcb_payment_method_id']);
        });
    }
};
