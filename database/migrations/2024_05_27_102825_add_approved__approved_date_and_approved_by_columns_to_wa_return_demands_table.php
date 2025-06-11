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
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('processed');
            $table->timestamp('approved_date')->nullable()->after('approved');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_date');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->dropColumn('approved');
            $table->dropColumn('approved_date');
            $table->dropColumn('approved_by');
        });
    }
};
