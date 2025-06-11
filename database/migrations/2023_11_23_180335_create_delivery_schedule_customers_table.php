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
        Schema::create('delivery_schedule_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->foreignId('delivery_schedule_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('order_id');
            $table->integer('delivery_code');
            $table->string('delivery_code_status')->default('pending')->comment('pending, sent, approved');
            $table->timestamps();

            /**
             * $table->boolean('visited')->nullable()->default(false);
             * $table->string( 'order_id')->change();
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_schedule_customers');
    }
};
