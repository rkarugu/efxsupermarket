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
        Schema::create('pos_cash_payments', function (Blueprint $table) {
            $table->id();
            $table->string('document_no');
            $table->integer('initiated_by');
            $table->integer('amount');
            $table->integer('payee');
            $table->string('payment_reason')->nullable();
            $table->string('status')->default('Pending')->comment('Approved', 'Rejected');
            $table->integer('approved_by')->nullable();
            $table->string('approved_at')->nullable();
            $table->timestamps();
        });
        WaNumerSeriesCode::create([
            "code" => "PCP",
            "module" => "POS_CASH_PAYMENTS",
            "description" => "Pos Cash Payment",
            "last_date_used" => "1",
            "last_number_used" => "0",
            "type_number" => "211",
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_cash_payments');
        WaNumerSeriesCode::where("code", "BIL")->delete();
    }
};
