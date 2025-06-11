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
        Schema::create('resolved_salesman_reported_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salesman_shift_issues_id');
            $table->longText('description');
            $table->integer('invoice_sent_to_supplier')->default(0);
            $table->integer('price_changed')->default(0);
            $table->integer('resolved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolved_salesman_reported_issues');
    }
};
