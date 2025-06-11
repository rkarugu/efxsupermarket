<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function hasIndex($table, $index)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $conn->listTableIndexes($table);
        return isset($indexes[$index]);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to wa_location_store_uom table
        if (Schema::hasTable('wa_location_store_uom')) {
            Schema::table('wa_location_store_uom', function (Blueprint $table) {
                if (!$this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_uom_id_index')) {
                    $table->index('uom_id');
                }
                if (!$this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_location_id_index')) {
                    $table->index('location_id');
                }
                if (!$this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_uom_id_location_id_index')) {
                    $table->index(['uom_id', 'location_id']);
                }
            });
        }

        // Add indexes to wa_inventory_location_uom table
        if (Schema::hasTable('wa_inventory_location_uom')) {
            Schema::table('wa_inventory_location_uom', function (Blueprint $table) {
                if (!$this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_inventory_id_index')) {
                    $table->index('inventory_id');
                }
                if (!$this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_location_id_index')) {
                    $table->index('location_id');
                }
                if (!$this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_uom_id_index')) {
                    $table->index('uom_id');
                }
                if (!$this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_inventory_id_location_id_index')) {
                    $table->index(['inventory_id', 'location_id']);
                }
            });
        }

        // Add indexes to wa_unit_of_measures table
        if (Schema::hasTable('wa_unit_of_measures')) {
            Schema::table('wa_unit_of_measures', function (Blueprint $table) {
                if (!$this->hasIndex('wa_unit_of_measures', 'wa_unit_of_measures_id_index')) {
                    $table->index('id');
                }
                if (!$this->hasIndex('wa_unit_of_measures', 'wa_unit_of_measures_title_index')) {
                    $table->index('title');
                }
            });
        }

        // Add indexes to wa_location_and_stores table
        if (Schema::hasTable('wa_location_and_stores')) {
            Schema::table('wa_location_and_stores', function (Blueprint $table) {
                if (!$this->hasIndex('wa_location_and_stores', 'wa_location_and_stores_id_index')) {
                    $table->index('id');
                }
                if (!$this->hasIndex('wa_location_and_stores', 'wa_location_and_stores_location_name_index')) {
                    $table->index('location_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from wa_location_store_uom table
        if (Schema::hasTable('wa_location_store_uom')) {
            Schema::table('wa_location_store_uom', function (Blueprint $table) {
                if ($this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_uom_id_index')) {
                    $table->dropIndex(['uom_id']);
                }
                if ($this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_location_id_index')) {
                    $table->dropIndex(['location_id']);
                }
                if ($this->hasIndex('wa_location_store_uom', 'wa_location_store_uom_uom_id_location_id_index')) {
                    $table->dropIndex(['uom_id', 'location_id']);
                }
            });
        }

        // Remove indexes from wa_inventory_location_uom table
        if (Schema::hasTable('wa_inventory_location_uom')) {
            Schema::table('wa_inventory_location_uom', function (Blueprint $table) {
                if ($this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_inventory_id_index')) {
                    $table->dropIndex(['inventory_id']);
                }
                if ($this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_location_id_index')) {
                    $table->dropIndex(['location_id']);
                }
                if ($this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_uom_id_index')) {
                    $table->dropIndex(['uom_id']);
                }
                if ($this->hasIndex('wa_inventory_location_uom', 'wa_inventory_location_uom_inventory_id_location_id_index')) {
                    $table->dropIndex(['inventory_id', 'location_id']);
                }
            });
        }

        // Remove indexes from wa_unit_of_measures table
        if (Schema::hasTable('wa_unit_of_measures')) {
            Schema::table('wa_unit_of_measures', function (Blueprint $table) {
                if ($this->hasIndex('wa_unit_of_measures', 'wa_unit_of_measures_id_index')) {
                    $table->dropIndex(['id']);
                }
                if ($this->hasIndex('wa_unit_of_measures', 'wa_unit_of_measures_title_index')) {
                    $table->dropIndex(['title']);
                }
            });
        }

        // Remove indexes from wa_location_and_stores table
        if (Schema::hasTable('wa_location_and_stores')) {
            Schema::table('wa_location_and_stores', function (Blueprint $table) {
                if ($this->hasIndex('wa_location_and_stores', 'wa_location_and_stores_id_index')) {
                    $table->dropIndex(['id']);
                }
                if ($this->hasIndex('wa_location_and_stores', 'wa_location_and_stores_location_name_index')) {
                    $table->dropIndex(['location_name']);
                }
            });
        }
    }
};
