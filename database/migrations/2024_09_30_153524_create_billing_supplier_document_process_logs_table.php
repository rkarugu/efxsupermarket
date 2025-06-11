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
        Schema::create('billing_supplier_document_process_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_supplier_document_process_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('previous_status')->nullable();
            $table->string('updated_status')->nullable();
            $table->string('previous_approve_status')->nullable();
            $table->string('updated_approve_status')->nullable();
            $table->string('stage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_supplier_document_process_logs');
    }
};
