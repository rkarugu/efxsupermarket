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
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->string('secondary_phone_no')->nullable();
            $table->string('secondary_name')->nullable();
            $table->string('customer-type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropColumn('secondary_phone_no');
            $table->dropColumn('secondary_name');
            $table->dropColumn('customer_type');
        });
    }
};
