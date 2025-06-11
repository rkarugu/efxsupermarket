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
        Schema::create('stock_take_user_assignment', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by');
            $table->integer('user_id');
            $table->integer('uom_id');
            $table->integer('wa_location_and_store_id');
            $table->string('category_ids');
            $table->string('stock_take_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_take_user_assignment');
    }
};
