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
        Schema::create('gl_reconcile_interest_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gl_reconcile_id');
            $table->string('type',125);
            $table->date('date');
            $table->decimal('amount',20);
            $table->unsignedInteger('chart_of_account_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_reconcile_interest_expenses');
    }
};
