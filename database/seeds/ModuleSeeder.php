<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            Module::create([
                'module_title' => $module,
            ]);
        }
    }
}
