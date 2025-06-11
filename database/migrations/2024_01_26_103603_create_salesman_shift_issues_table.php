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
        Schema::create('salesman_shift_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('salesman_shifts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('salesman_id');
            $table->unsignedInteger('customer_id');
            $table->string('scenario');
            $table->unsignedInteger('inventory_item_id')->nullable();
            $table->double('new_price')->nullable();
            $table->string('image')->nullable();
            $table->string('product_name')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_shift_issues');
    }
};
