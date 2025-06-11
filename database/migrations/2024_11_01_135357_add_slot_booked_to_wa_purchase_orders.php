<?php

use App\Model\WaPurchaseOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->boolean('slot_booked')->default(0);
        });

        WaPurchaseOrder::query()->update([
            'slot_booked' => 1
        ]);
    }

    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('slot_booked');
        });
    }
};
