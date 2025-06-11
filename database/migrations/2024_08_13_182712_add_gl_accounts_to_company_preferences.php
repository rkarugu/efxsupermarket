<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_company_preferences', function (Blueprint $table) {
            $table->unsignedInteger('discount_recieved_gl_account');
            $table->unsignedInteger('withholding_vat_gl_account');
        });
    }

    public function down(): void
    {
        Schema::table('wa_company_preferences', function (Blueprint $table) {
            $table->dropColumn('discount_recieved_gl_account');
            $table->dropColumn('withholding_vat_gl_account');
        });
    }
};
