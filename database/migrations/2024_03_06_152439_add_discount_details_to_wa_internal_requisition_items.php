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
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->double('discount')->default(0);
            $table->text('discount_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->dropColumn(['discount', 'discount_description']);
        });
    }
};
