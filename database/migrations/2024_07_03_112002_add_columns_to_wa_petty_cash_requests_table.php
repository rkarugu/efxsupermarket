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
        Schema::table('wa_petty_cash_requests', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->after('type')->nullable()->constrained();
            $table->string('repair_type')->after('vehicle_id')->nullable(); // service, garage, tyres
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropColumn('repair_type');
        });
    }
};
