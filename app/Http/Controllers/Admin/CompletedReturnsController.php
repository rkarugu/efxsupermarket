<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\WaInventoryLocationTransferItemReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Route;
use App\Model\WaInventoryItem;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Auth;


class CompletedReturnsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'completed-returns';
        $this->title = 'Completed Returns';
        $this->pmodule = 'completed-returns';
        $this->basePath = 'completed-returns';
    }
    public function index(Request $request){
        
        if (!can('view', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $user = Auth::user();
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $branchIds = DB::table('user_branches')
        ->where('user_id', $user->id)
        ->pluck('restaurant_id')
        ->toArray();
        $branches = Restaurant::whereIn('id', $branchIds)->get();
        if($user->role_id == 1){
            $branches = Restaurant::all();
        }
        $routes = Route::where('restaurant_id', $user->restaurant_id)->get();

        if($request->branch){
            $routes = Route::where('restaurant_id', $request->branch)->get();
        }
        
        $returns = DB::table('wa_inventory_location_transfer_item_returns')
            ->orderBy('wa_inventory_location_transfer_item_returns.return_date', 'DESC')
            ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start, $end])
            ->select(
                'wa_inventory_location_transfer_item_returns.return_number',
                'wa_inventory_location_transfer_item_returns.return_status',
                'wa_inventory_location_transfer_item_returns.status',
                'wa_inventory_location_transfers.transfer_no as invoice_number',
                'wa_inventory_location_transfers.name as customer',
                'wa_inventory_location_transfers.route as route',
                'wa_inventory_location_transfer_item_returns.updated_at as return_date',
                DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns')
            )
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', function ($join) use ($request) {
                $query = $join->on('wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id');
                if ($request->route) {
                    $query = $query->where('wa_inventory_location_transfers.route_id', $request->route);
                }
            })
            ->leftJoin('routes', 'routes.id', '=', 'wa_inventory_location_transfers.route_id' )
            ->where('wa_inventory_location_transfer_item_returns.return_status', 1)
            ->where('wa_inventory_location_transfer_item_returns.status', 'received');
            if($request->branch){
                $returns = $returns->where('routes.restaurant_id', $request->branch);
            }
            $returns = $returns->groupBy('wa_inventory_location_transfer_item_returns.return_number')
            ->get();           
        $title = $this->title;
        $model = $this->model;
        return view('admin.sales_invoice.returns.completed_returns', compact('title', 'model', 'returns', 'routes','branches', 'user'));

    }
    public function completedReturnsDetails($return, $date)
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();
        $returnItems =  DB::table('wa_inventory_location_transfer_item_returns')
        ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start, $end])
        ->select(
            'wa_inventory_location_transfer_item_returns.return_number',
            'wa_inventory_location_transfer_item_returns.return_status',
            'wa_inventory_location_transfer_item_returns.status',
            'wa_inventory_location_transfer_item_returns.received_quantity',
            'wa_inventory_location_transfer_item_returns.return_quantity as returned_quantity',
            'wa_inventory_items.title',
            'wa_inventory_items.stock_id_code',
            'wa_inventory_location_transfer_items.selling_price',
            'wa_inventory_location_transfer_item_returns.updated_at as return_date',
            // DB::raw('sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns')
        )
        ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
        ->join('wa_inventory_items', 'wa_inventory_location_transfer_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
        ->where('wa_inventory_location_transfer_item_returns.return_status', 1)
        ->where('wa_inventory_location_transfer_item_returns.status', 'received')
        ->where('wa_inventory_location_transfer_item_returns.return_number', $return)
        ->get();           
        return response()->json($returnItems);
    }

    public function detailedCompletedReturns(Request $request)
    {
        if (!can('view', 'detailed-completed-returns')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $user = Auth::user();
        $inventoryItems = WaInventoryItem::all(); 
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $branchIds = DB::table('user_branches')
        ->where('user_id', $user->id)
        ->pluck('restaurant_id')
        ->toArray();
        $branches = Restaurant::whereIn('id', $branchIds)->get();
        if($user->role_id == 1){
            $branches = Restaurant::all();
        }
        $routes = Route::where('restaurant_id', $user->restaurant_id)->get();

        if($request->branch){
            $routes = Route::where('restaurant_id', $request->branch)->get();
        }
        
        $returns = DB::table('wa_inventory_location_transfer_item_returns')
            ->orderBy('wa_inventory_location_transfer_item_returns.return_date', 'DESC')
            ->whereBetween('wa_inventory_location_transfer_item_returns.updated_at', [$start, $end])
            ->select(
                'wa_inventory_location_transfer_item_returns.updated_at as return_date',
                'wa_inventory_location_transfer_item_returns.created_at as created_date',
                'wa_inventory_location_transfers.route as route',
                'wa_inventory_location_transfers.created_at as invoice_date',
                'wa_inventory_location_transfers.transfer_no as invoice_number',
                'wa_inventory_location_transfer_item_returns.return_number',
                'wa_inventory_items.title',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_location_transfer_items.quantity as invoice_quantity',
                'wa_inventory_location_transfer_item_returns.return_quantity as returned_quantity',
                'wa_inventory_location_transfer_item_returns.received_quantity',
                'wa_inventory_location_transfer_items.selling_price',
            )
            ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->join('wa_inventory_location_transfers', function ($join) use ($request) {
                $query = $join->on('wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id');
                if ($request->route) {
                    $query = $query->where('wa_inventory_location_transfers.route_id', $request->route);
                }
            })
            ->join('wa_inventory_items', 'wa_inventory_location_transfer_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('routes', 'routes.id', '=', 'wa_inventory_location_transfers.route_id' )
            ->where('wa_inventory_location_transfer_item_returns.return_status', 1)
            ->where('wa_inventory_location_transfer_item_returns.status', 'received');
            if($request->branch){
                $returns = $returns->where('routes.restaurant_id', $request->branch);
            }
            if($request->item){
                $returns = $returns->where('wa_inventory_items.id', $request->item);
            }
            $returns = $returns->get(); 

            if($request->type && $request->type == 'Download'){
                $data = [];
                foreach($returns as $return){
                    $payload = [
                        'rcvd date' => $return->return_date,
                        'route' => $return->route,
                        'invoice_date' => $return->invoice_date,
                        'invoices' => $return->invoice_number,
                        'created date' => $return->created_date,
                        'return' => $return->return_number,
                        'stock_id_code' => $return->stock_id_code,
                        'title' => $return->title,
                        'invoice_quantity' => $return->invoice_quantity,
                       'returned_quantity' => $return->returned_quantity,
                       'receives_quantity' => $return->received_quantity,
                       'amount' => manageAmountFormat($return->received_quantity * $return->selling_price)

                    ];
                    $data [] = $payload;
                }
                $headings = ['Processed Date', 'Route', 'Invoice Date',  'Invoice', 'Return Date','Return No.', 'Stock Id Code','Item', 'Invoice Qty','Qty Returned', 'Qty Received', 'Amount'];
                $title = 'Detailed_Completed_Returns'. $start.'-'.$end;
                return ExcelDownloadService::download($title, collect($data), $headings);

            }
        $title = $this->title;
        $model = 'detailed-completed-returns';
        return view('admin.sales_invoice.returns.detailed_completed_returns', compact('title', 'model', 'returns', 'routes','branches', 'user', 'inventoryItems'));


    }
}
