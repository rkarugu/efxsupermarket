<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WaPettyCashRequestType;

class PettyCashRequestTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            WaPettyCashRequestType::with('users', 'chartOfAccount')
                ->withCount('users')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array'
        ]);

        try {
            $pettyCashRequestType = WaPettyCashRequestType::create([
                'name' => $request->name,
            ]);

            if (count($request->users)) {
                $pettyCashRequestType->users()->attach($request->users); 
            }

            return response()->json([
                'data' => $pettyCashRequestType,
                'message' => 'Petty cash request type created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'wa_charts_of_account_id' => 'nullable|integer|exists:wa_charts_of_accounts,id'
        ]);

        $pettyCashRequestType = WaPettyCashRequestType::find($id);

        if (!$pettyCashRequestType) {
            return response()->json([
                'message' => 'Petty cash request type not found'
            ], 404);
        }

        try {
            $pettyCashRequestType->update([
                'name' => $request->name,
                'wa_charts_of_account_id' => $request->wa_charts_of_account_id ?: $pettyCashRequestType->wa_charts_of_account_id,
            ]);

            if (count($request->users)) {
                $pettyCashRequestType->users()->sync($request->users); 
            }

            return response()->json([
                'data' => $pettyCashRequestType,
                'message' => 'Petty cash request type updated successfully'
            ]);
        } catch (Exception $e) {
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
        $pettyCashRequestType = WaPettyCashRequestType::find($id);

        if (!$pettyCashRequestType) {
            return response()->json([
                'message' => 'Petty cash request type not found'
            ], 404);
        }

        try {
            $pettyCashRequestType->delete();

            return response()->json([
                'message' => 'Petty cash request type deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function userPettyCashRequestTypes()
    {
        $user = request()->user();

        $types = $this->getUserPettyCashRequestTypes($user);

        return response()->json($types);
    }

    public function getUserPettyCashRequestTypes(User $user)
    {
        return WaPettyCashRequestType::query()
            ->unless($user->role_id == '1', function ($query) use ($user) {
                $query->whereHas('users', fn ($query) => $query->where('id', $user->id));
            })
            ->get();
    }

    // Mobile APIs

    public function pettyCashTypes()
    {
        $possibleTypes = [
            'parking-fees'
        ];
        
        $pettyCashTypes = WaPettyCashRequestType::whereIn('slug', $possibleTypes)
            ->select('name', 'slug')
            ->get();

        return response()->json($pettyCashTypes);
    }
}
