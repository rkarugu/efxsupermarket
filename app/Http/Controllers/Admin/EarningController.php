<?php

namespace App\Http\Controllers\Admin;

use App\Models\Earning;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EarningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Earning::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'type' => 'required|string',
            'amount_type' => 'required|string',
            // 'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            'ratio' => 'nullable|numeric',
            'is_taxable' => 'required|boolean',
            // 'tax_rate' => 'required_if:is_taxable,true',
            'is_recurring' => 'required|boolean',
            'is_reliefable' => 'required|boolean',
            'system_reserved' => 'required|boolean',
        ]);


        try {
            $series = getCodeWithNumberSeries('EARNINGS_AND_DEDUCTIONS');
            
            Earning::create([
                'code' => $series,
                ...$data
            ]);

            updateUniqueNumberSeries('EARNINGS_AND_DEDUCTIONS', $series);

            return response()->json([
                'message' => 'Earning created successfully'
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
    public function update(Request $request, Earning $earning)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'type' => 'required|string',
            'amount_type' => 'required|string',
            // 'amount' => 'nullable|int',
            'rate' => 'nullable|numeric',
            'ratio' => 'nullable|numeric',
            'is_taxable' => 'required|boolean',
            // 'tax_rate' => 'required_if:is_taxable,true',
            'is_recurring' => 'required|boolean',
            'is_reliefable' => 'required|boolean',
            'system_reserved' => 'required|boolean',
        ]);

        try {
           $earning->update($data);

            return response()->json([
                'message' => 'Earning updated successfully'
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
