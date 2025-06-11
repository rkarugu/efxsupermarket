<?php

namespace App\Http\Controllers\Admin;

use App\Models\Deduction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return response()->json(Deduction::with('brackets')->get());
        return response()->json(Deduction::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'amount_type' => 'required|string',
            // 'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            // 'has_brackets' => 'required|boolean',
            // 'is_statutory' => 'required|boolean',
            'is_recurring' => 'required|boolean',
            'is_reliefable' => 'required|boolean',
            'system_reserved' => 'required|boolean',
        ]);


        try {
            $series = getCodeWithNumberSeries('EARNINGS_AND_DEDUCTIONS');
            
            Deduction::create([
                'code' => $series,
                ...$data
            ]);

            updateUniqueNumberSeries('EARNINGS_AND_DEDUCTIONS', $series);

            return response()->json([
                'message' => 'Deduction created successfully'
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
    public function update(Request $request, Deduction $deduction)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'amount_type' => 'required|string',
            // 'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            // 'has_brackets' => 'required|boolean',
            // 'is_statutory' => 'required|boolean',
            'is_recurring' => 'required|boolean',
            'is_reliefable' => 'required|boolean',
            'system_reserved' => 'required|boolean',
        ]);

        try {
           $deduction->update($data);

            return response()->json([
                'message' => 'Deduction updated successfully'
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
