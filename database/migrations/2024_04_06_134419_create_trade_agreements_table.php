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
        Schema::create('trade_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('name');
            $table->date('date');
            $table->longText('summary')->nullable();
            $table->string('status')->nullable()->default('Pending');
            $table->unsignedInteger('wa_supplier_id');
            $table->foreign('wa_supplier_id')->references('id')->on('wa_suppliers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_agreements');
    }
};
