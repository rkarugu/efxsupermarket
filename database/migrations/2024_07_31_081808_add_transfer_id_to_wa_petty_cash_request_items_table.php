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
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->unsignedInteger('transfer_id')->nullable()->after('grn_number');
            $table->foreign('transfer_id')->references('id')->on('n_wa_inventory_location_transfers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transfer_id');
        });
    }
};
