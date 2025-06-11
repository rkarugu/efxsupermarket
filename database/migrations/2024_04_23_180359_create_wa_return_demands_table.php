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
        Schema::create('wa_return_demands', function (Blueprint $table) {
            $table->id();
            $table->string('demand_no');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('wa_supplier_id');
            $table->double('demand_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_return_demands');
    }
};
