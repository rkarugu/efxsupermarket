<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\DisciplineAction;
use App\Http\Controllers\Controller;

class DisciplineActionController extends Controller
{
    public function disciplineActionList()
    {
        return response()->json(DisciplineAction::orderBy('name')->get());
    }

    public function disciplineActionCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $disciplineAction = DisciplineAction::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Action added successfully',
            'data' => $disciplineAction
        ], 201);
    }

    public function disciplineActionEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:discipline_actions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $disciplineAction = DisciplineAction::find($request->id);

            $disciplineAction->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Action updated successfully',
            'data' => $disciplineAction
        ]);
    }

    public function disciplineActionDelete($id)
    {
        request()->validate([
            'id' => 'exists:discipline_actions,id'
        ]);

        $disciplineAction = DisciplineAction::find($id);
        try {
            $disciplineAction->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Action deleted successfully',
        ]);
    }
}
