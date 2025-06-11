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
        Schema::create('sale_center_small_pack_dispatch_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dispatch_id');
            $table->unsignedInteger('bin_id');
            $table->boolean('dispatched')->default(false);
            $table->timestamp('dispatch_time')->nullable();
            $table->unsignedInteger('dispatcher_id')->nullable();
            $table->boolean('received')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_center_small_pack_dispatch_statuses');
    }
};
