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
        Schema::table('employee_emergency_contacts', function (Blueprint $table) {
            $table->string('place_of_work')->nullable(true)->change();
            $table->string('id_no')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_emergency_contacts', function (Blueprint $table) {
            $table->string('place_of_work')->nullable(false)->change();
            $table->string('id_no')->nullable(false)->change();
        });
    }
};
