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
            $table->unsignedInteger('tax_manager_id')->nullable()->after('delivery_schedule_id');
            $table->foreign('tax_manager_id')->references('id')->on('tax_managers');
            $table->double('tax_value')->nullable()->after('amount');
            $table->double('vat_amount')->nullable()->after('tax_value');
            $table->double('sub_total')->nullable()->after('vat_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->dropForeign('wa_petty_cash_request_items_tax_manager_id_foreign');
            $table->dropColumn(['tax_manager_id', 'tax_value', 'vat_amount', 'sub_total']);
        });
    }
};
