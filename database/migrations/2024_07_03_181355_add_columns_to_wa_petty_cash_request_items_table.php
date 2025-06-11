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
            $table->unsignedInteger('employee_id')->after('tax_manager_id')->nullable();
            // $table->foreign('employee_id')->references('id')->on('users');
            $table->unsignedInteger('supplier_id')->after('employee_id')->nullable();
            // $table->foreign('supplier_id')->references('id')->on('wa_suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            // $table->dropForeign('wa_petty_cash_request_items_employee_id_foreign');
            // $table->dropForeign('wa_petty_cash_request_items_supplier_id_foreign');
            $table->dropColumn('employee_id');
            $table->dropColumn('supplier_id');
        });
    }
};
