<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HrPaymentModeController extends Controller
{
    public function paymentModeList()
    {
        $paymentModes = PaymentMode::withCount('employees')->orderBy('name')->get();
        
        return response()->json($paymentModes);
    }

    public function paymentModeCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $paymentMode = PaymentMode::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Mode added successfully',
            'data' => $paymentMode
        ], 201);
    }

    public function paymentModeEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:payment_modes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $paymentMode = PaymentMode::find($request->id);

            array_shift($data);
            $paymentMode->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Mode updated successfully',
            'data' => $paymentMode
        ]);
    }

    public function paymentModeDelete($id)
    {
        try {
            $paymentMode = PaymentMode::find($id);

            $paymentMode->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Mode deleted successfully',
        ]);
    }
}
