<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Models\CompetingBrand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CompetingBrandsReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Competing Brands Reports';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.competing_brands';
    }
    public function listing(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $items = WaInventoryItem::all(); 
        $competingBrands = CompetingBrand::with(['getRelatedItems', 'getRelatedUser'])
            ->withCount('getRelatedItems');
        if ($request->item){
            $competingBrands = $competingBrands->whereHas('getRelatedItems', function ($query) use ($request) {
                $query->where('competing_brand_items.wa_inventory_item_id', $request->item);
            });
        }
        $competingBrands = $competingBrands->get();
        
  
        if (isset($permission[$pmodule . '___competing-brands-reports']) || $permission == 'superadmin') {
            $breadcum = [$title => route('competing-brands.index'), 'Listing' => ''];
            return view('admin.competing_brands.listing', compact('title', 'model', 'breadcum', 'pmodule', 'permission','competingBrands', 'items'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function details($recordd){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = Restaurant::all();
        $competingBrand = CompetingBrand::with(['getRelatedItems', 'getRelatedUser'])->where('id', $recordd)->first();


        if (isset($permission[$pmodule . '___competing-brands-reports']) || $permission == 'superadmin') {
            $breadcum = [$title => route('competing-brands.index'), 'Listing' => ''];
            return view('admin.competing_brands.details', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'competingBrand', 'branches'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }
    public function tableData(Request $request, $brandId)
    {
        $branchFilter = "";
        $stocksFilter = "";
        $grnFilter = "";
        if($request->branch_filter && $request->branch_filter != '0'){
            $location = WaLocationAndStore::where('wa_branch_id', $request->branch_filter)->first();
            $branchFilter =   " AND wa_inventory_location_transfers.restaurant_id = " . $request->branch_filter;
            $stocksFilter =   " AND wa_stock_moves.wa_location_and_store_id = ". $location->id;
            $grnFilter = " AND wa_purchase_orders.restaurant_id = " . $request->branch_filter;
        }
        $start = $request->start;
        $end = $request->end;
        $smallPacksSubQuery = DB::table('wa_inventory_location_transfer_items')
                                ->select(
                                    'wa_inventory_assigned_items.wa_inventory_item_id',
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity)) AS small_pack_quantity_unconverted"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity"),

                                )
                                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                                ->whereBetween('wa_inventory_location_transfer_items.created_at', [$start. ' 00:00:00', $end . ' 23:59:59']);

        if($request->branch_filter && $request->branch_filter != '0'){
            $smallPacksSubQuery = $smallPacksSubQuery->where('wa_inventory_location_transfers.restaurant_id',  $request->branch_filter);
        }
        $smallPacksSubQuery = $smallPacksSubQuery->groupBy('wa_inventory_assigned_items.wa_inventory_item_id');

        $smallPacksReturnsSubQuery = DB::table('wa_inventory_location_transfer_item_returns')
                ->select(
                    'wa_inventory_assigned_items.wa_inventory_item_id',
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity)) AS small_pack_quantity_unconverted_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity_returns"),
                )
                ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_items.id', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id')
                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                ->where('wa_inventory_location_transfer_item_returns.return_status', '1')
                ->where('wa_inventory_location_transfer_item_returns.status', 'received')
                ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start. ' 00:00:00', $end . ' 23:59:59']);

        if($request->branch_filter && $request->branch_filter != '0'){
            $smallPacksReturnsSubQuery = $smallPacksReturnsSubQuery->where('wa_inventory_location_transfers.restaurant_id',  $request->branch_filter);
        }
        $smallPacksReturnsSubQuery = $smallPacksReturnsSubQuery->groupBy('wa_inventory_assigned_items.wa_inventory_item_id');


        $data =  DB::table('competing_brand_items')
                    ->select(
                        'wa_inventory_items.id',
                        'wa_inventory_items.stock_id_code',
                        'wa_inventory_items.title',
                        'wa_inventory_items.standard_cost',
                        'wa_inventory_items.selling_price',
                        DB::raw("(SELECT SUM(wa_stock_moves.qauntity)
                            FROM wa_stock_moves 
                            WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code
                            ". $stocksFilter."
                        ) as current_qoh"),
                        DB::raw("(SELECT (wa_grns.created_at) 
                            FROM wa_grns
                            LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                            LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                            WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                            ". $grnFilter."
                            ORDER BY wa_grns.created_at DESC
                            LIMIT 1
                        ) as last_grn_date"),
                        DB::raw("(SELECT (wa_purchase_order_items.created_at) 
                            FROM wa_grns
                            LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                            LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                            WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                            ". $grnFilter."
                            ORDER BY wa_purchase_order_items.created_at DESC
                            LIMIT 1
                        ) as last_lpo_date"),
                        DB::raw("(SELECT (wa_grns.qty_received) 
                            FROM wa_grns
                            LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                            LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                            WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                            ". $grnFilter."
                            ORDER BY wa_grns.created_at DESC
                            LIMIT 1
                        ) as last_grn_qoh"),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)
                            FROM wa_inventory_location_transfer_items
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN "' . $start . '" AND "' . $end . '")

                        ) AS invoices_sum_total'),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.standard_cost)
                            FROM wa_inventory_location_transfer_items
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN "' . $start . '" AND "' . $end . '")

                        ) AS invoices_cost_total'),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_items.quantity)
                            FROM wa_inventory_location_transfer_items
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN "' . $start . '" AND "' . $end . '")

                        ) AS invoices_qty_total'),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) 
                            FROM wa_inventory_location_transfer_item_returns
                            JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            AND wa_inventory_location_transfer_item_returns.return_status = "1"
                            AND wa_inventory_location_transfer_item_returns.status = "received"
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '")
                        ) AS invoices_return'),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.standard_cost) 
                            FROM wa_inventory_location_transfer_item_returns
                            JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            AND wa_inventory_location_transfer_item_returns.return_status = "1"
                            AND wa_inventory_location_transfer_item_returns.status = "received"
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '")
                        ) AS invoices_cost_return'),
                        DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity) 
                            FROM wa_inventory_location_transfer_item_returns
                            JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                            JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                            WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                            AND wa_inventory_location_transfer_item_returns.return_status = "1"
                            AND wa_inventory_location_transfer_item_returns.status = "received"
                            '. "$branchFilter".'
                            AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '")
                        ) AS invoices_return_qty'),
                        DB::raw("(SELECT (wa_suppliers.name) 
                            FROM wa_grns
                            LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                            LEFT JOIN wa_suppliers ON wa_grns.wa_supplier_id = wa_suppliers.id
                            LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                            WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                            ". $grnFilter."
                            ORDER BY wa_grns.created_at DESC
                            LIMIT 1
                        ) as supplier"),
                        DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_sales, 0), 2) as pack_sales'),
                        DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_quantity, 0), 2) as pack_quantity'),
                        DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_cost, 0), 2) as pack_sales_cost'),

                        DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_sales_returns, 0), 2) as pack_sales_returns'),
                        DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_quantity_returns, 0), 2) as pack_quantity_returns'),
                        DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_cost_returns, 0), 2) as pack_sales_cost_returns'),

                    )
                        ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'competing_brand_items.wa_inventory_item_id')   
                        ->leftJoinSub($smallPacksSubQuery, 'smallPacksSales', 'smallPacksSales.wa_inventory_item_id', 'wa_inventory_items.id')  
                        ->leftJoinSub($smallPacksReturnsSubQuery, 'smallPacksReturns', 'smallPacksReturns.wa_inventory_item_id', 'wa_inventory_items.id')  
                        ->where('competing_brand_id', $brandId)
                        ->groupBy('wa_inventory_items.id')
                        ->orderBy('invoices_sum_total', 'desc')
                        ->get()->map(function ($record){
                            $record->margin = $record->selling_price - $record->standard_cost;
                            $record->sales = ($record->invoices_sum_total - $record->invoices_return) +  ($record->pack_sales - $record->pack_sales_returns);
                            $record->cost = ($record->invoices_cost_total  - $record->invoices_cost_return) + ($record->pack_sales_cost - $record->pack_sales_cost_returns);
                            $record->qty_sold = ($record->invoices_qty_total - $record->invoices_return_qty) + ($record->pack_quantity - $record->pack_quantity_returns);
                            $record->selling_price = manageAmountFormat($record->selling_price);
                            $record->last_grn_date = Carbon::parse($record->last_grn_date)->toDateString();
                            $record->last_lpo_date = Carbon::parse($record->last_lpo_date)->toDateString();
                            $record->computed_margin = $record->sales - $record->cost;
                            return $record;
                        });
                $totalSales = manageAmountFormat($data->sum('sales'));
                $totalCost = manageAmountFormat($data->sum('cost'));
                $totalComputedMargin = manageAmountFormat($data->sum('computed_margin'));
            return response()->json(['data' =>$data, 'totalSales' =>$totalSales, 'totalCost' =>$totalCost, 'totalComputedMargin' =>$totalComputedMargin]);
    }
    public function piechart(Request $request, $brandId)
    {
        $branchFilter = "";
        $stocksFilter = "";
        $grnFilter = "";
        if($request->branch_filter && $request->branch_filter != '0'){
            $location = WaLocationAndStore::where('wa_branch_id', $request->branch_filter)->first();
            $branchFilter =   " AND wa_inventory_location_transfers.restaurant_id = " . $request->branch_filter;
            $stocksFilter =   " AND wa_stock_moves.wa_location_and_store_id = ". $location->id;
            $grnFilter = " AND wa_purchase_orders.restaurant_id = " . $request->branch_filter;
        }
       
        $start = $request->start;
        $end = $request->end;
        $smallPacksSubQuery = DB::table('wa_inventory_location_transfer_items')
                ->select(
                    'wa_inventory_assigned_items.wa_inventory_item_id',
                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales"),
                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost"),
                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity)) AS small_pack_quantity_unconverted"),
                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity"),

                )
                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                ->whereBetween('wa_inventory_location_transfer_items.created_at', [$start. ' 00:00:00', $end . ' 23:59:59']);
                

        if($request->branch_filter && $request->branch_filter != '0'){
            $smallPacksSubQuery = $smallPacksSubQuery->where('wa_inventory_location_transfers.restaurant_id',  $request->branch_filter);
        }
        $smallPacksSubQuery = $smallPacksSubQuery->groupBy('wa_inventory_assigned_items.wa_inventory_item_id');
        $smallPacksReturnsSubQuery = DB::table('wa_inventory_location_transfer_item_returns')
                ->select(
                'wa_inventory_assigned_items.wa_inventory_item_id',
                DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales_returns"),
                DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost_returns"),
                DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity)) AS small_pack_quantity_unconverted_returns"),
                DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity_returns"),
                )
                ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_items.id', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id')
                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                ->where('wa_inventory_location_transfer_item_returns.return_status', '1')
                ->where('wa_inventory_location_transfer_item_returns.status', 'received')
                ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start. ' 00:00:00', $end . ' 23:59:59']);

        if($request->branch_filter && $request->branch_filter != '0'){
            $smallPacksReturnsSubQuery = $smallPacksReturnsSubQuery->where('wa_inventory_location_transfers.restaurant_id',  $request->branch_filter);
        }
        $smallPacksReturnsSubQuery = $smallPacksReturnsSubQuery->groupBy('wa_inventory_assigned_items.wa_inventory_item_id');

           $data =  DB::table('competing_brand_items')
                ->select(
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'wa_inventory_items.standard_cost',
                    'wa_inventory_items.selling_price',
                    DB::raw("(SELECT SUM(wa_stock_moves.qauntity)
                        FROM wa_stock_moves 
                        WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code
                        ". $stocksFilter."
                    ) as current_qoh"),
                    DB::raw("(SELECT (wa_grns.created_at) 
                        FROM wa_grns
                        LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                        LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                        WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                        ". $grnFilter."
                        ORDER BY wa_grns.created_at DESC
                        LIMIT 1
                    ) as last_grn_date"),
                    DB::raw("(SELECT (wa_purchase_order_items.created_at) 
                        FROM wa_grns
                        LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                        LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                        WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                        ". $grnFilter."
                        ORDER BY wa_purchase_order_items.created_at DESC
                        LIMIT 1
                    ) as last_lpo_date"),
                    DB::raw("(SELECT (wa_grns.qty_received) 
                        FROM wa_grns
                        LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                        LEFT JOIN wa_purchase_orders ON wa_grns.wa_purchase_order_id = wa_purchase_orders.id
                        WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                        ". $grnFilter."
                        ORDER BY wa_grns.created_at DESC
                        LIMIT 1
                    ) as last_grn_qoh"),
                    DB::raw('(SELECT SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)
                        FROM wa_inventory_location_transfer_items
                        JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                        WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                        '. "$branchFilter".'
                        AND (DATE(wa_inventory_location_transfer_items.created_at) BETWEEN "' . $start . '" AND "' . $end . '")

                    ) AS invoices_sum_total'),
                    DB::raw('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) 
                        FROM wa_inventory_location_transfer_item_returns
                        JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                        JOIN wa_inventory_location_transfers ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                        WHERE wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id
                        AND wa_inventory_location_transfer_item_returns.return_status = "1"
                        AND wa_inventory_location_transfer_item_returns.status = "received"
                        '. "$branchFilter".'
                        AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '")
                    ) AS invoices_return'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_sales, 0), 2) as pack_sales'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_quantity, 0), 2) as pack_quantity'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_cost, 0), 2) as pack_sales_cost'),

                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_sales_returns, 0), 2) as pack_sales_returns'),
                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_quantity_returns, 0), 2) as pack_quantity_returns'),
                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_cost_returns, 0), 2) as pack_sales_cost_returns'),
                    )
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'competing_brand_items.wa_inventory_item_id')
                ->leftJoinSub($smallPacksSubQuery, 'smallPacksSales', 'smallPacksSales.wa_inventory_item_id', 'wa_inventory_items.id')  
                ->leftJoinSub($smallPacksReturnsSubQuery, 'smallPacksReturns', 'smallPacksReturns.wa_inventory_item_id', 'wa_inventory_items.id')
                ->where('competing_brand_id', $brandId)
                ->groupBy('wa_inventory_items.id')
                ->orderBy('invoices_sum_total', 'desc')
                ->get()->map(function ($record){
                    $record->sales = manageAmountFormat( ($record->invoices_sum_total - $record->invoices_return)  +  ($record->pack_sales - $record->pack_sales_returns) );
                    $record->selling_price = manageAmountFormat($record->selling_price);
                    return $record;
                });
                // dd($data);
            return response()->json(['data' =>$data]);
    }
    public function tableDataDetails(Request $request, $itemId)
    {
        $branchFilter = "";
        $stocksFilter = "";
        $grnFilter = "";
        if($request->branch_filter && $request->branch_filter != '0'){
            $location = WaLocationAndStore::where('wa_branch_id', $request->branch_filter)->first();
            $branchFilter =   " AND wa_inventory_location_transfers.restaurant_id = " . $request->branch_filter;
            $stocksFilter =   " AND wa_stock_moves.wa_location_and_store_id = ". $location->id;
            $grnFilter = " AND wa_purchase_orders.restaurant_id = " . $request->branch_filter;
        }
        $start = $request->start;
        $end = $request->end;
        $smallPacksSubQuery = DB::table('wa_inventory_location_transfer_items')
                                ->select(
                                    'wa_inventory_assigned_items.wa_inventory_item_id',
                                    DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m') AS month_year"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity)) AS small_pack_quantity_unconverted"),
                                    DB::raw("(SUM(wa_inventory_location_transfer_items.quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity"),
                                )
                                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                                ->where('wa_inventory_assigned_items.wa_inventory_item_id', $itemId)
                                ->whereBetween('wa_inventory_location_transfer_items.created_at', [$start. ' 00:00:00', $end . ' 23:59:59']);

        if($request->branch_filter && $request->branch_filter != '0'){
            $smallPacksSubQuery->where('wa_inventory_location_transfers.restaurant_id ',  $request->branch_filter);
        }
        $smallPacksSubQuery = $smallPacksSubQuery->groupBy('month_year');

        $smallPacksReturnsSubQuery = DB::table('wa_inventory_location_transfer_item_returns')
                ->select(
                    'wa_inventory_assigned_items.wa_inventory_item_id',
                    DB::raw("DATE_FORMAT(wa_inventory_location_transfer_item_returns.updated_at, '%Y-%m') AS month_year"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)) AS small_pack_sales_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.standard_cost)) AS small_pack_cost_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity)) AS small_pack_quantity_unconverted_returns"),
                    DB::raw("(SUM(wa_inventory_location_transfer_item_returns.received_quantity / wa_inventory_assigned_items.conversion_factor )) AS small_pack_quantity_returns"),
                )
                ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_items.id', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id')
                ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->leftJoin('wa_inventory_assigned_items', 'wa_inventory_assigned_items.destination_item_id', 'wa_inventory_location_transfer_items.wa_inventory_item_id')
                ->where('wa_inventory_location_transfer_item_returns.return_status', '1')
                ->where('wa_inventory_location_transfer_item_returns.status', 'received')
                ->where('wa_inventory_assigned_items.wa_inventory_item_id', $itemId)
                ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start. ' 00:00:00', $end . ' 23:59:59']);

        if($request->branch_filter && $request->branch_filter != '0'){
        $smallPacksReturnsSubQuery->where('wa_inventory_location_transfers.restaurant_id ',  $request->branch_filter);
        }
        $smallPacksReturnsSubQuery = $smallPacksReturnsSubQuery->groupBy('month_year');

        $data =  DB::table('wa_inventory_location_transfer_items')
                ->select(
                    DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.updated_at, '%Y-%m') AS month_year"),
                    DB::raw("SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.selling_price) AS invoices_sum_total"),
                    DB::raw("SUM(wa_inventory_location_transfer_items.quantity * wa_inventory_location_transfer_items.standard_cost) AS invoices_cost_total"),
                    DB::raw("SUM(wa_inventory_location_transfer_items.quantity) AS invoices_qty_total"),
                    DB::raw('(IFNULL(return_subquery.total_return_qty, 0)) AS invoices_return_qty'),
                    DB::raw('(IFNULL(return_subquery_cost.total_return_cost, 0)) AS invoices_cost_return'),
                    DB::raw('(IFNULL(return_subquery_selling_price.total_return_selling_price, 0)) AS invoices_return'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_sales, 0), 2) as pack_sales'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_quantity, 0), 2) as pack_quantity'),
                    DB::raw('ROUND(IFNULL(smallPacksSales.small_pack_cost, 0), 2) as pack_sales_cost'),

                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_sales_returns, 0), 2) as pack_sales_returns'),
                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_quantity_returns, 0), 2) as pack_quantity_returns'),
                    DB::raw('ROUND(IFNULL(smallPacksReturns.small_pack_cost_returns, 0), 2) as pack_sales_cost_returns'),


             
                )
                ->leftJoin(DB::raw('(
                    SELECT 
                        DATE_FORMAT(wa_inventory_location_transfer_item_returns.updated_at, "%Y-%m") AS month_year,
                        SUM(wa_inventory_location_transfer_item_returns.received_quantity) AS total_return_qty
                    FROM 
                        wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    WHERE 
                        wa_inventory_location_transfer_items.wa_inventory_item_id =  "' . $itemId . '"
                        AND wa_inventory_location_transfer_item_returns.return_status = "1"
                        AND wa_inventory_location_transfer_item_returns.status = "received"
                        '. "$branchFilter".'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '"
                    GROUP BY 
                        month_year
                ) AS return_subquery'), 'return_subquery.month_year', '=', DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m')"))
                ->leftJoin(DB::raw('(
                    SELECT 
                        DATE_FORMAT(wa_inventory_location_transfer_item_returns.updated_at, "%Y-%m") AS month_year,
                        SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.standard_cost) AS total_return_cost
                    FROM 
                        wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    WHERE 
                        wa_inventory_location_transfer_items.wa_inventory_item_id =  "' . $itemId . '"
                        AND wa_inventory_location_transfer_item_returns.return_status = "1"
                        AND wa_inventory_location_transfer_item_returns.status = "received"
                        '. "$branchFilter".'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '"
                    GROUP BY 
                        month_year
                ) AS return_subquery_cost'), 'return_subquery_cost.month_year', '=', DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m')"))
                ->leftJoin(DB::raw('(
                    SELECT 
                        DATE_FORMAT(wa_inventory_location_transfer_item_returns.updated_at, "%Y-%m") AS month_year,
                        SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_return_selling_price
                    FROM 
                        wa_inventory_location_transfer_item_returns
                    LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                    LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_items.wa_inventory_location_transfer_id
                    WHERE 
                        wa_inventory_location_transfer_items.wa_inventory_item_id =  "' . $itemId . '"
                        AND wa_inventory_location_transfer_item_returns.return_status = "1"
                        AND wa_inventory_location_transfer_item_returns.status = "received"
                        '. "$branchFilter".'
                        AND DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN "' . $start . '" AND "' . $end . '"
                    GROUP BY 
                        month_year
                ) AS return_subquery_selling_price'), 'return_subquery_selling_price.month_year', '=', DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m')"))
                ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfers.id', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id')
                ->leftJoinSub($smallPacksSubQuery, 'smallPacksSales', 'smallPacksSales.month_year', DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m')"))  
                ->leftJoinSub($smallPacksReturnsSubQuery, 'smallPacksReturns', 'smallPacksReturns.month_year', DB::raw("DATE_FORMAT(wa_inventory_location_transfer_items.created_at, '%Y-%m')"))  
                ->whereBetween('wa_inventory_location_transfer_items.created_at',[$start.' 00:00:00', $end.' 23:59:59'])
                ->where('wa_inventory_location_transfer_items.wa_inventory_item_id', $itemId);
        if($request->branch_filter && $request->branch_filter != '0'){
            $data->where('wa_inventory_location_transfers.restaurant_id ',  $request->branch_filter);
        }
        $data = $data->groupBy('month_year')
                ->get()->map(function ($record){
                    $record->sales = ($record->invoices_sum_total - $record->invoices_return) +  ($record->pack_sales - $record->pack_sales_returns);
                    $record->cost = ($record->invoices_cost_total  - $record->invoices_cost_return) + ($record->pack_sales_cost - $record->pack_sales_cost_returns);
                    $record->qty_sold = ($record->invoices_qty_total - $record->invoices_return_qty) + ($record->pack_quantity - $record->pack_quantity_returns);
                    $record->computed_margin = $record->sales - $record->cost;
                    return $record;
                });;

            return response()->json(['data' =>$data]);
    }
}
