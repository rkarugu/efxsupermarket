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
        Schema::table('item_promotions', function (Blueprint $table) {
            // Add foreign key constraints that might be missing
            
            // Check if promotion_type_id foreign key exists, if not add it
            if (!$this->foreignKeyExists('item_promotions', 'item_promotions_promotion_type_id_foreign')) {
                $table->foreign('promotion_type_id')->references('id')->on('promotion_types')->onDelete('cascade');
            }
            
            // Check if promotion_group_id foreign key exists, if not add it
            if (!$this->foreignKeyExists('item_promotions', 'item_promotions_promotion_group_id_foreign')) {
                $table->foreign('promotion_group_id')->references('id')->on('promotion_groups')->onDelete('set null');
            }
            
            // Check if wa_demand_id foreign key exists, if not add it
            if (!$this->foreignKeyExists('item_promotions', 'item_promotions_wa_demand_id_foreign')) {
                $table->foreign('wa_demand_id')->references('id')->on('wa_demands')->onDelete('cascade');
            }
            
            // Check if supplier_id foreign key exists, if not add it
            if (!$this->foreignKeyExists('item_promotions', 'item_promotions_supplier_id_foreign')) {
                $table->foreign('supplier_id')->references('id')->on('wa_suppliers')->onDelete('cascade');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('item_promotions', 'status')) {
                $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_promotions', function (Blueprint $table) {
            // Drop foreign keys if they exist
            if ($this->foreignKeyExists('item_promotions', 'item_promotions_promotion_type_id_foreign')) {
                $table->dropForeign(['promotion_type_id']);
            }
            
            if ($this->foreignKeyExists('item_promotions', 'item_promotions_promotion_group_id_foreign')) {
                $table->dropForeign(['promotion_group_id']);
            }
            
            if ($this->foreignKeyExists('item_promotions', 'item_promotions_wa_demand_id_foreign')) {
                $table->dropForeign(['wa_demand_id']);
            }
            
            if ($this->foreignKeyExists('item_promotions', 'item_promotions_supplier_id_foreign')) {
                $table->dropForeign(['supplier_id']);
            }
            
            // Drop status column if it exists
            if (Schema::hasColumn('item_promotions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
    
    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists($table, $foreignKey)
    {
        $foreignKeys = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($table);
            
        foreach ($foreignKeys as $key) {
            if ($key->getName() === $foreignKey) {
                return true;
            }
        }
        
        return false;
    }
};
