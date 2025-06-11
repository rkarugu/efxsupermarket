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
        Schema::create('trade_discount_demands', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('demand_no');
            $table->double('amount');
            $table->unsignedBigInteger('prepared_by');
            $table->string('supplier_reference')->nullable();
            $table->string('cu_invoice_number')->nullable();
            $table->date('note_date')->nullable();
            $table->string('memo')->nullable();
            $table->boolean('processed')->default(0);
            $table->timestamp('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('credit_note_no')->nullable();
            $table->timestamps();
        });

        Schema::create('trade_discount_demand_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_discount_demand_id');
            $table->unsignedBigInteger('trade_discount_id');
            $table->double('amount');
            $table->timestamps();
        });

        WaNumerSeriesCode::create([
            'code' => 'TRD',
            'module' => 'TRADE_DISCOUNT_DEMANDS',
            'description' => 'Trade discount demand',
            'starting_number' => 1,
            'type_number' => 15,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_discount_demands');
        Schema::dropIfExists('trade_discount_demand_items');

        WaNumerSeriesCode::where('code', 'TRD')->delete();
    }
};
