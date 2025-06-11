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
        Schema::table('employee_beneficiaries', function (Blueprint $table) {
            $table->double('percentage')->after('guardian_phone_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_beneficiaries', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
