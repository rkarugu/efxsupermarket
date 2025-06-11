<?php

namespace Database\Seeders;

use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class GeneralLedgerReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // if (!Schema::hasTable('module_reports')) {
        //     Schema::create('module_reports', function ($table) {
        //         $table->id();
        //         $table->unsignedBigInteger('module_report_category_id');
        //         $table->string('report_title')->unique();
        //         $table->string('report_route');
        //         $table->string('report_model');
        //         $table->string('report_permission');
        //         $table->timestamps();
        //     });
        // }

        $generalLedgerReportsCategory = ModuleReportCategory::where('module_id', 5)
            ->first();

        $reports = [
            [
                'title' => 'Trial Balance Report',
                'route' => 'trial-balances.index',
                'model' => 'trial-balances',
                'permission' => 'trial-balances___view',
            ],
            [
                'title' => 'Detailed Trial Balance Report',
                'route' => 'trial-balances.detailed',
                'model' => 'trial-balances',
                'permission' => 'tgeneral-ledger-reports___detailed-trial-balance',
            ],
            [
                'title' => 'Trading Profit & Loss Report',
                'route' => 'trading-profit-and-loss.index',
                'model' => 'trading-profit-and-loss',
                'permission' => null,
            ],
            // [
            //     'title' => 'Profit & Loss Report',
            //     'route' => 'profit-and-loss.index',
            //     'model' => 'profit-and-loss',
            //     'permission' => null,
            // ],
            // [
            //     'title' => 'Profit & Loss Details Report',
            //     'route' => 'profit-and-loss.detailsAll',
            //     'model' => 'profit-and-loss',
            //     'permission' => null,
            // ],
            [
                'title' => 'Balance Sheet Report',
                'route' => 'balance-sheet.index',
                'model' => 'balance-sheet',
                'permission' => null,
            ],
            [
                'title' => 'Detailed Balance Sheet Report',
                'route' => 'statement-financical-position.detailsAll',
                'model' => 'balance-sheet',
                'permission' => null,
            ],
            [
                'title' => 'Transaction Summary Report',
                'route' => 'general-ledger.gl_transaction_summary',
                'model' => 'gl-transaction-report-summary',
                'permission' => 'general-ledger-reports___transaction-summary',
            ],
            [
                'title' => 'Detailed Transaction Summary Report',
                'route' => 'gl-reports.detailed-transaction-summary',
                'model' => 'detailed-transaction-summary',
                'permission' => 'general-ledger-reports___detailed-transaction-summary',
            ],
            // [
            //     'title' => 'Profit & Loss Monthly Report',
            //     'route' => 'profit-and-loss.monthlyProfitSummary',
            //     'model' => 'profit-and-loss',
            //     'permission' => 'general-ledger-reports___p-l-monthly-report',
            // ],
            // [
            //     'title' => 'Profit & Lossdgfdfs Monthly Report',
            //     'route' => 'profit-and-loss.monthlyProfitSummary',
            //     'model' => 'profit-and-loss',
            //     'permission' => 'general-ledger-reports___p-l-monthly-report',
            // ],

            [
                'title' => 'GL Account Update Report',
                'route' => 'admin.account-inquiry.update_report',
                'model' => 'account-inquiry',
                'permission' => 'general-ledger-reports___account-inquiry',
            ],
        ];

        foreach ($reports as $report) {
            ModuleReport::firstOrCreate(
                ['report_title' => $report['title']],
                [
                    'module_report_category_id' => $generalLedgerReportsCategory->id,
                    'report_route' => $report['route'],
                    'report_model' => $report['model'],
                    'report_permission' => $report['permission'],
                ]
            );
        }
    }
}
