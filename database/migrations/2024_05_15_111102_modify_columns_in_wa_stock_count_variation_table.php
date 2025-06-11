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
        Schema::table('wa_stock_count_variation', function (Blueprint $table) {
            $table->decimal('quantity_recorded', 10)->nullable()->change();
            $table->string('variation', 10)->nullable()->change();


            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_stock_count_variation', function (Blueprint $table) {
			$table->decimal('quantity_recorded', 10)->default(0.00)->change();
            $table->string('variation', 10)->default('0.00')->comment('current_qoh  - quantity_recorded')->change();
            

        });
    }
};
