<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use App\Models\TradeAgreement;
use App\Models\TradeAgreementDiscount;
use App\Models\WaSupplierDistributor;
use App\Model\WaStockMove;
use DB;

class SupplierDiscountDemandController extends Controller
{
    protected $model;
    protected $pmodule;
    protected $title;

    public function __construct()
    {
        $this->model = 'suppliers-discounts';
        $this->pmodule = 'suppliers-discounts';
        $this->title = 'Suppliers Discounts';
    }

    public function monthly_demand_index(){
        $breadcum = ['Supplier Utilities' => "", $this->title => ""];
        $suppliers = WaSupplier::get();
        return view('admin.supplier_utility.supllier_discounts.monthly_demand_index')->with([
            'model'=>$this->model,
            'pmodule'=>$this->pmodule,
            'title'=>$this->title,
            'suppliers'=>$suppliers,
            'breadcum'=>$breadcum
        ]);
    }

    public function monthly_demand_generate(Request $request){
        $breadcum = ['Supplier Utilities' => "", $this->title => ""];
        $suppliers = WaSupplier::get();
        $make_collection = [];
        if($request->supplier && $request->month && $request->year){
            $trade = TradeAgreement::where('wa_supplier_id',$request->supplier)->where('status','Approved')->firstOrFail();
            $discounts = TradeAgreementDiscount::where('trade_agreements_id',$trade->id)->where('discount_type','End month Discount')->first();
            $inventory_ids = [];
            $options = json_decode($discounts->other_options, true);
            if($options){
                foreach ($options as $key => $option) {
                    $inventory_ids[] = $key;
                }
            }
            $parent = WaSupplierDistributor::where('distributors',$request->supplier)->first()?->supplier_id;

            $stockMovesData = WaStockMove::
                select([
                    'wa_inventory_item_id',
                    DB::RAW('SUM(qauntity) as tq'),
                    DB::RAW('SUM(price * qauntity) as tc')
                ])->whereHas('inventoryItem', function($query) use ($request, $parent) {
                    $query->whereHas('suppliers', function($query) use ($request, $parent) {
                        $query->whereIn('wa_suppliers.id', [$request->supplier, $parent]);
                    })->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13]);
                })
                ->where(function($e){
                    $e->where('document_no', 'like', '%GRN%')->orWhere('document_no', 'like', '%RFS%');
                })
                ->whereIn('wa_inventory_item_id',$inventory_ids)
                ->where('qauntity', '>', 0)
                ->whereYear('created_at', $request->year)
                ->whereMonth('created_at', $request->month)
                ->groupBy('wa_inventory_item_id')
                ->get();
            foreach ($stockMovesData as $key => $data) {
                $discount = $options[$data->wa_inventory_item_id];
                if($discount){
                    $makediscount = 0;
                    if($discounts->discount_value_type == 'Value'){
                        $makediscount = (float)$discount['discount'] * $data->tq;
                    }else{
                        $makediscount = $data->tc * (float)$discount['discount'] / 100;
                    }
                    $make_collection[$data->wa_inventory_item_id] = [
                        'type'=>$discounts->discount_value_type,
                        'discount_value'=> (float)$discount['discount'],
                        'discount'=> $makediscount,
                        'total_quantity'=> $data->tq,
                        'total_cost'=> $data->tc,
                        'inventory'=>$data->inventoryItem
                    ];
                }
            }
        }
        return view('admin.supplier_utility.supllier_discounts.monthly_demand_generate')->with([
            'model'=>$this->model,
            'pmodule'=>$this->pmodule,
            'title'=>$this->title,
            'suppliers'=>$suppliers,
            'breadcum'=>$breadcum,
            'data'=>$make_collection
        ]);
    }
}
