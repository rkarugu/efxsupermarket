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
        Schema::table('wa_customers', function (Blueprint $table) {
            $table->boolean('is_invoice_customer')->default(false);
            $table->unsignedInteger('delivery_route_id')->nullable();
            $table->string('bussiness_name', 200)->nullable();
            $table->string('kra_pin', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_customers', function (Blueprint $table) {
            $table->dropColumn('is_invoice_customer');
            $table->dropColumn('delivery_route_id');
            $table->dropColumn('bussiness_name');
            $table->dropColumn('kra_pin');
        });
    }
};
