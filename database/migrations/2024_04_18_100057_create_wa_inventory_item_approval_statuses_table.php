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
        Schema::create('wa_inventory_item_approval_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_inventory_items_id');
            $table->unsignedInteger('approval_by');
            $table->string('status', 125);
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_inventory_item_approval_statuses');
    }
};
