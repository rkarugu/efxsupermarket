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
        Schema::create('salesman_supplier_incentive_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('route_id')->nullable();
            $table->string('stock_id_code')->nullable();
            $table->decimal('quantity')->default(0);
            $table->unsignedInteger('wa_stock_move_id')-> nullable();
            $table->boolean('settled')->default(false);
            $table->string('supplier_code')->nullable();
            $table->string('incentive_id')->nullable();
            $table->string('incentive')->nullable();
            $table->decimal('target')->nullable();
            $table->decimal('reward')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_supplier_incentive_earnings');
    }
};
