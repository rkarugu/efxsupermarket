<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_suppliers', function (Blueprint $table) {
            $table->boolean('professional_withholding')->after('tax_withhold');
        });
        Schema::table('wa_supp_trans', function (Blueprint $table) {
            $table->boolean('professional_withholding')->after('withholding_amount');
        });
    }
   
    public function down(): void
    {
        Schema::table('wa_suppliers', function (Blueprint $table) {
            $table->dropColumn('professional_withholding');
        });

        Schema::table('wa_supp_trans', function (Blueprint $table) {
           $table->dropColumn('professional_withholding');
        });
    }
};
