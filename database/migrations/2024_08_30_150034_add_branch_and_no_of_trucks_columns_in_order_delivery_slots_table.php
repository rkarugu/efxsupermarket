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
        Schema::table('order_delivery_slots', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('restaurants')->nullOnDelete();
            $table->integer('no_of_trucks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_delivery_slots', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            
            // Drop the 'no_of_trucks' column
            $table->dropColumn('no_of_trucks');
            
            // Drop the 'branch_id' column
            $table->dropColumn('branch_id');
        });
    }
};
