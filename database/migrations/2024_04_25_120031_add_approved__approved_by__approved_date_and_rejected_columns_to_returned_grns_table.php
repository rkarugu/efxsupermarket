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
        Schema::table('returned_grns', function (Blueprint $table) {
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->boolean('rejected')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returned_grns', function (Blueprint $table) {
            $table->dropColumn('approved');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_date');
            $table->dropColumn('rejected');
        });
    }
};
