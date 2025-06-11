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
        Schema::create('wa_sub_account_sections', function (Blueprint $table) {
            $table->unsignedInteger('id', true);
            $table->string('section_name')->nullable();
            $table->string('section_code')->nullable();
            $table->string('slug')->nullable();
            $table->unsignedInteger('wa_account_section_id')->nullable()->index();
            $table->unsignedInteger('wa_account_group_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_sub_account_sections');
    }
};
