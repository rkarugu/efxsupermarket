<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\DisciplineCategory;
use App\Http\Controllers\Controller;

class DisciplineCategoryController extends Controller
{
    public function disciplineCategoryList()
    {
        return response()->json(DisciplineCategory::orderBy('name')->get());
    }

    public function disciplineCategoryCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $disciplineCategory = DisciplineCategory::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Category added successfully',
            'data' => $disciplineCategory
        ], 201);
    }

    public function disciplineCategoryEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:discipline_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $disciplineCategory = DisciplineCategory::find($request->id);

            $disciplineCategory->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Category updated successfully',
            'data' => $disciplineCategory
        ]);
    }

    public function disciplineCategoryDelete($id)
    {
        request()->validate([
            'id' => 'exists:discipline_categories,id'
        ]);
        
        $disciplineCategory = DisciplineCategory::find($id);
        try {
            $disciplineCategory->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Discipline Category deleted successfully',
        ]);
    }
}
