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
        Schema::table('wa_demands', function (Blueprint $table) {
            $table->double('edited_demand_amount');
            $table->double('vat_amount');
            $table->string('supplier_reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_demands', function (Blueprint $table) {
            $table->dropColumn('edited_demand_amount');
            $table->dropColumn('vat_amount');
            $table->dropColumn('supplier_reference');
        });
    }
};
