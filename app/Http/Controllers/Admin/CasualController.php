<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Casual;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CasualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');
        
        $casuals = Casual::with('gender', 'nationality', 'branch')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get();
        
        return response()->json($casuals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' =>'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'id_no' => 'required|string|max:255',
            'phone_no' => 'required|string|max:255|unique:casuals,phone_no',
            'email' => 'nullable|email|max:255',
            'gender_id' => 'required|int|exists:genders,id',
            'nationality_id' => 'required|int|exists:nationalities,id',
            'branch_id' => 'required|int|exists:restaurants,id',
        ]);

        try {
            Casual::create($data);

            return response()->json(['message' => 'Casual created successfully'], 201);
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
    public function update(Request $request, Casual $casual)
    {
        $data = $request->validate([
            'first_name' =>'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'id_no' => 'required|string|max:255',
            'phone_no' => 'required|string|max:255|unique:casuals,phone_no,'. $casual->id,
            'email' => 'nullable|email|max:255',
            'gender_id' => 'required|int|exists:genders,id',
            'nationality_id' => 'required|int|exists:nationalities,id',
            'branch_id' => 'required|int|exists:restaurants,id',
        ]);

        try {
            $casual->update($data);

            return response()->json([
                'message' => 'Casual updated successfully',
                'data' => $casual,
            ]);
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

    public function activate(Casual $casual)
    {
        try {
            $casual->update([
                'active' => true,
            ]);

            return response()->json(['message' => 'Casual activated successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function deactivate(Request $request, Casual $casual)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:255',
            'narration' => 'nullable|string|max:255',
        ]);

        try {
            $casual->update([
                'active' => false,
                ...$data
            ]);

            return response()->json(['message' => 'Casual deactivated successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
