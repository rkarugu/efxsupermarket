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
//            $table->bigInteger('order_item_id')->nullable();
//            $table->decimal('ordered_quantity', 20,2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        Schema::table('wa_lpo_portal_req_approval_items', function (Blueprint $table) {
//            $table->dropColumn('order_item_id');
//            $table->dropColumn('ordered_quantity');
//        });
    }
};
