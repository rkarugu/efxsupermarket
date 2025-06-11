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
        Schema::create('field_visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->unsignedInteger('route_id');
            $table->string('hq_rep')->nullable();
            $table->string('hq_rep_contact')->nullable();
            $table->string('bw_rep')->nullable();
            $table->string('bw_rep_contact')->nullable();
            $table->longText('comments')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visit_schedules');
    }
};
