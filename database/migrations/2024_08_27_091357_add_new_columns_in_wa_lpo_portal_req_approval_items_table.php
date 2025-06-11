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
        Schema::table('wa_lpo_portal_req_approval_items', function (Blueprint $table) {
            $table->decimal('unit_price', 20,2)->nullable();
            $table->decimal('vat_percentage', 20,2)->nullable();
            $table->decimal('vat_amount', 20,2)->nullable();
            $table->decimal('discount_amount',20,2)->nullable()->default(0);
            $table->decimal('discount_percentage',10,2)->nullable()->default(0);
            $table->text('discount_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_lpo_portal_req_approval_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price','vat_percentage','vat_amount','discount_amount','discount_percentage','discount_settings'
            ]);
        });
    }
};
