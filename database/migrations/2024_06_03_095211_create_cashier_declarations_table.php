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
        Schema::create('cashier_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cashier_id')->nullable();
            $table->unsignedInteger('declared_by')->nullable();
            $table->timestamp('declared_at')->nullable();
            $table->decimal('balance', 10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_declarations');
    }
};
