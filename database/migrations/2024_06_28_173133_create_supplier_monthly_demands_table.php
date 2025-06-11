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
        Schema::create('wa_supplier_monthly_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_supplier_id');
            $table->foreign('wa_supplier_id')->references('id')->on('wa_suppliers');
            $table->dateTime('selected_month');
            $table->dateTime('started_from');
            $table->dateTime('ended_at');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_supplier_monthly_demands');
    }
};
