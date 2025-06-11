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
        Schema::create('block_users_exemption_schedule_users', function (Blueprint $table) {
            $table->id();
            $table->integer('schedule_id');
            $table->integer('user_id');
            $table->integer('added_by');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_users_exemption_schedule_users');
    }
};
