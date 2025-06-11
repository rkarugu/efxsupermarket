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
        Schema::create('financial_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_no');
            $table->string('type');
            $table->unsignedInteger('supplier_id');
            $table->date('note_date');
            $table->unsignedInteger('location_id');
            $table->string('memo')->nullable();
            $table->double('tax_amount');
            $table->double('amount');
            $table->unsignedInteger('created_by');
            $table->timestamps();
        });

        Schema::create('financial_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('financial_note_id');
            $table->unsignedInteger('account_id');
            $table->string('memo')->nullable();
            $table->double('tax_rate');
            $table->double('tax_amount');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_notes');
        Schema::dropIfExists('financial_note_items');
    }
};
