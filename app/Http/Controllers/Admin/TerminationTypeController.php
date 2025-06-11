<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\TerminationType;
use App\Http\Controllers\Controller;

class TerminationTypeController extends Controller
{
    public function terminationTypeList()
    {
        return response()->json(TerminationType::orderBy('name')->get());
    }

    public function terminationTypeCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $terminationType = TerminationType::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type added successfully',
            'data' => $terminationType
        ], 201);
    }

    public function terminationTypeEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:termination_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $terminationType = TerminationType::find($request->id);

            $terminationType->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type updated successfully',
            'data' => $terminationType
        ]);
    }

    public function terminationTypeDelete($id)
    {
        request()->validate([
            'id' => 'exists:termination_types,id'
        ]);
        
        $terminationType = TerminationType::find($id);
        try {
            $terminationType->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type deleted successfully',
        ]);
    }
}
