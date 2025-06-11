<?php

namespace Database\Seeders;

use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InventoryReportSeeder extends Seeder
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

        $inventoryReportsCategory = ModuleReportCategory::where('module_id', 4)
            ->first();

        $reports = [
            [
                'title' => 'Valuation Report',
                'route' => 'summary_report.inventory_sales_report',
                'model' => 'inventory_sales_report',
                'permission' => 'inventory-reports___inventory-valuation-report',
            ],
            [
                'title' => 'Suggested Order (Reorder Level) Report',
                'route' => 'reports.suggested_order_report',
                'model' => 'maintain-items-suggested-order-report',
                'permission' => 'maintain-items___suggested_order_report',
            ],
            [
                'title' => 'Inventory -Ve Stock Report',
                'route' => 'reports.items_negetive_listing',
                'model' => 'items_negetive_listing',
                'permission' => 'maintain-items___negetive_stock_report',
            ],
            [
                'title' => 'Average Sales Vs Max Stock Report',
                'route' => 'inventory-reports.average-sales-report.index',
                'model' => 'average-sales-report',
                'permission' => 'inventory-reports___average-sales-report',
            ],
            [
                'title' => 'Max Stock Report',
                'route' => 'inventory-reports.max-stock-report.index',
                'model' => 'max-stock-report',
                'permission' => 'inventory-reports___max-stock-report',
            ],
            [
                'title' => 'Missing Items Report',
                'route' => 'inventory-reports.missing-items-report.index',
                'model' => 'missing-items-report',
                'permission' => 'inventory-reports___missing-items-report',
            ],
            [
                'title' => 'Discount Items Report',
                'route' => 'items-with-discounts-reports',
                'model' => 'discount-items-report',
                'permission' => 'inventory-reports___discount-items',
            ],
            [
                'title' => 'Promotion Items Report',
                'route' => 'items-with-promotions-reports',
                'model' => 'promotion-items-report',
                'permission' => 'inventory-reports___promotion-items',
            ],
            [
                'title' => 'Overstock Report',
                'route' => 'inventory-reports.overstock-report.index',
                'model' => 'overstock-report',
                'permission' => 'inventory-reports___overstock-report',
            ],
            [
                'title' => 'Inactive Report',
                'route' => 'inventory-reports.inactive-stock-report.index',
                'model' => 'inactive-stock-report',
                'permission' => 'inventory-reports___inactive-stock-report',
            ],
            [
                'title' => 'Dead Stock Report',
                'route' => 'inventory-reports.dead-stock-report.index',
                'model' => 'dead-stock-report',
                'permission' => 'inventory-reports___dead-stock-report',
            ],
            [
                'title' => 'Slow Moving Items Report',
                'route' => 'reports.slow_moving_items_report',
                'model' => 'slow-moving-items-report',
                'permission' => 'inventory-reports___slow-moving-items-report',
            ],
            [
                'title' => 'Child Vs Mother Qoh Report',
                'route' => 'child-vs-mother-qoh',
                'model' => 'child-vs-mother-qoh-report',
                'permission' => 'inventory-reports___child-vs-mother-qoh',
            ],
            [
                'title' => 'Missing Split Report',
                'route' => 'report.missingsplit-report',
                'model' => 'missing-split-report',
                'permission' => 'inventory-reports___missing-split-report',
            ],
            [
                'title' => 'CTN without Children Report',
                'route' => 'report.ctn-no-child',
                'model' => 'CTN-without-children',
                'permission' => 'inventory-reports___ctn-without-children',
            ],
            [
                'title' => 'Price Timeline Report',
                'route' => 'reports.price_timeline_report',
                'model' => 'price-timeline-report',
                'permission' => 'inventory-reports___price-timeline-report',
            ],
            [
                'title' => 'Items List Report',
                'route' => 'reports.items_list_report',
                'model' => 'items-list-report',
                'permission' => 'inventory-reports___items-list-report',
            ],
            [
                'title' => 'Reported Issues Report',
                'route' => 'procurement-reported-shift-issues.index',
                'model' => 'procurement-reported-shift-issues',
                'permission' => 'reported-shift-issues___view',
            ],
            [
                'title' => 'Supplier Sales Product Report',
                'route' => 'inventory-reports.supplier-product-reports',
                'model' => null,
                'permission' => 'inventory-reports___supplier-product-reports',
            ],
            [
                'title' => 'GRN Summary by Supplier Report',
                'route' => 'inventory-reports.grn-summary-by-supplier-report.index',
                'model' => 'grn-summary-by-supplier-report',
                'permission' => 'inventory-reports___grn-summary-by-supplier-report',
            ],
            [
                'title' => 'No Supplier Items Report',
                'route' => 'reports.no_supplier_items_report',
                'model' => 'no-supplier-items-report',
                'permission' => 'inventory-reports___no-supplier-items-report',
            ],
            [
                'title' => 'Supplier User Report',
                'route' => 'reports.supplier_user_report',
                'model' => 'supplier-user-report',
                'permission' => 'inventory-reports___supplier-user-report',
            ],
            [
                'title' => 'Sub Distributor Suppliers Report',
                'route' => 'reports.sub_distributor_report',
                'model' => 'sub-distributor-suppliers-report',
                'permission' => 'inventory-reports___sub-distributor-suppliers-report',
            ],
            [
                'title' => 'Location Stock Report',
                'route' => 'reports.inventory_location_stock_summary',
                'model' => 'inventory_location_stock_summary',
                'permission' => 'inventory-reports___inventory-location-stock-report',
            ],
            [
                'title' => 'Location Stock As At Report',
                'route' => 'reports.inventory_location_as_at',
                'model' => 'inventory_location_as_at',
                'permission' => 'inventory-reports___inventory-location-as-at',
            ],
            [
                'title' => 'Items Data Sales Report',
                'route' => 'reports.items-data-sales',
                'model' => 'items-data-sales',
                'permission' => 'inventory-reports___items-data-sales',
            ],
            [
                'title' => 'Items Data Purchases Report',
                'route' => 'reports.items_data_purchase_report',
                'model' => 'promotion-items-report',
                'permission' => 'inventory-reports___promotion-items',
            ],
            [
                'title' => 'Transfers Inwards Report',
                'route' => 'reports.transfer_inwards_report',
                'model' => 'transfer-inwards-report',
                'permission' => 'inventory-reports___transfer-inwards-report',
            ],
            [
                'title' => 'Item Sales Route Performance Report',
                'route' => 'reports.route_performance_report',
                'model' => 'item-sales-route-performance-report',
                'permission' => 'inventory-reports___item-sales-route-performance-report',
            ],
            [
                'title' => 'Inventory Sales Margin Report',
                'route' => 'sales-analysis-report',
                'model' => 'sales-analysis-report',
                'permission' => 'sales-and-receivables-reports___sales-analysis',
            ],
            [
                'title' => 'Missing Invoice Series Numbers',
                'route' => 'number-series-report.invoices-missing',
                'model' => 'number-series-report',
                'permission' => 'inventory-reports___missing_invoice_series_numbers',
            ],
            [
                'title' => 'Competing Brand Report',
                'route' => 'competing-brands.listing',
                'model' => 'competing-brands-reports-model',
                'permission' => 'sales-and-receivables-reports___competing-brands-reports',
            ],
            [
                'title' => 'Items Margins Report',
                'route' => 'item-margins-report.index',
                'model' => 'item-margins-report',
                'permission' => 'sales-and-receivables-reports___item-margins-report',
            ],
            [
                'title' => 'Price List Cost',
                'route' => 'price-list-costs-reports.index',
                'model' => 'price-list-cost-reports',
                'permission' => 'inventory-reports___price-list-cost-report',
            ],
            [
                'title' => 'Items With Multiple Suppliers',
                'route' => 'items-with-multiple-suppliers.index',
                'model' => 'multi-supplier-items-reports',
                'permission' => 'inventory-reports___items-with-multiple-suppliers',
            ],
            [
                'title' => 'Daily Moves Report',
                'route' => 'salesVsMovesReport.index',
                'model' => 'multi-supplier-items-reports',
                'permission' => 'inventory-reports___daily-moves-report',
            ],
        ];

        foreach ($reports as $report) {
            ModuleReport::firstOrCreate(
                ['report_title' => $report['title']],
                [
                    'module_report_category_id' => $inventoryReportsCategory->id,
                    'report_route' => $report['route'],
                    'report_model' => $report['model'],
                    'report_permission' => $report['permission'],
                ]
            );
        }
    }
}
