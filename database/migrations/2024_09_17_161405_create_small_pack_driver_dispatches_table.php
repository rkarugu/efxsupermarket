<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Model\WaNumerSeriesCode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('small_pack_driver_dispatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('route_id');
            $table->string('document_no',125);
            $table->boolean('received')->default(false);
            $table->unsignedInteger('received_by')->nullable();
            $table->dateTime('received_on')->nullable();
            $table->timestamps();
        });

        $data =[
            'code' => 'SDD',
            'module' => 'SMALL_PACK_DRIVER_DISPATCH',
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
        Schema::dropIfExists('small_pack_driver_dispatches');
    }
};
