<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryManShiftCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_shift_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deliveryman_shift_id');
            $table->foreign('deliveryman_shift_id')->references('id')->on('delivery_man_shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('route_customer_id');
            $table->unsignedSmallInteger('visited')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_man_shift_customers');
    }
}
