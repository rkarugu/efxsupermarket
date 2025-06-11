<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalStatusOptionsToWaInternalRequisitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE wa_internal_requisitions MODIFY COLUMN status ENUM('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'COMPLETED', 'PAID', 'DELIVERED') NOT NULL DEFAULT 'UNAPPROVED'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
