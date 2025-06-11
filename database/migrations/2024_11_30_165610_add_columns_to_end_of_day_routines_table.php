<?php

use App\Model\WaNumerSeriesCode;
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
        Schema::table('end_of_day_routines', function (Blueprint $table) {
            $table->index('day', 'idx_day');
            
        });
        WaNumerSeriesCode::create([
            "code" => "EOD",
            "module" => "END_OF_DAY",
            "description" => "End of Day",
            "last_date_used" => "1",
            "last_number_used" => "0",
            "type_number" => "113",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('end_of_day_routines', function (Blueprint $table) {
            $table->dropIndex('idx_day');
            WaNumerSeriesCode::where("code", "EOD")->delete();

        });
    }
};
