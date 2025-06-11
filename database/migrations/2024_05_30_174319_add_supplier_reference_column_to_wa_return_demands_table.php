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
            $table->string('supplier_reference')->after('vat_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->dropColumn('supplier_reference');
        });
    }
};
