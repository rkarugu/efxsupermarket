<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GeneralExcelExport;
use App\Http\Controllers\Controller;
use App\Model\WaCustomer;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationTransferItem;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalesAnalysisReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-analysis-report';
        $this->title = 'Sales Analysis Report';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.salesreceiablesreports';
    }
    public function index(Request $request){

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $user = getLoggeduserProfile();
        $branch = 'Thika';
        try {
        $categories = WaInventoryCategory::all();
        if($request->category){
            $categories = WaInventoryCategory::where('id',$request->category)->get();
        }
    
            if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
                if($request->type && $request->type=='pdf'){
                    if($request->date &&  $request->todate){
                        $fromDate = $request->date . " 00:00:00";
                        $toDate = $request->todate . " 23:59:59";
                        $data = DB::table('wa_inventory_items')
                        ->select(
                            'wa_inventory_items.id as item_id',
                            'wa_inventory_items.stock_id_code as stock_id_code',
                            'wa_inventory_items.title as item_title',
                            'wa_inventory_items.wa_inventory_category_id as category_id',
                            'wa_inventory_items.standard_cost as standard_cost',
                            'wa_inventory_items.selling_price as selling_price',
                            DB::raw('(SELECT tax_managers.tax_value
                                FROM tax_managers 
                                WHERE tax_managers.id = wa_inventory_items.tax_manager_id) as tax_value'),
                            DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                                FROM wa_suppliers
                                LEFT JOIN wa_inventory_item_suppliers ON wa_suppliers.id = wa_inventory_item_suppliers.wa_supplier_id
                                WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                                ) as suppliers"),
                            DB::raw("(SELECT SUM(wa_internal_requisition_items.quantity) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?) ) as quantity"),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.standard_cost * wa_internal_requisition_items.quantity) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as actual_standard_cost'),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.vat_amount) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as vat_amount'),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.total_cost_with_vat) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as total_selling_price_with_vat'),
                            DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity) 
                                FROM wa_inventory_location_transfer_item_returns
                                LEFT JOIN  wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                                WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_inventory_location_transfer_item_returns.created_at) BETWEEN ? AND ?)) as return_quantity')
                        )->setBindings([$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate], 'select');
                        
                
                        if($request->category){
                            $data->where('wa_inventory_items.wa_inventory_category_id', '=', $request->category);
                        }
                
                        $data = $data->havingRaw('quantity > ?', [0])->get();
                        $itemCount = $data->count();
                    }  

                    ini_set('max_execution_time', '300'); 
                    ini_set('memory_limit', '2048M');


                    $pdf = Pdf::loadView('admin.salesreceiablesreports.sales_analysis_report_pdf', compact('user', 'branch', 'data', 'categories','itemCount'))->setPaper('a3', 'landscape');
    
                    return $pdf->download('sales-analysis' . $request->date . '-' . $request->todate . '.pdf');          
                  }
                  //excel
                  if($request->type && $request->type=='Excel'){
                    if($request->date &&  $request->todate){
                        $fromDate = $request->date . " 00:00:00";
                        $toDate = $request->todate . " 23:59:59";
                        $data = DB::table('wa_inventory_items')
                        ->select(
                            'wa_inventory_items.id as item_id',
                            'wa_inventory_items.stock_id_code as stock_id_code',
                            'wa_inventory_items.title as item_title',
                            'wa_inventory_items.wa_inventory_category_id as category_id',
                            'wa_inventory_items.standard_cost as standard_cost',
                            'wa_inventory_items.selling_price as selling_price',
                            DB::raw('(SELECT tax_managers.tax_value
                                FROM tax_managers 
                                WHERE tax_managers.id = wa_inventory_items.tax_manager_id) as tax_value'),
                            DB::raw('(SELECT category_description
                                FROM wa_inventory_categories
                                WHERE wa_inventory_categories.id = wa_inventory_items.wa_inventory_category_id
                            ) as category'),
                            DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                                FROM wa_suppliers
                                LEFT JOIN wa_inventory_item_suppliers ON wa_suppliers.id = wa_inventory_item_suppliers.wa_supplier_id
                                WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                                ) as suppliers"),
                            DB::raw("(SELECT SUM(wa_internal_requisition_items.quantity) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?) ) as quantity"),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.standard_cost * wa_internal_requisition_items.quantity) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as actual_standard_cost'),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.vat_amount) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as vat_amount'),
                            DB::raw('(SELECT SUM(wa_internal_requisition_items.total_cost_with_vat) 
                                FROM wa_internal_requisition_items 
                                WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_internal_requisition_items.created_at) BETWEEN ? AND ?)) as total_selling_price_with_vat'),
                            DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity) 
                                FROM wa_inventory_location_transfer_item_returns
                                LEFT JOIN  wa_inventory_location_transfer_items ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                                WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id AND (DATE(wa_inventory_location_transfer_item_returns.created_at) BETWEEN ? AND ?)) as return_quantity')
                        )->setBindings([$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate,$fromDate, $toDate], 'select');
                        
                
                        if($request->category){
                            $data->where('wa_inventory_items.wa_inventory_category_id', '=', $request->category);
                        }
                
                        $data = $data->havingRaw('quantity > ?', [0])->get();
                    }  

                    //excel calculation
                    $excelData = [];
                    foreach ($data as $dataItem){
                        $payload = [
                            'stockIdCode' => $dataItem->stock_id_code,
                            'itemTitle' => $dataItem->item_title,
                            'supplier' => $dataItem->suppliers,
                            'category' => $dataItem->category,
                            'quantity' => $dataItem->quantity,
                            'costExcl' =>  manageAmountFormat(($dataItem->standard_cost * 100) /  (100 + $dataItem->tax_value) ),
                            'costIncl' => manageAmountFormat($dataItem->standard_cost),
                            'priceExcl' => manageAmountFormat(($dataItem->selling_price * 100) /  (100 + $dataItem->tax_value) ),
                            'priceIncl' => manageAmountFormat($dataItem->selling_price),
                            'totalCostExcl' => manageAmountFormat(($dataItem->actual_standard_cost * 100) /  (100 + $dataItem->tax_value) ),
                            'totalCostIncl' => manageAmountFormat($dataItem->actual_standard_cost),
                            'totalSaleExcl' =>  manageAmountFormat(($dataItem->total_selling_price_with_vat  * 100) /  (100 + $dataItem->tax_value) ),
                            'totalSaleIncl' => manageAmountFormat($dataItem->total_selling_price_with_vat),
                            'profitOnExcl' => manageAmountFormat((($dataItem->total_selling_price_with_vat  * 100) /  (100 + $dataItem->tax_value))-(($dataItem->actual_standard_cost * 100) /  (100 + $dataItem->tax_value))),
                            'profitOnIncl' => manageAmountFormat(($dataItem->total_selling_price_with_vat)-($dataItem->actual_standard_cost)),
                            'margin' => manageAmountFormat(((($dataItem->total_selling_price_with_vat)-($dataItem->actual_standard_cost)) / ($dataItem->actual_standard_cost != 0 ? $dataItem->actual_standard_cost : 1) ) * 100) . "%",
                        ];
                        $excelData [] = $payload;
                    }

                    ini_set('max_execution_time', '300'); 
                    ini_set('memory_limit', '2048M');    
                    return ExcelDownloadService::download("sale margin report - $fromDate - $toDate", collect($excelData), ['STOCK ID CODE', 'TITLE', 'SUPPLIERS', 'CATEGORY', 'QUANTITY', 'COST EXCL',
                    'COST INCL','PRICE EXCL', 'PRICE INCL','TOTAL COST EXCL', 'TOTAL COST INCL',  'TOTAL SALE EXCL', 'TOTAL SALE INCL','PROFIT ON EXCL', 'PROFIT ON INCL', 'MARGIN']);          
                  }
            
               
                $breadcum = [$title => route('sales-per-supplier-per-route'), 'Listing' => ''];
                return view($basePath . '.sales_analysis_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission','categories'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();    
        }
       
        

    }

    public function dailySalesMargin()
    {
        if (!can('daily-sales-margin', 'sales-and-receivables-reports')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $model = 'daily-sales-margin-report';
        $title = 'Daily Sales Margin Report';
        $breadcum = [$title => route('daily-sales-margin'), 'Listing' => ''];
        
        $salesData = $this->getSalesData();

        return view('admin.salesreceiablesreports.daily-sales-margin-report', compact('model', 'breadcum', 'title', 'salesData'));
    }

    public function dailySalesMarginDownload()
    {
        $salesData = $this->getSalesData();
        
        $export_array = [];
        foreach($salesData as $saleData){
            $export_array[]=[
                $saleData->inventoryItem->stock_id_code,
                $saleData->inventoryItem->description,
                $saleData->inventoryItem->suppliers->first()?->name,
                $saleData->quantity_sold,
                $saleData->standard_cost,
                $saleData->total_standard_cost,
                $saleData->standard_cost_excl,
                $saleData->total_standard_cost_excl,
                $saleData->selling_price,
                $saleData->total_selling_price,
                $saleData->selling_price_excl,
                $saleData->total_selling_price_excl,
                $saleData->profit,
                $saleData->profit_excl,
                $saleData->margin,
            ];
        }

        $report_name = 'daily_sales_margin_' . date('Y_m_d_H_i_A');
        $date = date('Y-m-d');
        
        return ExcelDownloadService::download(
            $report_name, 
            collect($export_array), 
            [
                [
                    'Daily Sales Margin Report'
                ],
                [
                    "Date: $date"
                ],
                [''],
                [
                    'Item Id',
                    'Description',
                    'Supplier',
                    'Qty',
                    'Cost',
                    'Total Cost',
                    'Cost Excl',
                    'Total Cost Excl',
                    'Price',
                    'Total Price',
                    'Price Excl',
                    'Total Price Excl',
                    'Profit',
                    'Profit Excl',
                    'Margin'
                ]
            ]
        );
    }

    public function getSalesData()
    {
        return WaInventoryLocationTransferItem::with('inventoryItem.suppliers')
            ->withSum(['inventoryLocationTransferItemReturns as returned_quantity' => function ($query) {
                $query->where('received_quantity', '>', 0)
                ->where('return_status', '1')
                ->where('status', 'received');
            }], 'received_quantity')
            ->whereDate('created_at', Carbon::today())
            // ->whereDate('created_at', Carbon::parse('2024-05-24'))
            ->get()
            ->map(function ($saleData) {
                $saleData->quantity_sold = $saleData->quantity - $saleData->returned_quantity ?? 0;

                $saleData->total_standard_cost = $saleData->standard_cost * $saleData->quantity_sold;
                $saleData->standard_cost_excl = $this->calculateExclusive($saleData->vat_rate, $saleData->standard_cost);
                $saleData->total_standard_cost_excl = $saleData->standard_cost_excl * $saleData->quantity_sold;

                $saleData->total_selling_price = $saleData->selling_price * $saleData->quantity_sold;
                $saleData->selling_price_excl = $this->calculateExclusive($saleData->vat_rate, $saleData->selling_price);
                $saleData->total_selling_price_excl = $saleData->selling_price_excl * $saleData->quantity_sold;

                $saleData->profit = $saleData->total_selling_price - $saleData->total_standard_cost;
                $saleData->profit_excl = $saleData->total_selling_price_excl - $saleData->total_standard_cost_excl;

                if ($saleData->total_standard_cost) {
                    $saleData->margin = round($saleData->profit / $saleData->total_standard_cost * 100, 2);
                } else {
                    $saleData->margin = 0;
                }
                
                return $saleData;
            })
            ->sortBy('inventoryItem.description')
            ->values();
    }

    protected function calculateExclusive(float $vatRate, float $amount)
    {
        return round(($amount * 100) /  (100 + $vatRate), 2);
    }
}


