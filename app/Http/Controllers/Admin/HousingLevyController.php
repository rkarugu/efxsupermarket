<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\HousingLevy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HousingLevyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(HousingLevy::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'rate' => 'required|numeric'
        ]);

        try {
            HousingLevy::create($data);

            return response()->json(['message' => 'Housing levy created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
    public function update(Request $request, HousingLevy $housingLevy)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'rate' => 'required|numeric'
        ]);

        try {
            $housingLevy->update($data);

            return response()->json(['message' => 'Housing levy created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
