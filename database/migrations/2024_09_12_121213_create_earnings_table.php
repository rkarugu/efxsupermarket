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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            // $table->longText('description')->nullable();
            $table->string('type')->default('cash'); //cash, non_cash
            $table->string('amount_type'); // fixed_amount, percentage, ratio
            // $table->double('amount')->nullable();
            $table->double('rate')->nullable();
            $table->double('ratio')->nullable();
            $table->boolean('is_taxable')->default(false);
            // $table->double('tax_rate')->nullable();
            $table->boolean('is_recurring')->default(false);
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
        Schema::dropIfExists('earnings');
    }
};
