<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeeProfessionalHistory;
use App\Http\Requests\Admin\EmployeeProfessionalHistoryRequest;

class EmployeeProfessionalHistoryController extends Controller
{
    public function employeeProfessionalHistoryList(Employee $employee)
    {
        return response()->json($employee->professionalHistories);
    }

    public function employeeProfessionalHistoryCreate(EmployeeProfessionalHistoryRequest $request)
    {
        $data = $request->validated();

        try {
            EmployeeProfessionalHistory::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee professional history created successfully"
        ]);
    }

    public function employeeProfessionalHistoryEdit(EmployeeProfessionalHistoryRequest $request, $id)
    {
        $data = $request->validated();

        $employeeProfessionalHistory = EmployeeProfessionalHistory::find($id);

        try {
            $employeeProfessionalHistory->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee professional history updated successfully"
        ]);
    }

    public function employeeProfessionalHistoryDelete($id) {
        $employeeProfessionalHistory = EmployeeProfessionalHistory::find($id);

        try {
            $employeeProfessionalHistory->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee professional history deleted successfully"
        ]);
    }
}
