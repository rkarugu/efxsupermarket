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
        Schema::create('casuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gender_id')->constrained();
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('restaurants');
            $table->foreignId('nationality_id')->constrained();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('id_no');
            $table->string('phone_no');
            $table->string('email')->nullable();
            $table->date('date_of_birth');
            $table->boolean('active')->default(true);
            $table->string('reason')->nullable();
            $table->longText('narration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casuals');
    }
};
