<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_notes', function (Blueprint $table) {
            $table->double('withholding_amount')->after('tax_amount')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('financial_notes', function (Blueprint $table) {
            $table->dropColumn('withholding_amount');
        });
    }
};
