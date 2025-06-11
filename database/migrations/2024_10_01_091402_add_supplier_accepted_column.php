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
            $table->boolean('supplier_accepted')->default(0);
        });

        WaPurchaseOrder::whereNotIn('status', ['DRAFT', 'PRELPO'])->update([
            'supplier_accepted' => true
        ]);
    }

    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('supplier_accepted');
        });
    }
};
