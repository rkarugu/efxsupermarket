<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\JobLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobLevelController extends Controller
{
    public function jobLevelList()
    {
        $jobLevels = JobLevel::with('jobGroup')->withCount('jobGrades', 'jobTitles')->orderBy('name')->get();
        
        return response()->json($jobLevels);
    }

    public function jobLevelCreate(Request $request)
    {
        $data = $request->validate([
            'job_group_id' => 'required|integer|exists:job_groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobLevel = JobLevel::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Level added successfully',
            'data' => $jobLevel
        ], 201);
    }

    public function jobLevelEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:job_levels,id',
            'job_group_id' => 'required|integer|exists:job_groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $jobLevel = JobLevel::find($request->id);

            $jobLevel->update([
                'job_group_id' => $request->job_group_id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Level updated successfully',
            'data' => $jobLevel
        ]);
    }

    public function jobLevelDelete($id)
    {
        request()->validate([
            'id' => 'exists:job_levels,id'
        ]);
        
        $jobLevel = JobLevel::find($id);
        
        try {
            $jobLevel->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Job Level deleted successfully',
        ]);
    }

}
