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
        Schema::table('salesman_shift_customers', function (Blueprint $table) {
            $table->boolean('order_taken')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salesman_shift_customers', function (Blueprint $table) {
            $table->dropColumn(['order_taken']);
        });
    }
};
