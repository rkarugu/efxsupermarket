<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->after('id', function (Blueprint $table) {
                $table->string('type')->nullable();
                $table->unsignedBigInteger('notifiable_id')->nullable();
                $table->string('notifiable_type')->nullable();
                $table->text('data')->nullable();
                $table->timestamp('read_at')->nullable();
            });
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('notifiable_id');
            $table->dropColumn('notifiable_type');
            $table->dropColumn('data');
            $table->dropColumn('read_at');
        });
    }
};
