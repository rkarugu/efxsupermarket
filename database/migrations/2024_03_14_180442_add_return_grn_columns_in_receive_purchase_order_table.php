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
        Schema::table('wa_receive_purchase_orders', function (Blueprint $table) {
            $table->string('return_no')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_receive_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('return_no');
            $table->dropColumn('returned_at');
            $table->dropColumn('returned_by');
        });
    }
};
