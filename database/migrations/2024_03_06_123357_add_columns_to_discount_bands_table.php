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
        Schema::table('discount_bands', function (Blueprint $table) {
            $table->renameColumn('sale_quantity', 'from_quantity');
            $table->integer('to_quantity')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_bands', function (Blueprint $table) {
            $table->renameColumn('from_quantity','sale_quantity');
            $table->dropColumn('to_quantity');
        });
    }
};
