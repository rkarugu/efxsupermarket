<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RunReportsSeeders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-reports-seeders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all seeders except VehicleTypeSeeder';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    //     $seeders = [
    //         'ModuleReportSeeder' => 'modules',
    //         'ModuleReportCategorySeeder' => 'module_report_categories',
    //         'SalesAndReceivablesReportSeeder' => 'module_reports',
    //         'PurchaesReportSeeder' => 'module_reports',
    //         'AccountPayablesReportSeeder' => 'module_reports',
    //         'InventoryReportSeeder' => 'module_reports',
    //         'GeneralLedgerReportSeeder' => 'module_reports',
    //     ];

    //     $truncatedTables = [];

    //     foreach ($seeders as $seeder => $table) {
    //         if (!in_array($table, $truncatedTables)) {
    //             DB::table($table)->truncate();
    //             $truncatedTables[] = $table;
    //         }

    //         Artisan::call("db:seed", ['--class' => $seeder]);
    //     }

    //     $this->info('All specified seeders have been run successfully.');

    //     return 0;
    // }

    public function handle()
    {
        $seeders = [
            'ModuleReportSeeder' => 'modules',
            'ModuleReportCategorySeeder' => 'module_report_categories',
            'SalesAndReceivablesReportSeeder' => 'module_reports',
            'PurchaesReportSeeder' => 'module_reports',
            'AccountPayablesReportSeeder' => 'module_reports',
            'InventoryReportSeeder' => 'module_reports',
            'GeneralLedgerReportSeeder' => 'module_reports',
        ];

        foreach ($seeders as $seeder => $table) {
            if (!Schema::hasTable($table)) {
                Artisan::call("migrate");
            }

            Artisan::call("db:seed", ['--class' => $seeder]);
        }

        $this->info('All specified seeders have been run successfully.');

        return 0;
    }
}
