<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ModuleReportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function ($table) {
                $table->id();
                $table->unsignedBigInteger('module_id');
                $table->string('category_title');
                $table->timestamps();
            });
        }

        $modules = Module::all();

        foreach ($modules as $module) {
            ModuleReportCategory::firstOrCreate(
                [
                    'category_title' => 'Sales Reports',
                    'module_id' => $module->id,
                ]
            );
        }
    }
}
