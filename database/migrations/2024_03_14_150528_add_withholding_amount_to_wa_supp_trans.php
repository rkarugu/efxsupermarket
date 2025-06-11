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
        Schema::table('wa_supp_trans', function (Blueprint $table) {
            $table->double('withholding_amount')->after('vat_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_supp_trans', function (Blueprint $table) {
            $table->dropColumn('withholding_amount');
        });
    }
};
