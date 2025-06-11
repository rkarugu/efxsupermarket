<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ModuleReportCategory;

class UpdateReportEntry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-report-entry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reports = [
            [
                'title' => 'Multi Supplier Items',
                'new_title' => 'Items With Multiple Suppliers',
                // 'new_route' => 'payment-vouchers-report.index',
                // 'new_model' => 'payment-vouchers-report',
                // 'new_permission' => 'payment-vouchers-report___view',
            ]
        ];

        foreach ($reports as $report) {

            $existingReport = DB::table('module_reports')
                ->where('report_title', $report['title'])
                ->update([
                    'report_title' => $report['new_title'],
                    // 'report_route' => $report['new_route'],
                    // 'report_model' => $report['new_model'],
                    // 'report_permission' => $report['new_permission'],
                ]);
        }
    }
}
