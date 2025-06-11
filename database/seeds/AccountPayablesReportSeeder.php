<?php

namespace Database\Seeders;

use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccountPayablesReportSeeder extends Seeder
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

        $accountPayablesCategory = ModuleReportCategory::where('module_id', 3)
            ->first();


        $reports = [
            [
                'title' => 'Supplier Ageing Analysis Report',
                'route' => 'supplier-aging-analysis.index',
                'model' => 'supplier-aging-analysis',
                'permission' => 'sales-and-receivables-reports___customer-aging-analysis',
            ],
            [
                'title' => 'Vat Report',
                'route' => 'vat-report.index',
                'model' => 'vat-report',
                'permission' => 'vat-report___view',
            ],
            [
                'title' => 'Supplier Listing Report',
                'route' => 'supplier-listing.index',
                'model' => 'supplier-listing',
                'permission' => 'supplier-listing___view',
            ],
            [
                'title' => 'Supplier Bank Listing Report',
                'route' => 'supplier-bank-listing.index',
                'model' => 'supplier-bank-listing',
                'permission' => 'supplier-bank-listing___view',
            ],
            [
                'title' => 'Supplier Statement Report',
                'route' => 'maintain-suppliers.supplier-statement',
                'model' => 'supplier-statement',
                'permission' => 'supplier-statement___view',
            ],
            [
                'title' => 'Supplier Ledger Report',
                'route' => 'maintain-suppliers.supplier-ledger-report',
                'model' => 'supplier-ledger-report',
                'permission' => 'supplier-ledger-report___view',
            ],
            [
                'title' => 'Payment Vouchers Report',
                'route' => 'payment-vouchers-report.index',
                'model' => 'payment-vouchers-report',
                'permission' => 'payment-vouchers-report___view',
            ],
            [
                'title' => 'GRNs Against Invoices Report',
                'route' => 'grns-against-invoices.index',
                'model' => 'grns-against-invoices',
                'permission' => 'grns-against-invoices___view',
            ],
            [
                'title' => 'Bank Payments Report',
                'route' => 'bank-payments-report.index',
                'model' => 'bank-payments-report',
                'permission' => 'bank-payments-report___view',
            ],
            [
                'title' => 'Withholding TAX Payments Report',
                'route' => 'withholding-tax-payments-report.index',
                'model' => 'withholding-tax-payments-report',
                'permission' => 'withholding-tax-payments-report___view',
            ],            
            [
                'title' => 'Trade Discounts Report',
                'route' => 'trade-discounts-report.index',
                'model' => 'trade-discounts-report',
                'permission' => 'trade-discounts-report___view',
            ],
            [
                'title' => 'Trade Discount Demands Report',
                'route' => 'trade-discount-demands-report.index',
                'model' => 'trade-discount-demands-report',
                'permission' => 'trade-discount-demands-report___view',
            ],
        ];

        foreach ($reports as $report) {
            ModuleReport::firstOrCreate(
                ['report_title' => $report['title']],
                [
                    'module_report_category_id' => $accountPayablesCategory->id,
                    'report_route' => $report['route'],
                    'report_model' => $report['model'],
                    'report_permission' => $report['permission'],
                ]
            );
        }
    }
}
