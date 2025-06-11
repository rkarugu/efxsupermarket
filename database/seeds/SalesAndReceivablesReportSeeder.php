<?php

namespace Database\Seeders;

use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SalesAndReceivablesReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!Schema::hasTable('module_reports')) {
            Schema::create('module_reports', function ($table) {
                $table->id();
                $table->unsignedBigInteger('module_report_category_id');
                $table->string('report_title')->unique();
                $table->string('report_route');
                $table->string('report_model');
                $table->string('report_permission');
                $table->timestamps();
            });
        }

        $salesReportsCategory = ModuleReportCategory::where('module_id', 1)
            ->first();

            $salesreports = [
                [
                    'title' => 'Sales Summary Report',
                    'route' => 'summary_report.sales_summary',
                    'model' => 'summary_report',
                    'permission' => 'summary-report___sales_summary',
                ],
                [
                    'title' => 'Sales of Product by Date Report',
                    'route' => 'summary_report.detailed_sales_report',
                    'model' => 'detailed_sales_report',
                    'permission' => 'summary-report___detailed_sales_report',
                ],
                [
                    'title' => 'Sales by Date Report',
                    'route' => 'summary_report.sales_by_date_report',
                    'model' => 'sales_by_date_report',
                    'permission' => 'summary-report___sales_by_date_report',
                ],
                [
                    'title' => 'Daily Sales Report',
                    'route' => 'route-reports.daily-sales',
                    'model' => 'route-daily-sales-report',
                    'permission' => 'route-reports___weekly-sales-report',
                ],
                [
                    'title' => 'Promotion Sales Report',
                    'route' => 'sales-and-receivables-reports.promotion-sales-report',
                    'model' => 'promotion-sales-report',
                    'permission' => 'sales-and-receivables-reports___promotion-sales',
                ],
                [
                    'title' => 'Discount Sales Report',
                    'route' => 'discount-sales-report',
                    'model' => 'discount-sales-report',
                    'permission' => 'sales-and-receivables-reports___discount-sales',
                ],
                [
                    'title' => 'Sales Per Supplier Per Route Report',
                    'route' => 'sales-per-supplier-per-route',
                    'model' => 'sales-per-supplier-per-route',
                    'permission' => 'sales-and-receivables-reports___sales-per-supplier-per-route',
                ],
                [
                    'title' => 'Sales Margin Report',
                    'route' => 'sales-analysis-report',
                    'model' => 'sales-analysis-report',
                    'permission' => 'sales-and-receivables-reports___sales-analysis',
                ],
                [
                    'title' => 'Daily Sales Margin Report',
                    'route' => 'daily-sales-margin',
                    'model' => 'daily-sales-margin-report',
                    'permission' => 'sales-and-receivables-reports___daily-sales-margin',
                ],
                [
                    'title' => 'Shift Summary Report',
                    'route' => 'sales-and-receivables-reports.shift-summary',
                    'model' => null,
                    'permission' => 'sales-and-receivables-reports___shift-summary',
                ],
                [
                    'title' => 'Salesman Trip Summary Report',
                    'route' => 'sales-and-receivables-reports.salesman-trip-summary',
                    'model' => null,
                    'permission' => 'sales-and-receivables-reports___salesman-trip-summary',
                ],
                [
                    'title' => 'Route Profitability Report',
                    'route' => 'gross-profit.route-profitibility-report',
                    'model' => 'route-profitibility-report',
                    'permission' => 'route-profitibility-report___view',
                ],
                [
                    'title' => 'Dispatch Items Report',
                    'route' => 'dispatched_items.report',
                    'model' => 'dispatched_items_report',
                    'permission' => 'dispatch-pos-invoice-sales___dispatch-report',
                ],
                [
                    'title' => 'Inventory Valuation Report',
                    'route' => 'summary_report.inventory_sales_report',
                    'model' => 'dispatched_items_report',
                    'permission' => 'summary-report___inventory_sales_report',
                ],
                [
                    'title' => 'Customer Aging Analysis Report',
                    'route' => 'customer-aging-analysis.index',
                    'model' => 'customer-aging-analysis',
                    'permission' => 'sales-and-receivables-reports___customer-aging-analysis',
                ],
                [
                    'title' => 'Customer Statement Report',
                    'route' => 'sales-and-receivables-reports.customer_statement',
                    'model' => 'customer-statement',
                    'permission' => 'sales-and-receivables-reports___customer-statement',
                ],
                [
                    'title' => 'Loading Schedule vs Stocks Report',
                    'route' => 'sales-and-receivables-reports.loading-schedule-vs-sales-report',
                    'model' => 'loading-schedule-vs-sales-report',
                    'permission' => 'sales-and-receivables-reports___loading-schedule-vs-stock-report',
                ],
                [
                    'title' => 'Delivery Schedule Report',
                    'route' => 'sales-and-receivables-reports.delivery-schedule-report',
                    'model' => 'delivery-schedule-report',
                    'permission' => 'sales-and-receivables-reports___delivery-schedule-report',
                ],
                [
                    'title' => 'Customer Balances Report',
                    'route' => 'sales-and-receivables-reports.customer-balances-report',
                    'model' => 'customer-balances-report',
                    'permission' => 'sales-and-receivables-reports___customer-balances-report',
                ],
                [
                    'title' => 'Route Performance Report',
                    'route' => 'sales-and-receivables-reports.route-performance-report',
                    'model' => 'route-performance-report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Group Performance Report',
                    'route' => 'sales-and-receivables-reports.group-performance-report',
                    'model' => 'group-performance-report',
                    'permission' => 'sales-and-receivables-reports___group-performance-report',
                ],
                [
                    'title' => 'Customer Invoices Report',
                    'route' => 'sales-and-receivables-reports.customer_invoices',
                    'model' => null,
                    'permission' => 'sales-and-receivables-reports___customer_invoices',
                ],
                [
                    'title' => 'Unsigned Invoices Report',
                    'route' => 'sales-and-receivables-reports.unassigned_invoices',
                    'model' => null,
                    'permission' => 'sales-and-receivables-reports___customer_invoices',
                ],
                [
                    'title' => 'Daily Cash Receipt Summary Report',
                    'route' => 'sales-and-receivables-reports.daily-cash-receipt-summary',
                    'model' => null,
                    'permission' => 'sales-and-receivables-reports___daily-cash-receipt-summary',
                ],
                [
                    'title' => 'Vat Report',
                    'route' => 'customer-aging-analysis.vatReport',
                    'model' => 'total-vat-report',
                    'permission' => 'sales-and-receivables-reports___vat-report',
                ],
                [
                    'title' => 'ESD Vat Report',
                    'route' => 'customer-aging-analysis.esdVatReport',
                    'model' => 'esd-vat-report',
                    'permission' => 'sales-and-receivables-reports___vat-report',
                ],
                [
                    'title' => 'Till Direct Banking Report',
                    'route' => 'sales-and-receivables-reports.till-direct-banking-report',
                    'model' => 'till-direct-banking-report',
                    'permission' => 'sales-and-receivables-reports___till-direct-banking-report',
                ],
                [
                    'title' => 'Sales Vs Stocks Ledger Report',
                    'route' => 'summary_report.sales_vs_stocks_ledger',
                    'model' => 'till-direct-banking-report',
                    'permission' => null,
                ],
                [
                    'title' => 'Gross Profit Summary Report',
                    'route' => 'gross-profit.inventory-valuation-report',
                    'model' => 'dispatched_items_report',
                    'permission' => 'sales-and-receivables-reports___gross-profit',
                ],
                [
                    'title' => 'EOD Detailed Report',
                    'route' => 'summary_report.index',
                    'model' => 'summary_report',
                    'permission' => 'summary-report___detailed',
                ],
                [
                    'title' => 'EOD Summary Report',
                    'route' => 'summary_report.summaryindex',
                    'model' => 'summary_report',
                    'permission' => 'summary-report___summary',
                ],
                [
                    'title' => 'Dashboard Report',
                    'route' => 'dashboard_report.index',
                    'model' => 'dashboard_report',
                    'permission' => 'sales-and-receivables-reports___dashboard-report',
                ],
                [
                    'title' => 'Route Return Summary Report',
                    'route' => 'route-returns-summary-report',
                    'model' => 'route-return-summary-report',
                    'permission' => 'sales-and-receivables-reports___route-return-summary-report',
                ],
                [
                    'title' => 'Onsite Vs Offsite Shifts',
                    'route' => 'onsite-vs-offsite-shifts-report',
                    'model' => 'onsite-vs-offsite-shifts-report',
                    'permission' => 'sales-and-receivables-reports___onsite-vs-offsite-shifts-report',
                ],
                [
                    'title' => 'Summary Customer Statement',
                    'route' => 'reports.salesman_shift_report',
                    'model' => 'salesman-shifts-report',
                    'permission' => 'sales-and-receivables-reports___salesman_shift_report',
                ],
                [
                    'title' => 'Invoice Balancing Report',
                    'route' => 'sales-and-receivables-reports.invoice-balancing-report',
                    'model' => 'invoice-balancing-report',
                    'permission' => 'sales-and-receivables-reports___invoice-balancing-report',
                ],
                [
                    'title' => 'Unbalanced Invoices Report',
                    'route' => 'sales-and-receivables-reports.unbalanced-invoices-report',
                    'model' => 'unbalance_invoices_report',
                    'permission' => 'sales-and-receivables-reports___unbalanced_invoices_report',
                ],
                [
                    'title' => 'Sales Report',
                    'route' => 'sales-and-receivables-reports.sales-per-route',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Met Unmet Report',
                    'route' => 'sales-and-receivables-reports.unmet-customers',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Route Shifts Report',
                    'route' => 'sales-and-receivables-reports.route-shifts',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Tonnage Report',
                    'route' => 'sales-and-receivables-reports.route-tonnage',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Dozen Report',
                    'route' => 'sales-and-receivables-reports.route-dozens',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Cartons Report',
                    'route' => 'sales-and-receivables-reports.route-cartons',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Returns Report',
                    'route' => 'sales-and-receivables-reports.route-returns',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Customers Performance Report',
                    'route' => 'sales-and-receivables-reports.route-customers-reports',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Customer Items Report',
                    'route' => 'sales-and-receivables-reports.route-customer-reports',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Sales Vs Payments Report',
                    'route' => 'sales-and-receivables-reports.sales-vs-payments',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'End Of Day Operation Shifts',
                    'route' => 'operation_shifts.index',
                    'model' => 'operation-shift',
                    'permission' => 'sales-and-receivables-reports___operation-shift',
                ],
                [
                    'title' => 'Sales By centers Summary Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-summary',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Top centres Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-top-centers',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Top Customers Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-top-customers',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Dormant Customers Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-dormant_customers',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Global Sales Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-global-sales',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Global Sales Summary Report',
                    'route' => 'sales-and-receivables-reports.sales-by-center-global-sales-summary',
                    'model' => 'summary_report',
                    'permission' => 'sales-and-receivables-reports___route-performance-report',
                ],
                [
                    'title' => 'Missing Item Sale',
                    'route' => 'missing-items-sales.index',
                    'model' => 'missing-items-sales',
                    'permission' => 'sales-and-receivables-reports___missing-items-sales',
                ],
                [
                    'title' => 'POS overview Report',
                    'route' => 'pos-cash-sales.overview',
                    'model' => 'pos-overview-report',
                    'permission' => 'sales-and-receivables-reports___pos-overview-report',
                ],
                [
                    'title' => 'Reported Missing Items',
                    'route' => 'reported-missing-items.index',
                    'model' => 'reported-missing-items',
                    'permission' => 'sales-and-receivables-reports___reported-missing-items',
                ],
                [
                    'title' => 'Reported New Items',
                    'route' => 'reported-new-items.index',
                    'model' => 'reported-new-items',
                    'permission' => 'sales-and-receivables-reports___reported-new-items',
                ],
                [
                    'title' => 'Reported Price Conflicts',
                    'route' => 'reported-price-conflicts.index',
                    'model' => 'reported-price-conflicts',
                    'permission' => 'sales-and-receivables-reports___reported-price-conflicts',
                ],
                [
                    'title' => 'Salesman Performance Report',
                    'route' => 'salesman-performance-report',
                    'model' => 'salesman-performance-report',
                    'permission' => 'sales-and-receivables-reports___salesman-performance-report',
                ],
                [
                    'title' => 'Driver Performance Report',
                    'route' => 'driver-performance-report',
                    'model' => 'driver-performance-report',
                    'permission' => 'sales-and-receivables-reports___driver-performance-report',
                ],
                [
                    'title' => 'Competing Brands Report',
                    'route' => 'competing-brands.listing',
                    'model' => 'competing-brands-reports-model',
                    'permission' => 'sales-and-receivables-reports___competing-brands-reports',
                ],
                [
                    'title' => 'EOD Report',
                    'route' => 'eod_report.index',
                    'model' => 'eod-report-summaries',
                    'permission' => 'sales-and-receivables-reports___eod-report',
                ],
                [
                    'title' => 'Debtors Report',
                    'route' => 'debtors-report.index',
                    'model' => 'sales-and-receivables-reports',
                    'permission' => 'sales-and-receivables-reports___debtors-report',
                ],
               
            ];

            foreach ($salesreports as $report) {
                ModuleReport::firstOrCreate(
                    ['report_title' => $report['title']],
                    [
                        'module_report_category_id' => $salesReportsCategory->id,
                        'report_route' => $report['route'],
                        'report_model' => $report['model'],
                        'report_permission' => $report['permission'],
                    ]
                );
            }
    }
}
