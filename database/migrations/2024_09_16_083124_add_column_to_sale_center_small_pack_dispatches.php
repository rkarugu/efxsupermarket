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
        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->string('document_no',125)->nullable();
        });

        $data =[
            'code' => 'SPD',
            'module' => 'SMALL_PACK_DISPATCH',
            'description' => 'Small Pack Dispatch ',
            'starting_number' => 1,
            'type_number' => 27,
        ];

        $exists = WaNumerSeriesCode::where('code',$data['code'])->exists();

        if (!$exists) {
            WaNumerSeriesCode::create($data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->dropColumn(['document_no']);
        });

        WaNumerSeriesCode::where('code', 'SMPD')->delete();
    }
};
