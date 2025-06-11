<?php

use Database\Seeders\AccountPayablesReportSeeder;
use Database\Seeders\GeneralLedgerReportSeeder;
use Database\Seeders\HrAndPayrollReportSeeder;
use Database\Seeders\HrConfigurationSeeder;
use Database\Seeders\IncentivesTableSeeder;
use Database\Seeders\InventoryReportSeeder;
use Database\Seeders\ModuleReportCategorySeeder;
use Database\Seeders\ModuleReportSeeder;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\PurchaesReportSeeder;
use Database\Seeders\SalesAndReceivablesReportSeeder;
use Database\Seeders\VehicleTypeSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            VehicleTypeSeeder::class,
            ModuleReportSeeder::class,
            ModuleReportCategorySeeder::class,
            SalesAndReceivablesReportSeeder::class,
            PurchaesReportSeeder::class,
            AccountPayablesReportSeeder::class,
            InventoryReportSeeder::class,
            GeneralLedgerReportSeeder::class,
            HrConfigurationSeeder::class,
            IncentivesTableSeeder::class,
            \Database\Seeders\SettingsSeeder::class,
            HrAndPayrollReportSeeder::class
        ]);
    }
}
