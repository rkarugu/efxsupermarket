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
        Schema::table('wa_petty_cash_request_item_withdrawals', function (Blueprint $table) {
            $table->string('reference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_item_withdrawals', function (Blueprint $table) {
            $table->string('reference')->change();
        });
    }
};
