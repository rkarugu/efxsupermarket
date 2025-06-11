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
        Schema::create('suggested_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_supplier_id')->nullable();
            $table->foreign('wa_supplier_id')->references('id')->on('wa_suppliers')->nullOnDelete();
            $table->string('order_number');
            $table->date('order_date');
            $table->string('status')->nullable()->default('Pending');
            $table->text('reject_reason')->nullable();
            $table->dateTime('finished_at')->nullable(); // Indicating when this order is completed or rejected.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggested_orders');
    }
};
