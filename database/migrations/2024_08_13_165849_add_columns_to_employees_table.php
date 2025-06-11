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
            $table->foreignId('line_manager_id')->nullable()->after('payment_mode_id')->references('id')->on('employees');
            $table->boolean('is_line_manager')->default(false)->after('inclusive_of_house_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('line_manager_id');
            $table->dropColumn('is_line_manager');
        });
    }
};
