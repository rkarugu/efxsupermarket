<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Models\ReportedMissingItem;
use App\SalesmanShift;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportedMissingItemsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Missing Items Sales';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.missing_items_sales';
    }
    public  function reportMissingItems(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 402);
            }
            if($user->role_id == 4){
               $shift = SalesmanShift::where('salesman_id', $user->id)
                    ->whereDate('created_at', Carbon::now()->toDateString())
                    ->where('status', 'open')
                    ->first();
                if(!$shift){
                    return response()->json(['status' => false, 'message' => 'You do not have an open shift'], 402);
                }

            }
            foreach($request->items as $data){
                $item = WaInventoryItem::find($data['item_id']);
                $report  = new ReportedMissingItem();
                $report->reported_by  =  $user->id;
                $report->item_id = $data['item_id'];
                $report->quantity = $data['missing_qty'];
                $report->as_at_quantity = WaStockMove::where('stock_id_code', $item->stock_id_code)->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity');
                $report->wa_location_and_store_id = $user->wa_location_and_store_id;
                $report->save();
            }
            return response()->json(['status' => true, 'message' => 'Missing items reported successfully'], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }
       
    }

    public function getReportedMissingItems(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 402);
            }
            // $reportedItems = ReportedMissingItem::with('getRelatedItem')
            //     // ->whereDate('created_at', Carbon::now()->toDateString())
            //     ->where('reported_by', $user->id)
            //     ->get();
            $reportedItems = DB::table('reported_missing_items')
                ->select(
                    DB::raw("(SUM(reported_missing_items.quantity)) AS quantity"),
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.id',
                    'wa_inventory_items.title',

                )
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'reported_missing_items.item_id')
                ->where('reported_missing_items.reported_by', $user->id)
                ->whereDate('reported_missing_items.created_at', Carbon::now()->toDateString())
                ->groupBy('reported_missing_items.reported_by','reported_missing_items.item_id', DB::raw('DATE(reported_missing_items.created_at)'))
                ->get();    
            $reportedItems->transform(function ($item){
                $item->get_related_item = (object) [
                    'id' => $item->id,
                    'stock_id_code' => $item->stock_id_code,
                    'title' => $item->title,
                ];
                unset($item->id, $item->stock_id_code, $item->title);
                return $item;

            });
            
            return response()->json(['status' => true, 'data' => $reportedItems], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }
       
    }

    public function reportedMissingItemsReport(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = WaLocationAndStore::all();
        $start = $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->todate ? Carbon::parse($request->todate)->endOfDay() : Carbon::now()->endOfDay();
        // $missingItems = ReportedMissingItem::with(['getRelatedItem', 'getRelatedUser', 'getRelatedStore'])
        //     ->whereBetween('created_at', [$start, $end]);
      
        $missingItems  =  DB::table('reported_missing_items')
            ->select(
                'reported_missing_items.created_at',
                'users.name',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.selling_price',
                // 'reported_missing_items.quantity',
                DB::raw('SUM(reported_missing_items.quantity) as quantity'),
                'reported_missing_items.as_at_quantity',
                DB::raw("(SELECT (wa_grns.created_at) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as last_purchase_date"),
                DB::raw("(SELECT (wa_internal_requisition_items.created_at)
                    FROM wa_internal_requisition_items
                    WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                    ORDER BY wa_internal_requisition_items.created_at DESC
                    LIMIT 1
                ) AS last_sale_date"),
                DB::raw("(SELECT (wa_suppliers.name) 
                    FROM wa_grns
                    LEFT JOIN wa_purchase_order_items ON wa_grns.wa_purchase_order_item_id = wa_purchase_order_items.id
                    LEFT JOIN wa_suppliers ON wa_grns.wa_supplier_id = wa_suppliers.id
                    WHERE wa_purchase_order_items.wa_inventory_item_id =  wa_inventory_items.id
                    ORDER BY wa_grns.created_at DESC
                    LIMIT 1
                ) as supplier"),
                DB::RAW("(SELECT GROUP_CONCAT(users.name SEPARATOR ', ')
                    FROM wa_inventory_item_suppliers
                    LEFT JOIN wa_user_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_user_suppliers.wa_supplier_id
                    LEFT JOIN users ON  wa_user_suppliers.user_id = users.id
                    WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                ) AS procurement_users"),

            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'reported_missing_items.item_id')
            ->leftJoin('users', 'users.id', 'reported_missing_items.reported_by')
            ->whereBetween('reported_missing_items.created_at', [$start, $end])
            ->groupBy('reported_missing_items.item_id', 'reported_missing_items.reported_by', DB::raw('DATE(reported_missing_items.created_at)'));
            
        if($request->branch){
            $missingItems = $missingItems->where('reported_missing_items.wa_location_and_store_id', $request->branch);
        }
        $missingItems = $missingItems->get();
        if($request->intent && $request->intent == 'Excel'){
            $data = [];
            foreach ($missingItems as $item){
                $payload = [
                    'created_at' => $item->created_at,
                    'reported_by' => $item->name,
                    'stock_id_code' => $item->stock_id_code,
                    'item_name' => $item->title,
                    'last_purchase_date' => $item->last_purchase_date,
                    'last_sale_date' => $item->last_sale_date,
                    'supplier' => $item->supplier,
                    'procurement_users' => $item->procurement_users,
                    'qoh_as_at' => $item->as_at_quantity,
                   'selling_price' => $item->selling_price,
                   'order_quantity' => $item->quantity,
                ];
                $data[] = $payload;
            }
            return ExcelDownloadService::download('reported_missing_items'.$start.'_'.$end, collect($data), ['DATE', 'REPORTED BY', 'STOCK ID CODE', 'ITEM','LAST PURCHASE DATE', 'LAST SALE DATE','SUPPLIER','PROCUREMENT USERS', 'QOH AS AT', 'SELLING PRICE', 'ORDER QUANTITY']);
        }
        if (isset($permission[$pmodule . '___reported-missing-items']) || $permission == 'superadmin') {
            $breadcum = [$title => route('missing-items-sales.index'), 'Listing' => ''];
            return view('admin.missing_items_sales.reported_missing_items', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'missingItems', 'branches'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function reportMissingItemsWeb(Request $request)
    {
        try {
            $data = $request->validate([
                'item_name.*' => 'required|exists:wa_inventory_items,id',
                'quantity.*' => 'required|integer|min:1',
            ]);
            $user = Auth::user();
    
            foreach ($data['item_name'] as $index => $itemId) {
                $item = WaInventoryItem::find($itemId);
                $qoh_as_at = WaStockMove::where('stock_id_code', $item->stock_id_code)
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity');
                $reportedItem = new ReportedMissingItem();
                $reportedItem->item_id = $itemId;
                $reportedItem->quantity = $data['quantity'][$index];
                $reportedItem->reported_by = $user->id;
                $reportedItem->as_at_quantity = $qoh_as_at;
                $reportedItem->wa_location_and_store_id = $user->wa_location_and_store_id;
                $reportedItem->save();

            }
    
            return response()->json(['success' => true, 'message' => 'Missing Items Submitted Successfully!']);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getMessage()]);

        }
      
    }
}
