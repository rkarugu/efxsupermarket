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
        Schema::create('module_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_report_category_id');
            $table->string('report_title')->nullable();
            $table->string('report_model')->nullable();
            $table->string('report_permission')->nullable();
            $table->string('report_route')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_reports');
    }
};
