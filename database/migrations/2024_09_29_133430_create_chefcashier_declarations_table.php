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
        Schema::create('chief_cashier_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); //cashier_id
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('total_drop')->default(0);
            $table->decimal('banked_drops')->default(0);
            $table->decimal('un_banked_drop')->default(0);
            $table->date('cleared_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chief_cashier_declarations');
    }
};
