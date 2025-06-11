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
        WaNumerSeriesCode::create([
            'code' => 'RSTB',
            'module' => 'REVERSE_STOCKBREAKING',
            'description' => 'Reverse Stock Breaking',
            'starting_number' => 1,
            'type_number' => 27,
        ]);
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaNumerSeriesCode::where('code', 'RSTB')->delete();

    }
};
