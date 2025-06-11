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
            $table->string('cu_invoice_no')->nullable()->after('demand_amount');
            $table->text('note')->nullable()->after('cu_invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->dropColumn('cu_invoice_no');
            $table->dropColumn('note');
        });
    }
};
