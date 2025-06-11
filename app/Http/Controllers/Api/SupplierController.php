<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Setting;
use App\Model\WaSupplier;
use App\Services\Inventory\TurnoverPurchases;
use App\Services\Inventory\TurnoverSales;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function getPayableBalances(Request $request)
    {
        try {

            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => [
                    'balance' => $supplier->balance(),
                    'grn_amount' => $supplier->grnsValue(),
                    'stock_value' => $supplier->stockValue(),
                    'processing_amount' => $supplier->getUnpaidVouchers(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function turnoverPurchases(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $data = app(TurnoverPurchases::class)->purchases($supplier->id);

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function turnoverSales(Request $request)
    {
        try {
            $supplier = WaSupplier::where('supplier_code', $request->supplier_code)->firstOrFail();

            $data = app(TurnoverSales::class)->sales($supplier->id);

            return response()->json([
                'message' => 'Data Retrived Successfully',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function portal_billing_note(){
        return response()->json([
            'result'=>1,'message'=>'Billing Note Succesfully','data'=> Setting::where('name','SUPPLIER_PORTAL_BILLING_DESCRIPTION')->first()
        ],200);
    }
}
