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
        Schema::create('wallet_matrix', function (Blueprint $table) {
            $table->string('parameter');
            $table->decimal('salesman', 5, 2)->nullable();
            $table->decimal('delivery_driver', 5, 2)->nullable();
            $table->decimal('turn_boy')->nullable();
            $table->decimal('driver_grn')->nullable();
            $table->string('status')->default('active')->comment('active', 'inactive');
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_matrix');
    }
};
