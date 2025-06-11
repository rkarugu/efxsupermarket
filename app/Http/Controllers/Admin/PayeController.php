<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Paye;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Paye::orderBy('from')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|integer',
            'to' => 'required|integer',
            'rate' => 'required|numeric',
        ]);

        try {
            Paye::create($data);

            return response()->json(['message' => 'Paye created successfully'], 201);
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
    public function update(Request $request, Paye $paye)
    {
        $data = $request->validate([
            'from' => 'required|integer',
            'to' => 'required|integer',
            'rate' => 'required|numeric',
        ]);

        try {
            $paye->update($data);

            return response()->json(['message' => 'Paye created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paye $paye)
    {
        try {
            $paye->delete();

            return response()->json(['message' => 'Paye deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
