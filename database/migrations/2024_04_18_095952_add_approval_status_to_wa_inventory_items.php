<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status\ApprovalStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wa_inventory_items', function (Blueprint $table) {
            $table->string('approval_status')->default(ApprovalStatus::Approved->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_items', function (Blueprint $table) {
            $table->string('approval_status')->default(ApprovalStatus::Approved->value);
        });
    }
};
