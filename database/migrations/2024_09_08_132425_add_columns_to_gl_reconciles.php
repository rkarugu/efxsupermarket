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
        Schema::table('gl_reconciles', function (Blueprint $table) {
            $table->string('status',125);
            $table->date('closed_on')->nullable();
            $table->unsignedInteger('closed_by')->nullable();
        });

        Schema::table('gl_reconcile_interest_expenses', function (Blueprint $table) {
            $table->string('document_no',125)->nullable();
        });

        WaNumerSeriesCode::create([
            'code' => 'GLRC',
            'module' => 'GL_RECON_CHARGES',
            'description' => 'Gl recon Charges',
            'starting_number' => 1,
            'type_number' => 27,
        ]);

        WaNumerSeriesCode::create([
            'code' => 'GLRI',
            'module' => 'GL_RECON_INTERESTS',
            'description' => 'Gl recon Interests',
            'starting_number' => 1,
            'type_number' => 27,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gl_reconciles', function (Blueprint $table) {
            $table->dropColumn(['status','closed_on','closed_by']);
        });

        Schema::table('gl_reconcile_interest_expenses', function (Blueprint $table) {
            $table->dropColumn(['document_no']);
        });

        WaNumerSeriesCode::where('code', 'GLRC')->delete();
        WaNumerSeriesCode::where('code', 'GLRI')->delete();
    }
};
