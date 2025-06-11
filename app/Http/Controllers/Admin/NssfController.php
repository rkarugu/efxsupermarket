<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Nssf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NssfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Nssf::orderBy('from')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|integer',
            'to' => 'required|integer',
            'rate' => 'required|integer',
        ]);

        try {
            Nssf::create($data);

            return response()->json(['message' => 'Nssf created successfully'], 201);
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
    public function update(Request $request, Nssf $nssf)
    {
        $data = $request->validate([
            'from' => 'required|integer',
            'to' => 'required|integer',
            'rate' => 'required|integer',
        ]);

        try {
            $nssf->update($data);

            return response()->json(['message' => 'Nssf created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nssf $nssf)
    {
        try {
            $nssf->delete();

            return response()->json(['message' => 'Nssf deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
