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
        Schema::table('wa_petty_cash_request_types', function (Blueprint $table) {
            $table->unsignedInteger('wa_charts_of_account_id')->nullable()->after('slug');
            $table->foreign('wa_charts_of_account_id')->references('id')->on('wa_charts_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_types', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wa_charts_of_account_id');
        });
    }
};
