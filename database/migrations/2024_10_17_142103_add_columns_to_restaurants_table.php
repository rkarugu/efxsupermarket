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
            $table->bigInteger('equity_account')->nullable();
            $table->bigInteger('equity_paybill')->nullable();
            $table->bigInteger('kcb_account')->nullable();
            $table->bigInteger('kcb_paybill')->nullable();
            $table->bigInteger('vooma_account')->nullable();
            $table->bigInteger('vooma_paybill')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumns(['equity_account', 'equity_paybill', 'kcb_account', 'kcb_paybill', 'vooma_account', 'vooma_paybill']);
        });
    }
};
