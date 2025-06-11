<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_grns', function (Blueprint $table) {
            $table->boolean('invoiced')->default(0);
        });
    }
    
    public function down(): void
    {
        Schema::table('wa_grns', function (Blueprint $table) {
            $table->dropColumn('invoiced');
        });
    }
};
