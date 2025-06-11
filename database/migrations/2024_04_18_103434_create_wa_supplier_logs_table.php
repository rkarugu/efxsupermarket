<?php
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
        Schema::create('wa_supplier_logs', function (Blueprint $table) {
         
$table->increments('id');
            $table->string('supplier_code')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('telephone')->nullable();
            $table->string('facsimile')->nullable();
            $table->string('email')->nullable();
            $table->text('url')->nullable();
            $table->string('supplier_type')->nullable();
            $table->date('supplier_since')->nullable();
            $table->string('bank_reference')->nullable();
            $table->unsignedBigInteger('wa_payment_term_id')->nullable();
            $table->unsignedBigInteger('wa_currency_manager_id')->nullable();
            $table->string('remittance_advice')->nullable();
            $table->string('tax_group')->nullable();
            $table->timestamps();
            $table->string('service_type')->nullable()->comment('Types of services supplier is supplying – Goods, Services');
            $table->tinyInteger('tax_withhold')->default(0);
            $table->tinyInteger('professional_withholding')->default(0);
            $table->string('kra_pin')->nullable();
            $table->string('transport')->nullable()->comment('Transport – Own Collection or Delivery');
            $table->tinyInteger('purchase_order_blocked')->default(0);
            $table->tinyInteger('payments_blocked')->default(0);
            $table->string('blocked_note')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_swift')->nullable();
            $table->string('bank_cheque_payee')->nullable();
            $table->tinyInteger('is_verified')->default(0)->comment('This will be used to check if the supplier account is authentic or not');
            $table->string('portal_status')->default('pending');
            $table->tinyInteger('edit_status')->default(0);
      
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_supplier_logs');
    }
};