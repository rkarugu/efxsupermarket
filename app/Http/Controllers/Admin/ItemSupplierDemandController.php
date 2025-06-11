<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\WaDemand;
use App\WaDemandItem;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;


class ItemSupplierDemandController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'item-demands';
        $this->base_route = 'item-demands';
        $this->resource_folder = 'admin.item_demands';
        $this->base_title = 'Demands';
        $this->permissions_module = 'item-demands';
    }

    public function index(Request $request): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $suppliers = DB::table('wa_suppliers')->select('id', 'name')->get();


        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $demands = DB::table('item_supplier_demands')->leftJoin('wa_inventory_items', 'item_supplier_demands.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('wa_suppliers', 'item_supplier_demands.wa_supplier_id', '=', 'wa_suppliers.id')
            ->select('item_supplier_demands.*', 'wa_inventory_items.title as item_name', 'wa_inventory_items.stock_id_code as item_code', 'wa_suppliers.name as supplier');

        if ($request->supplier) {
            $demands = $demands->where('wa_supplier_id', $request->supplier);
        }

        if ($request->item) {
            $demands = $demands->where('wa_inventory_item_id', $request->item);
        }

        $demands = $demands->orderBy('created_at', 'DESC')
            ->get()->map(function ($demand) {
                $demand->valuation_before = $demand->current_cost * $demand->demand_quantity;
                $demand->valuation_after = $demand->new_cost * $demand->demand_quantity;
                $demand->amount = $demand->valuation_before - $demand->valuation_after;

                return $demand;
            });
        //group by suppliers
        $groupedDemands = $demands->groupBy('wa_supplier_id')->map(function ($group) {
            $supplier = $group->first()->supplier;
        
            $totalDemandAmount = $group->sum('amount');
            $totalItemCount = $group->count();
            $totalItemQuantity = $group->sum('demand_quantity');
        
            return [
                'supplier_id' => $group->first()->wa_supplier_id,   
                'supplier_name' => $group->first()->supplier,
                'total_demand_amount' => $totalDemandAmount,
                'total_item_count' => $totalItemCount,
                'total_item_quantity' => $totalItemQuantity,
                'demands' => $group,
            ];
        });
       
   
     
        $myItems = DB::table('wa_inventory_items')->select('id', 'title', 'stock_id_code')->get();
        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'demands',
            'suppliers',
            'myItems',
            'groupedDemands'
        ));
    }
    public function demandItems(Request $request, $id): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $suppliers = DB::table('wa_suppliers')->select('id', 'name')->get();
        $supplierName = WaSupplier::find($id)->name;


        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $demands = DB::table('item_supplier_demands')->leftJoin('wa_inventory_items', 'item_supplier_demands.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('wa_suppliers', 'item_supplier_demands.wa_supplier_id', '=', 'wa_suppliers.id')
            ->select('item_supplier_demands.*', 'wa_inventory_items.title as item_name', 'wa_inventory_items.stock_id_code as item_code', 'wa_suppliers.name as supplier');

        // if ($request->supplier) {
        //     $demands = $demands->where('wa_supplier_id', $request->supplier);
        // }
        $demands = $demands->where('wa_supplier_id', $id);


        if ($request->item) {
            $demands = $demands->where('wa_inventory_item_id', $request->item);
        }

        $demands = $demands->orderBy('created_at', 'DESC')
            ->get()->map(function ($demand) {
                $demand->valuation_before = $demand->current_cost * $demand->demand_quantity;
                $demand->valuation_after = $demand->new_cost * $demand->demand_quantity;
                $demand->amount = $demand->valuation_before - $demand->valuation_after;

                return $demand;
            });

        $myItems = DB::table('wa_inventory_items')->select('id', 'title', 'stock_id_code')->get();
        return view("$this->resource_folder.demand_items", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'demands',
            'suppliers',
            'myItems',
            'supplierName',
        ));
    }
    public function newIndex(Request $request): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $suppliers = DB::table('wa_suppliers')->select('id', 'name')->get();


        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $demands = WaDemand::with(['getSupplier',  'getUser', 'getDemandItem'])
            ->where('merged', false)
            ->orderBy('created_at','desc');

        if ($request->supplier) {
            $demands = $demands->where('wa_supplier_id', $request->supplier);
        }
        if($request->from && $request->to){
            $from = \Carbon\Carbon::parse($request->from)->toDateString();
            $to = \Carbon\Carbon::parse($request->to)->toDateString();
            $demands = $demands->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        }
        if(!$request->supplier && !$request->from && !$request->to){
            $demands = $demands->take(25);
        }
        $demands  = $demands->get();

        $myItems = DB::table('wa_inventory_items')->select('id', 'title', 'stock_id_code')->get();
        return view("$this->resource_folder.new_index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'demands',
            'suppliers',
        ));
    }
    public function newDemandItems(Request $request, $id): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $suppliers = DB::table('wa_suppliers')->select('id', 'name')->get();
        $supplierName = WaSupplier::find($id)->name;


        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        $demand = WaDemand::find($id);

       $demandItems = WaDemandItem::with(['getInventoryItemDetail'])->where('wa_demand_id',$id);
        if ($request->item) {
            $demandItems = $demandItems->where('wa_inventory_item_id', $request->item);
        }

      $demandItems = $demandItems->get()->map(function ($demand) {
        $demand->valuation_before = $demand->current_cost * $demand->demand_quantity;
        $demand->valuation_after = $demand->new_cost * $demand->demand_quantity;
        $demand->amount = $demand->valuation_before - $demand->valuation_after;

        return $demand;
    });

        $myItems = DB::table('wa_inventory_items')->select('id', 'title', 'stock_id_code')->get();
        return view("$this->resource_folder.new_demand_items", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'myItems',
            'supplierName',
            'demand',
            'demandItems',
        ));
    }

    public function downloadDemand($id){
        $demand = WaDemand::find($id);
        $supplier = WaSupplier::find($demand->wa_supplier_id);
        $data =  WaDemandItem::with(['getInventoryItemDetail'])->where('wa_demand_id',$id)->get()->map(function ($demand) {
            $demand->valuation_before = $demand->current_cost * $demand->demand_quantity;
            $demand->valuation_after = $demand->new_cost * $demand->demand_quantity;
            $demand->amount = $demand->valuation_before - $demand->valuation_after;
            $demand->edited_demand_amount = $demand?->edited_demand_amount ?? 0;
            return $demand;
        });
        $pdf = PDF::loadView("$this->resource_folder.print", compact('data', 'supplier', 'demand'))->set_option("enable_php", true);
        return $pdf->stream('demand'.$demand->demand_no.'.pdf');


    }

    public function itemDemandConvert($id)
    {
        if (!can('convert', 'item-demands')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = $this->base_title;
        $base_route = $this->base_route;
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => route('demands.item-demands.new'), 'Convert' => ''];

        $page = "Convert";

        $demand = WaDemand::with([
            'demandItems.inventoryItem' => function($query) {
                $query->with('taxManager')
                    ->withSum('stockMoves', 'qauntity');
            }, 
            'supplier', 
            'user.userRestaurent', 
            'user.location_stores'
        ])
            ->find($id);

        if ($demand->processed) {
            Session::flash('warning', 'Demand already converted');
            return redirect()->back();
        }

        return view('admin.item_demands.edit', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'page'));            
    }

    public function itemDemandApprove($id)
    {
        if (!can('approve', 'item-demands')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = $this->base_title;
        $base_route = $this->base_route;
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => route('demands.item-demands.new'), 'Convert' => ''];

        $page = "Approve";

        $demand = WaDemand::with([
            'demandItems.inventoryItem' => function($query) {
                $query->with('taxManager')
                    ->withSum('stockMoves', 'qauntity');
            }, 
            'supplier', 
            'user.userRestaurent', 
            'user.location_stores'
        ])
            ->find($id);

        if ($demand->approved) {
            Session::flash('warning', 'Demand already approved');
            return redirect()->back();
        }

        return view('admin.item_demands.edit', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'page'));            
    }
}
