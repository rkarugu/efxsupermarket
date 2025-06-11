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
        Schema::create('gl_recon_statements', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->unsignedInteger('gl_reconcile_id');
            $table->unsignedInteger('bank_id');
            $table->string('matched_type');
            $table->integer('matched_id');
            $table->string('current_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_recon_statements');
    }
};
