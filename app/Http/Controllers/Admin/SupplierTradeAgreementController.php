<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SupplierTradeAgreement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierTradeAgreementController extends Controller
{
    public function addAgreement(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'agreement' => 'required',
                'supplier_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->jsonify(['message' => $validator->errors()], 422);
            }

            SupplierTradeAgreement::create([
                'supplier_id' => $request->supplier_id,
                'agreement' => $request->agreement,
            ]);

            return $this->jsonify([], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getAgreements(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->jsonify(['message' => $validator->errors()], 422);
            }

            $agreements = SupplierTradeAgreement::where('supplier_id', $request->supplier_id)->get();

            return $this->jsonify(['data' => $agreements], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
