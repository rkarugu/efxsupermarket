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
        Schema::create('wallet_supplier_document_process_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_supplier_document_process_id');
            $table->unsignedBigInteger('approved_by');
            $table->string('previous_status');
            $table->string('updated_status');
            $table->string('previous_approve_status');
            $table->string('updated_approve_status');
            $table->string('stage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_supplier_document_process_logs');
    }
};
