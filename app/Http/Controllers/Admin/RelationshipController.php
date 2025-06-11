<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Relationship;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $relationships = Relationship::withCount('employeeEmergencyContacts', 'employeeBeneficiaries')->get();
        
        return response()->json($relationships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'system_reserved' => 'nullable|boolean'
        ]);

        try {
            Relationship::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Relationship created successfully'
        ]);
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
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'system_reserved' => 'nullable|boolean'
        ]);

        try {
            $relationship = Relationship::find($id);

            $relationship->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Relationship updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $relationship = Relationship::find($id);
            
            $relationship->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Relationship deleted successfully'
        ]);
    }
}
