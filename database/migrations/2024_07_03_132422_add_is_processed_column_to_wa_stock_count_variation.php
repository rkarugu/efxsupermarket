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
        if (!Schema::hasColumn('wa_stock_count_variation', 'is_processed'))
        {
            Schema::table('wa_stock_count_variation', function (Blueprint $table) {
                $table->boolean('is_processed')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('wa_stock_count_variation', 'is_processed'))
        {
            Schema::table('wa_stock_count_variation', function (Blueprint $table) {
                $table->dropColumn('is_processed');
            });
        }
        
    }
};
