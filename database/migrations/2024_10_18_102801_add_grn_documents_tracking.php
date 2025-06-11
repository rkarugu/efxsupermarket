<?php

use App\Model\WaGrn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_grns', function (Blueprint $table) {
            $table->boolean('documents_sent')->default(0);
            $table->unsignedBigInteger('documents_sent_by')->nullable();
            $table->timestamp('documents_sent_at')->nullable();

            $table->boolean('documents_received')->default(0);
            $table->unsignedBigInteger('documents_received_by')->nullable();
            $table->timestamp('documents_received_at')->nullable();
        });

        // Mark old ones as sent
        WaGrn::query()->update([
            'documents_sent' => 1,
            'documents_received' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('wa_grns', function (Blueprint $table) {
            $table->dropColumn([
                'documents_sent',
                'documents_sent_by',
                'documents_sent_at',
                'documents_received',
                'documents_received_by',
                'documents_received_at',
            ]);
        });
    }
};
