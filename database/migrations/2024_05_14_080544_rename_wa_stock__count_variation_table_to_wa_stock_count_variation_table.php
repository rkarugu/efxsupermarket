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
        Schema::rename('wa_stock__count_variation', 'wa_stock_count_variation');
            //
        }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('wa_stock_count_variation', 'wa_stock__count_variation');

    }
};
