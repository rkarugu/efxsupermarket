<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_break_dispatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('child_bin_id');
            $table->unsignedInteger('mother_bin_id');
            $table->unsignedInteger('initiated_by');
            $table->boolean('dispatched')->default(false);
            $table->unsignedInteger('dispatched_by')->nullable();
            $table->timestamp('dispatch_time')->nullable();
            $table->boolean('received')->default(false);
            $table->unsignedInteger('received_by')->nullable();
            $table->timestamp('receive_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_break_dispatches');
    }
};
