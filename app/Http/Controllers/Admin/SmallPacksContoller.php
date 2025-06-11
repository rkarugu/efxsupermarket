<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockBreakDispatch;

use App\Models\SaleCenterSmallPackDispatch;
use App\Models\SaleCenterSmallPackDispatchItems;
use App\Models\SaleCenterSmallPackDispatchStatus;
use App\Models\SaleCenterSmallPackItems;
use Yajra\DataTables\Facades\DataTables;

class SmallPacksContoller extends Controller
{
    protected $model;
    protected $title;

    public function __construct() {
        $this->model = 'small-packs';
        $this->title = 'Small Packs';
    }

    /**
     * Display a listing of the resource.
     */
    public function store_loading_sheets()
    {
        if (!can('store-loading-sheets', $this->model)) {
            return returnAccessDeniedPage();
        }
 
        $title = $this->title .' Store Loading Sheets';
        $model = $this->model.'-store-loading-sheets';
        $breadcum = ['Sales & Receivables' => '', $title  => ''];
        $dispatch = SaleCenterSmallPackDispatchStatus::with('dispatch','center')
        ->select(
            'sale_center_small_pack_dispatch_statuses.*',
            DB::raw('(select count(id) from sale_center_small_pack_dispatch_items where sale_center_small_pack_dispatch_statuses.bin_id = sale_center_small_pack_dispatch_items.bin_id AND sale_center_small_pack_dispatch_statuses.dispatch_id=dispatch_id ) as items_count'),
            DB::raw('(select name from users where users.wa_unit_of_measures_id = sale_center_small_pack_dispatch_statuses.bin_id AND role_id=152 LIMIT 1) as bin_manager'),
        )
        ->where('dispatched',0)
        ->where('bin_id',Auth::user()->wa_unit_of_measures_id)
        ->get()
        ->map(function($item){
            return [
                'dispatch_id' => $item->dispatch_id,
                'bin_id' => $item->bin_id,
                'date'=>date('Y-m-d', strtotime($item->created_at)),
                'document_no' => $item->dispatch?->document_no,
                'branch' => $item->dispatch->saleCenter?->branch?->name,
                'route' => $item->dispatch?->saleCenter?->route?->route_name,
                'center' => $item->center?->name,
                'items_count' => $item->items_count,
                'created_by' => $item->dispatch?->createdBy?->name
            ];
        });

        return view('admin.small_packs.store_loading_sheets', compact('title', 'model','breadcum','dispatch'));
    }


    public function loading_sheets($id)
    {
        if (!can('store-loading-sheets', $this->model)) {
            return returnAccessDeniedPage();
        }
 
        $info = DB::table('sale_center_small_pack_dispatches')
        ->select(
            'sale_center_small_pack_dispatches.created_at as date',
            'sale_center_small_pack_dispatches.document_no',
            'routes.route_name',
            )
        ->join('sale_center_small_packs','sale_center_small_packs.id','sale_center_small_pack_dispatches.sale_center_small_pack_id')
        ->join('restaurants','restaurants.id','sale_center_small_packs.restaurant_id')
        ->join('routes','routes.id','sale_center_small_packs.route_id')
        ->where('sale_center_small_pack_dispatches.id',$id)
        ->first();
        $title = $this->title .' Loading Sheets - '.$info->document_no.' '.$info->route_name.' '.date('Y-m-d',strtotime($info->date));
        $model = $this->model.'-store-loading-sheet';
        $breadcum = ['Sales & Receivables' => '', $title  => ''];

        $dispatch = SaleCenterSmallPackDispatchStatus::with('dispatch')
        ->select(
            'sale_center_small_pack_dispatch_statuses.*',
            DB::raw('(select count(id) from sale_center_small_pack_dispatch_items where sale_center_small_pack_dispatch_statuses.bin_id = sale_center_small_pack_dispatch_items.bin_id AND sale_center_small_pack_dispatch_statuses.dispatch_id=dispatch_id ) as items_count'),
            DB::raw('(select name from users where users.wa_unit_of_measures_id = sale_center_small_pack_dispatch_statuses.bin_id AND role_id=152 LIMIT 1) as bin_manager'),
        )
        ->where('dispatch_id',$id)
        ->where('dispatched',0)
        ->get();

        return view('admin.small_packs.loading_sheets', compact('title', 'model','breadcum','dispatch','id'));

    }

