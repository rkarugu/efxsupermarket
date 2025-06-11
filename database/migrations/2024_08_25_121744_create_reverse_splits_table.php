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
        Schema::create('reverse_splits', function (Blueprint $table) {
            $table->id();
            $table->integer('mother_item_id');
            $table->integer('mother_item_bin');
            $table->integer('child_item_id');
            $table->integer('child_item_bin');
            $table->integer('requested_child_quantity');
            $table->integer('expected_mother_quantity');
            $table->integer('approved_quantity')->nullable();
            $table->integer('requested_by');
            $table->integer('approved_by')->nullable();
            $table->string('approved_at')->nullable();
            $table->string('status')->default('pending')->comment('pending, approved, rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reverse_splits');
    }
};
