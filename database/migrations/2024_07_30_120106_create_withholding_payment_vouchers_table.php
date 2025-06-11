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
        Schema::create('withholding_payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedBigInteger('withholding_file_id');
            $table->unsignedBigInteger('withholding_account_id');
            $table->unsignedBigInteger('wa_bank_account_id');
            $table->string('cheque_number');
            $table->string('memo');
            $table->date('payment_date');
            $table->double('amount');
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('prepared_by');
            $table->timestamps();
        });

        Schema::table('wa_withholding_files', function (Blueprint $table) {
            $table->dropColumn('wa_bank_account_id');
        });

        WaNumerSeriesCode::create([
            'code' => 'WPV',
            'module' => 'WITHHOLDING_TAX_PAYMENT_VOUCHERS',
            'description' => 'Withholding Tax Payment',
            'starting_number' => 1,
            'type_number' => 13,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withholding_payment_vouchers');

        Schema::table('wa_withholding_files', function (Blueprint $table) {
            $table->unsignedBigInteger('wa_bank_account_id')->nullable();
        });

        WaNumerSeriesCode::where('code', 'WPV')->delete();
    }
};
