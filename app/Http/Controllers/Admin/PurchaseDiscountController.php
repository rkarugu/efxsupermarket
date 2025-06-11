<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use \Illuminate\Http\Request;
use App\Models\TradeAgreement;
use App\Models\TradeAgreementDiscount;
use App\Services\ApiService;
use Exception;

class PurchaseDiscountController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'purchase-discount';
        $this->title = 'Purchase Discount';
        $this->pmodule = 'purchase-discount';
    }

    public function get_supplier_discounts(Request $request){
        try {
            $supplier = WaSupplier::findOrFail($request->supplier_id);
            $trade = $supplier->locked_trade;
            
            if(is_null($trade)){
                throw new Exception('Supplier trade agreement is not locked');
            }

            $discounts = TradeAgreementDiscount::where('trade_agreements_id',$trade->id)->get();
            return response()->json([
                'result'=>1,
                'message'=>"Ok!",
                'data'=>$discounts
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,'message'=>$th->getMessage()
            ]);
        }
    }
}
