<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\BankBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankBranchController extends Controller
{
    // API
    public function bankBranchList()
    {
        $branches = BankBranch::with('bank')
            ->withCount('bankAccounts')
            ->orderBy('name')
            ->get();
        
        return response()->json($branches);
    }

    public function bankBranchCreate(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255',
        ]);

        try {
            $bankBranch = BankBranch::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch added successfully',
            'data' => $bankBranch
        ], 201);
    }

    public function bankBranchEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:bank_branches,id',
            'bank_id' => 'required|exists:wa_bank,id',
            'name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255',
        ]);

        try {
            $bankBranch = BankBranch::find($request->id);

            array_shift($data);
            $bankBranch->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch updated successfully',
            'data' => $bankBranch
        ]);
    }

    public function bankBranchDelete($id)
    {
        request()->validate([
            'id' => 'exists:bank_branches,id'
        ]);
        
        $bankBranch = BankBranch::find($id);
        try {
            $bankBranch->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch deleted successfully',
        ]);
    }
}
