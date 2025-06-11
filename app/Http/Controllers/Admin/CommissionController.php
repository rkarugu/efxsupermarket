<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommissionController extends Controller
{
    // API
    public function commissionList()
    {
        return response()->json(Commission::orderBy('commission')->get());
    }

    public function commissionCreate(Request $request)
    {
        $data = $request->validate([
            'commission' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $commission = Commission::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Commission added successfully',
            'data' => $commission
        ], 201);
    }

    public function commissionEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_commission,id',
            'commission' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $commission = Commission::find($request->id);

            array_shift($data);
            $commission->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Commission updated successfully',
            'data' => $commission
        ]);
    }

    public function commissionDelete($id)
    {
        try {
            $commission = Commission::find($id);

            $commission->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Commission deleted successfully',
        ]);
    }
}
