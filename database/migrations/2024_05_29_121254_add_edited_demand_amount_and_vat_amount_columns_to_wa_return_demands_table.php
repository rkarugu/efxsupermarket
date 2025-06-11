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
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->double('edited_demand_amount')->after('demand_amount');
            $table->double('vat_amount')->after('edited_demand_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->dropColumn('edited_demand_amount');
            $table->dropColumn('vat_amount');
        });
    }
};
