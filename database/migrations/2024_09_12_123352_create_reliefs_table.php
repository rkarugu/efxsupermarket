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
        Schema::create('reliefs', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('earning_id')->nullable();
            // $table->unsignedBigInteger('deduction_id')->nullable();
            $table->string('name');
            // $table->longText('description')->nullable();
            $table->string('amount_type'); // flat_rate, percentage
            $table->double('amount')->nullable();
            $table->double('rate')->nullable();
            $table->boolean('system_reserved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reliefs');
    }
};
