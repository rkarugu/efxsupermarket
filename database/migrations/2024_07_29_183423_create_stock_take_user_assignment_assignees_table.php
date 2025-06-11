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
        Schema::create('stock_take_user_assignment_assignees', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_take_user_assignment_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('stock_take_user_assignment', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_take_user_assignment_assignees');
    }
};
