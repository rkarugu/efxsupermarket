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
            $table->boolean('is_credit_customer')->default(false);
            $table->decimal('credit_limit', 10)->default(0.00);
            $table->decimal('return_limit', 10)->default(0.00);
            $table->integer('payment_term_id')->unsigned()->nullable()->index();
            $table->integer('assigned_route_id')->unsigned()->nullable()->index('assigned_route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropColumn('is_credit_customer');
            $table->dropColumn('credit_limit');
            $table->dropColumn('payment_term_id');
            $table->dropColumn('assigned_route_id');
        });
    }
};
