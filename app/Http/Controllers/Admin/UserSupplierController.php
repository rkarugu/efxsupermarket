<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\WaUserSupplier;

class UserSupplierController extends Controller
{
    public function getUserSuppliers(Request $request): JsonResponse
    {
        try {
            $userSuppliers = DB::table('wa_user_suppliers')->where('wa_user_suppliers.user_id', $request->user_id)
                ->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                ->select('wa_suppliers.id as supplier_id', 'wa_suppliers.name as supplier', 'wa_suppliers.supplier_code as supplier_code')
                ->get()->map(function ($record) {
                    $record->listed_items = DB::table('wa_inventory_item_supplier_data')->where('wa_supplier_id', $record->supplier_id)->count();
                    return $record;
                });

            return $this->jsonify(['data' => $userSuppliers], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function addUserSupplier(Request $request): JsonResponse
    {
        try {
            $userSupplier = new WaUserSupplier();
            $userSupplier->wa_supplier_id = $request->supplier_id;
            $userSupplier->user_id = $request->user_id;
            $userSupplier->save();

            return $this->jsonify([], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function deallocateUserSupplier(Request $request): JsonResponse
    {
        try {
            WaUserSupplier::where('user_id', $request->user_id)->where('wa_supplier_id', $request->supplier_id)->first()->delete();

            return $this->jsonify([], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
