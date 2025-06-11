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
        Schema::table('wa_receive_purchase_order_items', function (Blueprint $table) {
            $table->string('return_doc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_receive_purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('return_doc');
        });
    }
};
