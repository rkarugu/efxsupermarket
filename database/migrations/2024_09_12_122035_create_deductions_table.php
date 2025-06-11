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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            // $table->longText('description')->nullable();
            $table->string('amount_type'); // fixed_amount, percentage
            // $table->double('amount')->nullable();
            $table->double('rate')->nullable();
            // $table->boolean('has_brackets')->default(false);
            $table->boolean('is_recurring')->default(false);
            // $table->boolean('is_statutory')->default(false);
            $table->boolean('is_reliefable')->default(false);
            $table->boolean('system_reserved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
