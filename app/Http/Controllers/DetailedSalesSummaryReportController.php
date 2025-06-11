<?php

namespace App\Http\Controllers;

use App\Exports\SalesSummaryReturnsCommbinedExport;
use App\Exports\StockSalesAndReturnsCombined;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DetailedSalesSummaryReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'detailed-sales-summary-report';
        $this->title = 'Detailed Sales Summary Report';
        $this->pmodule = 'detailed-sales-summary-report';
        $this->basePath = 'admin.promotions';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'summary-report';
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $tax_manager_id  = $request->vat;
        $vat_rate = 0;
        $date = $request->date;
        if($tax_manager_id ==  1){
            $vat_rate = 16;
        }
        if($request->type && $request->type == 'all'){
            $salesData = DB::table('wa_internal_requisitions')
            ->select(
                'wa_internal_requisitions.requisition_no as invoice_no',
                'wa_internal_requisitions.created_at as invoice_date',
                'routes.route_name as route',
                DB::raw("(SELECT  (SUM(wa_internal_requisition_items.total_cost_with_vat))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS total_sales"),
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vatable_total_sales"),
                DB::raw("(SELECT  (SUM(wa_internal_requisition_items.vat_amount))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vat_amount"),
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.vat_amount ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vatable_vat_amount")
            )
            ->leftJoin('routes', 'routes.id', '=', 'wa_internal_requisitions.route_id')
            ->whereDate('wa_internal_requisitions.created_at', $request->date);
            if($request->branch){
                $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            }
            $salesData =  $salesData->get();
            $returnsData = DB::table('wa_inventory_location_transfer_item_returns')
            ->select(
                'routes.route_name as route',
                'wa_inventory_location_transfers.transfer_no AS invoice_no',
                'wa_inventory_location_transfer_item_returns.return_number  AS return_no',
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS return_value'),
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.vat_amount / wa_inventory_location_transfer_items.quantity)) AS return_value_tax'),
                'tax_managers.tax_value AS vat_rate'
                )
            ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
            ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
            ->leftJoin('routes', 'routes.id', '=', 'wa_inventory_location_transfers.route_id')
            ->leftJoin('tax_managers', 'tax_managers.id', '=', 'wa_inventory_items.tax_manager_id')
            // ->where('wa_inventory_items.tax_manager_id', $tax_manager_id)
            ->where('wa_inventory_location_transfer_item_returns.return_status',  1)
            ->where('wa_inventory_location_transfer_item_returns.status',  'received')
            ->whereDate('wa_inventory_location_transfer_item_returns.updated_at', $request->date);
            if($request->branch){
                $returnsData  = $returnsData->where('routes.restaurant_id', $request->branch);
            }
        $returnsData = $returnsData->get();
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.summary_report.detailed_sales_summary_report_all', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesData', 'returnsData', 'date'));

        }
        $salesData = DB::table('wa_internal_requisitions')
            ->select(
                'wa_internal_requisitions.requisition_no as invoice_no',
                'wa_internal_requisitions.created_at as invoice_date',
                'routes.route_name as route',
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = $tax_manager_id THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS total_sales"),
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = $tax_manager_id THEN wa_internal_requisition_items.vat_amount ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vat_amount")
            )
            ->leftJoin('routes', 'routes.id', '=', 'wa_internal_requisitions.route_id')
            ->whereDate('wa_internal_requisitions.created_at', $request->date);
            if($request->branch){
                $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            }
            $salesData =  $salesData->get();
        $returnsData = DB::table('wa_inventory_location_transfer_item_returns')
            ->select(
                'routes.route_name as route',
                'wa_inventory_location_transfers.transfer_no AS invoice_no',
                'wa_inventory_location_transfer_item_returns.return_number  AS return_no',
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS return_value'),
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.vat_amount / wa_inventory_location_transfer_items.quantity)) AS return_value_tax')
                )
            ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
            ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
            ->leftJoin('routes', 'routes.id', '=', 'wa_inventory_location_transfers.route_id')
            ->where('wa_inventory_items.tax_manager_id', $tax_manager_id)
            ->where('wa_inventory_location_transfer_item_returns.return_status',  1)
            ->where('wa_inventory_location_transfer_item_returns.status',  'received')
            ->whereDate('wa_inventory_location_transfer_item_returns.updated_at', $request->date);
            if($request->branch){
                $returnsData  = $returnsData->where('routes.restaurant_id', $request->branch);
            }
        $returnsData = $returnsData->get();            
        if (isset($permission[$pmodule . '___sales_summary']) || $permission == 'superadmin') {
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.summary_report.detailed_sales_summary_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesData', 'returnsData', 'vat_rate'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function excel(Request $request)
    {
        $salesData = DB::table('wa_internal_requisitions')
            ->select(
                'wa_internal_requisitions.requisition_no as invoice_no',
                'wa_internal_requisitions.created_at as invoice_date',
                'routes.route_name as route',
                DB::raw("(SELECT  (SUM(wa_internal_requisition_items.total_cost_with_vat))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS total_sales"),
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.total_cost_with_vat ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vatable_total_sales"),
                DB::raw("(SELECT  (SUM(wa_internal_requisition_items.vat_amount))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vat_amount"),
                DB::raw("(SELECT  (SUM(CASE WHEN wa_internal_requisition_items.tax_manager_id = '1' THEN wa_internal_requisition_items.vat_amount ELSE 0 END))
                FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) AS vatable_vat_amount")
            )
            ->leftJoin('routes', 'routes.id', '=', 'wa_internal_requisitions.route_id')
            ->whereDate('wa_internal_requisitions.created_at', $request->date);
            if($request->branch){
                $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            }
            $salesData =  $salesData->get();
            $salesExportData = [];

            foreach ($salesData as $sales) {
                $payload  = [
                    'Route' => $sales->route,
                    'Invoice No' => $sales->invoice_no,
                    'Vatable Total Sales' => manageAmountFormat($sales->vatable_total_sales - $sales->vat_amount),
                    'VAT Amount' => $sales->vat_amount,
                    'Total Sales' => $sales->total_sales,
                ] ;
                $salesExportData[] = $payload;
            }

            $returnsData = DB::table('wa_inventory_location_transfer_item_returns')
            ->select(
                'routes.route_name as route',
                'wa_inventory_location_transfers.transfer_no AS invoice_no',
                'wa_inventory_location_transfer_item_returns.return_number  AS return_no',
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS return_value'),
                DB::raw('(wa_inventory_location_transfer_item_returns.received_quantity * (wa_inventory_location_transfer_items.vat_amount / wa_inventory_location_transfer_items.quantity)) AS return_value_tax'),
                'tax_managers.tax_value AS vat_rate'
                )
            ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
            ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', '=', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id')
            ->leftJoin('routes', 'routes.id', '=', 'wa_inventory_location_transfers.route_id')
            ->leftJoin('tax_managers', 'tax_managers.id', '=', 'wa_inventory_items.tax_manager_id')
            ->where('wa_inventory_location_transfer_item_returns.return_status',  1)
            ->where('wa_inventory_location_transfer_item_returns.status',  'received')
            ->whereDate('wa_inventory_location_transfer_item_returns.updated_at', $request->date);
            if($request->branch){
                $returnsData  = $returnsData->where('routes.restaurant_id', $request->branch);
            }
        $returnsData = $returnsData->get();
        $returnsExportData = [];
        foreach ($returnsData as $returns) {
            $payload  = [
                    'Route' => $returns->route,
                    'Invoice No' => $returns->invoice_no,
                    'Return No' => $returns->return_no,
                    'Vatable Return Value' => manageAmountFormat($returns->return_value - (($returns->vat_rate * $returns->return_value ) / (100 + $returns->vat_rate))),
                    'Return Value Tax' => manageAmountFormat((($returns->vat_rate * $returns->return_value ) / (100+ $returns->vat_rate))),
                    'Total' => manageAmountFormat($returns->return_value),
                ] ;
                $returnsExportData[] = $payload;
        }
        return Excel::download(new SalesSummaryReturnsCommbinedExport(collect($salesExportData), collect($returnsExportData)), 'detailed_sales_summary_export_'.$request->date.'.xlsx');

    }
    public function stockSalesIndex(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'summary-report';
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $tax_manager_id  = $request->vat;
        $vat_rate = 0;
        $date = $request->date;
        if($tax_manager_id ==  1){
            $vat_rate = 16;
        }
        if($request->type && $request->type == 'all'){
            $salesData = DB::table('stock_debtor_trans')
            ->select(
                'stock_debtor_trans.document_no as document_no',
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
                ) AS total_sales"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id
                ) AS total_tax"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND stock_debtor_tran_items.vat_percentage = '16'
                ) AS taxable_sales"),
            )
            ->where('stock_debtor_trans.document_no', 'like', 'SAS%')
            ->whereDate('stock_debtor_trans.created_at', $request->date);
            // if($request->branch){
            //     $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            // }
            $salesData =  $salesData->get();
            $returnsData = DB::table('stock_debtor_trans')
            ->select(
                'stock_debtor_trans.document_no as document_no',
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
                ) AS total_sales"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
                ) AS total_tax"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND stock_debtor_tran_items.vat_percentage = '16'
                ) AS taxable_returns"),
            )
            ->where('stock_debtor_trans.document_no', 'like', 'SAR%')
            ->whereDate('stock_debtor_trans.created_at', $request->date);
        
        $returnsData = $returnsData->get(); 
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.summary_report.stock_sales_summary_detailed_all', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesData', 'returnsData', 'vat_rate','date'));

        }
        $salesData = DB::table('stock_debtor_trans')
            ->select(
                'stock_debtor_trans.document_no as document_no',
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND wa_inventory_items.tax_manager_id = $tax_manager_id
                ) AS total_sales"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND wa_inventory_items.tax_manager_id = $tax_manager_id
                ) AS total_tax"),
                 
            )
            ->where('stock_debtor_trans.document_no', 'like', 'SAS%')
            ->whereDate('stock_debtor_trans.created_at', $request->date);
            // if($request->branch){
            //     $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            // }
            $salesData =  $salesData->get();
            $returnsData = DB::table('stock_debtor_trans')
            ->select(
                'stock_debtor_trans.document_no as document_no',
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND wa_inventory_items.tax_manager_id = $tax_manager_id
                ) AS total_sales"),
                DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
                stock_debtor_tran_items
                LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
                WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND wa_inventory_items.tax_manager_id = $tax_manager_id
                ) AS total_tax"),
            )
            ->where('stock_debtor_trans.document_no', 'like', 'SAR%')
            ->whereDate('stock_debtor_trans.created_at', $request->date);
            // if($request->branch){
            //     $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
            // }
        
        $returnsData = $returnsData->get();            
        if (isset($permission[$pmodule . '___sales_summary']) || $permission == 'superadmin') {
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.summary_report.stock_sales_summary_detailed', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'salesData', 'returnsData', 'vat_rate','date'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function  stockSalesExcel(Request $request)
    {
        $salesData = DB::table('stock_debtor_trans')
        ->select(
            'stock_debtor_trans.document_no as document_no',
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
            stock_debtor_tran_items
            LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
            ) AS total_sales"),
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
            stock_debtor_tran_items
            LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id
            ) AS total_tax"),
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
            stock_debtor_tran_items
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND stock_debtor_tran_items.vat_percentage = '16'
            ) AS taxable_sales"),
        )
        ->where('stock_debtor_trans.document_no', 'like', 'SAS%')
        ->whereDate('stock_debtor_trans.created_at', $request->date);
        // if($request->branch){
        //     $salesData  = $salesData->where('routes.restaurant_id', $request->branch);
        // }
        $salesData =  $salesData->get();
        $salesExportData = [];
        foreach ($salesData as $sale) {
            $salesExportData[] = [
                'Document No' => $sale->document_no,
                'Taxable Sales' => manageAmountFormat(($sale->taxable_sales ?? 0) - ($sale->total_tax ?? 0)),
                'Total Tax' => manageAmountFormat($sale->total_tax ?? 0),
                'Total Sales' => manageAmountFormat($sale->total_sales ?? 0),
            ];
        }
        $returnsData = DB::table('stock_debtor_trans')
        ->select(
            'stock_debtor_trans.document_no as document_no',
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
            stock_debtor_tran_items
            LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
            ) AS total_sales"),
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.vat)) FROM
            stock_debtor_tran_items
            LEFT JOIN wa_inventory_items ON wa_inventory_items.id = stock_debtor_tran_items.inventory_item_id
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id 
            ) AS total_tax"),
            DB::raw("(SELECT (SUM(stock_debtor_tran_items.total)) FROM
            stock_debtor_tran_items
            WHERE stock_debtor_tran_items.stock_debtor_trans_id = stock_debtor_trans.id AND stock_debtor_tran_items.vat_percentage = '16'
            ) AS taxable_returns"),
        )
        ->where('stock_debtor_trans.document_no', 'like', 'SAR%')
        ->whereDate('stock_debtor_trans.created_at', $request->date);
        $returnsData = $returnsData->get(); 

        $returnsExportData = [];
        foreach ($returnsData as $return) {
            $returnsExportData[] = [
                'Document No' => $return->document_no ,
                'Taxable Returns' => manageAmountFormat(($return->taxable_returns ?? 0) - ($return->total_tax ?? 0)),
                'Total Tax' => manageAmountFormat($return->total_tax ?? 0),
                'Total Sales' => manageAmountFormat($return->total_sales ?? 0),
            ];
        }

        return Excel::download(new StockSalesAndReturnsCombined(collect($salesExportData), collect($returnsExportData)), 'detailed_stock_take_sales_summary_export_'.$request->date.'.xlsx');

    


    }

}