    public function view_loading_sheets($id,$bin)
    {
        if (!can('view-loading-sheets', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title .' Loading Sheets';
        $model = $this->model.'-view-loading-sheets';
        $breadcum = ['Sales & Receivables' => '', $title  => ''];

        $info = SaleCenterSmallPackDispatch::find($id);
        $dispatch = SaleCenterSmallPackDispatchItems::join('wa_inventory_items', 'wa_inventory_items.id', 'sale_center_small_pack_dispatch_items.wa_inventory_item_id')
            ->select(
                'wa_inventory_items.title',
                'wa_inventory_items.stock_id_code',
                DB::raw("(SUM(sale_center_small_pack_dispatch_items.total_quantity)) AS total_quantity")
                )
            ->where('sale_center_small_pack_dispatch_items.dispatch_id',$id)
            ->where('sale_center_small_pack_dispatch_items.bin_id',$bin)
            ->groupBy('sale_center_small_pack_dispatch_items.wa_inventory_item_id')
            ->get();

        $binId=$bin;
        $dispatchId=$id;
        
        return view('admin.small_packs.view_loading_sheets', compact('title', 'model','breadcum','dispatch','binId','dispatchId','info'));
    }

    public function process_dispatch(Request $request)
    {
        if (!can('process-dispatch', $this->model)) {
            return returnAccessDeniedPage();
        }
        
        $user = User::find(Auth::user()->id);

        $uom = $user->uom;

        if ($uom?->is_display) {
            $autoBreaks = DB::table('route_auto_breaks')
                ->where('route_auto_breaks.status', 'pending')
                ->whereDate('route_auto_breaks.created_at', '<', Carbon::today());
            
            if ($user->role_id == 152) {
                $autoBreaks = $autoBreaks->where('route_auto_breaks.child_bin_id', $user->wa_unit_of_measures_id);
            }
    
            $autoBreaks = $autoBreaks->join('wa_inventory_items as child', 'route_auto_breaks.child_item_id', '=', 'child.id')
                ->join('wa_inventory_items as mother', 'route_auto_breaks.mother_item_id', '=', 'mother.id')
                ->join('wa_unit_of_measures as child_bin', 'route_auto_breaks.child_bin_id', '=', 'child_bin.id')
                ->join('wa_unit_of_measures as mother_bin', 'route_auto_breaks.mother_bin_id', '=', 'mother_bin.id')
                ->groupBy('route_auto_breaks.child_item_id')
                ->orderBy('route_auto_breaks.child_bin_id')
                ->count();
    
            if ($autoBreaks) {
                return response()->json([
                    'message' => 'Cannot process when there are pending auto breaks'
                ], 422);
            }

            $unreceivedDispatches = StockBreakDispatch::where('stock_break_dispatches.dispatched', true)
                ->where('received', false)
                ->whereDate('stock_break_dispatches.created_at', '<', Carbon::today());

            if ($user->role_id == 152) {
                $unreceivedDispatches = $unreceivedDispatches->where(function ($query) use ($user) {
                    $query->where('stock_break_dispatches.child_bin_id', $user->wa_unit_of_measures_id)->orWhere('stock_break_dispatches.dispatched_by', $user->id);
                });
            }

            $unreceivedDispatches = $unreceivedDispatches->join('wa_unit_of_measures as child_bin', 'stock_break_dispatches.child_bin_id', '=', 'child_bin.id')
                ->join('wa_unit_of_measures as mother_bin', 'stock_break_dispatches.mother_bin_id', '=', 'mother_bin.id')
                ->join('users as initiator', 'stock_break_dispatches.initiated_by', '=', 'initiator.id')
                ->orderBy('stock_break_dispatches.created_at', 'DESC')
                ->count();

            if ($unreceivedDispatches) {
                return response()->json([
                    'message' => 'Cannot process when there are unreceived dispatches'
                ], 422);
            }
        }

        $check = DB::transaction(function () use ($request){
            SaleCenterSmallPackDispatchStatus::where('dispatch_id', $request->dispatch_id)
            ->where('bin_id', $request->bin_id)
            ->update([
                'dispatched' => true,
                'dispatch_time' => Carbon::now(),
                'dispatcher_id' => Auth::user()->id
            ]);

            SaleCenterSmallPackDispatchItems::where('dispatch_id', $request->dispatch_id)
            ->where('bin_id', $request->bin_id)
            ->update(['dispatched_quantity' =>  DB::raw('total_quantity')]);

            // foreach ($request->qty as $key => $item) {
            //     $dispatchItem = SaleCenterSmallPackDispatchItems::find($key);
            //     $dispatchItem->update(['dispatched_quantity' => $item]);
            // }
            return true;
        });
        
        try {
            if($check){    
                return response()->json([
                    'result'=>1,
                    'message'=>'Process Dispatch Success',
                    'id'=>$check], 200);         
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function dispatched()
    {
        if (!can('dispatched-loading-sheets', $this->model)) {
            return returnAccessDeniedPage();
        }
 
        $title = $this->title .' Dispatched Loading Sheets';
        $model = $this->model.'-dispatched-loading-sheets';
        $breadcum = ['Sales & Receivables' => '', $title  => ''];

        $query = SaleCenterSmallPackDispatchStatus::with('dispatch','dispatch.saleCenter','dispatch.saleCenter.route','uom','dispatchedBy')
        ->where('dispatched',1)
        ->where('bin_id',Auth::user()->wa_unit_of_measures_id);

        if(request()->filled('bin')){
            $query->where('bin_id',request()->bin);
        }

        if(request()->filled('date')){
            $query->whereBetween('created_at', [request()->date . ' 00:00:00', request()->date . ' 23:59:59']);
        }

        if (request()->wantsJson()) {
            return DataTables::of($query)
                    ->addColumn('dispatch_date',function($query){
                        return date('Y-m-d', strtotime($query->dispatch_time));
                    })
                    ->addColumn('dispatch_time',function($query){
                        return date('H:i', strtotime($query->dispatch_time));
                    })
                    ->addColumn('item_count',function($query){
                        $counter = DB::table('sale_center_small_pack_dispatch_items as t1')
                        ->join('sale_center_small_pack_dispatch_statuses as t2', function($join) {
                            $join->on('t1.dispatch_id', '=', 't2.dispatch_id')
                                 ->on('t1.bin_id', '=', 't2.bin_id');
                        })
                        ->where('t2.bin_id',$query->bin_id)
                        ->where('t2.dispatch_id',$query->dispatch_id)
                        ->sum('t1.dispatched_quantity');
                        return number_format($counter);
                    })
                    ->addColumn('route_name',function($query){
                        return $query->dispatch->saleCenter->route->route_name;
                    })

                    ->toJson();
        }

        return view('admin.small_packs.dispatched_sheets', compact('title', 'model','breadcum'));
    }

    public function dispatched_view($id)
    {
        if (!can('dispatched-sheets-view', $this->model)) {
            return returnAccessDeniedPage();
        }
       
        $title = $this->title .' Dispatched Sheet';
        $model = $this->model.'-dispatched-loading-sheet';
        $breadcum = ['Sales & Receivables' => '', $title  => ''];

        $dispatch = DB::table('sale_center_small_pack_dispatch_items as t1')
        ->join('sale_center_small_pack_dispatch_statuses as t2', function($join) {
            $join->on('t1.dispatch_id', '=', 't2.dispatch_id')
                 ->on('t1.bin_id', '=', 't2.bin_id');
        })
        ->where('t2.id',$id)
        ->join('wa_inventory_items','wa_inventory_items.id','t1.wa_inventory_item_id')
        ->select('wa_inventory_items.stock_id_code','wa_inventory_items.title','t1.total_quantity','t1.dispatched_quantity')
        ->get();

        return view('admin.small_packs.dispatched_sheet_view', compact('title', 'model','breadcum','dispatch','id'));
    }
}
