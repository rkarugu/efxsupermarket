<?php

namespace App\Http\Controllers\Admin;

use App\Models\Relief;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReliefController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Relief::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // 'earning_id' => 'required|int|exists:earnings,id',
            // 'deduction_id' => 'required|int|exists:deductions,id',
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'amount_type' => 'required|string',
            'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            'system_reserved' => 'required|boolean',
        ]);


        try {
            Relief::create($data);

            return response()->json([
                'message' => 'Relief created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Relief $relief)
    {
        $data = $request->validate([
            // 'earning_id' => 'required|int|exists:earnings,id',
            // 'deduction_id' => 'required|int|exists:deductions,id',
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'amount_type' => 'required|string',
            'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            'system_reserved' => 'required|boolean',
        ]);

        try {
           $relief->update($data);

            return response()->json([
                'message' => 'Relief updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
