<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeeEducationHistory;
use App\Http\Requests\Admin\EmployeeEducationHistoryRequest;

class EmployeeEducationHistoryController extends Controller
{
    public function employeeEducationHistoryList(Employee $employee)
    {
        return response()->json($employee->educationHistories);
    }

    public function employeeEducationHistoryCreate(EmployeeEducationHistoryRequest $request)
    {
        $data = $request->validated();

        try {
            EmployeeEducationHistory::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee education history created successfully"
        ]);
    }

    public function employeeEducationHistoryEdit(EmployeeEducationHistoryRequest $request, $id)
    {
        $data = $request->validated();

        $employeeEducationHistory = EmployeeEducationHistory::find($id);

        try {
            $employeeEducationHistory->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee education history updated successfully"
        ]);
    }

    public function employeeEducationHistoryDelete($id) {
        $employeeEducationHistory = EmployeeEducationHistory::find($id);

        try {
            $employeeEducationHistory->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee education history deleted successfully"
        ]);
    }
}
