<?php

use App\Model\WaNumerSeriesCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no');
            $table->string('cu_invoice_number');
            $table->string('supplier_invoice_number');
            $table->unsignedInteger('wa_supplier_id');
            $table->date('bill_date');
            $table->unsignedInteger('location_id');
            $table->string('memo');
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('withholding_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('status');
            $table->string('file_name');
            $table->timestamps();
        });

        Schema::create('supplier_bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('supplier_bill_id');
            $table->unsignedInteger('account_id');
            $table->string('memo')->nullable();
            $table->decimal('tax_rate', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('withholding_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        WaNumerSeriesCode::create([
            "code" => "BIL",
            "module" => "SUPPLIER_BILLS",
            "description" => "Supplier Bills",
            "last_date_used" => "1",
            "last_number_used" => "0",
            "type_number" => "103",
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_bills');

        Schema::dropIfExists('supplier_bill_items');

        WaNumerSeriesCode::where("code", "BIL")->delete();
    }
};
