<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ModuleReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function ($table) {
                $table->id();
                $table->string('module_title');
                $table->timestamps();
            });
        }

        $modules = [
            'Sales & Receivables',
            'Purchases',
            'Account Payables',
            'Inventory',
            'General Ledger',
            'Fleet Management',
            'System Administration',
        ];
       
        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['module_title' => $module],
            );
        }
    }
}
