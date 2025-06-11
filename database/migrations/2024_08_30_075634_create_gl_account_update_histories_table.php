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
        Schema::create('gl_account_update_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gl_trans_id');
            $table->unsignedInteger('created_by');
            $table->string('new_account');
            $table->string('old_account');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_account_update_histories');
    }
};
