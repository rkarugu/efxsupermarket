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
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('inclusive_of_house_allowance')->default(false)->change();
            $table->boolean('eligible_for_overtime')->default(false)->after('inclusive_of_house_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('inclusive_of_house_allowance')->default(true)->change();
            $table->dropColumn('eligible_for_overtime');
        });
    }
};
