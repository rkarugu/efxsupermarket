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
            $table->unsignedInteger('route_id')->nullable()->after('delivery_schedule_id');
            $table->foreign('route_id')->references('id')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('route_id');
        });
    }
};
