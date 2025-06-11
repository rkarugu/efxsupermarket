<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeeEmergencyContact;
use App\Http\Requests\Admin\EmployeeEmergencyContactRequest;

class EmployeeEmergencyContactController extends Controller
{
    public function employeeEmergencyContactList(Employee $employee)
    {
        return response()->json($employee->emergencyContacts->load("relationship"));
    }

    public function employeeEmergencyContactCreate(EmployeeEmergencyContactRequest $request)
    {
        $data = $request->validated();

        try {
            EmployeeEmergencyContact::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee emergency contact created successfully"
        ]);
    }

    public function employeeEmergencyContactEdit(EmployeeEmergencyContactRequest $request, $id)
    {
        $data = $request->validated();

        $employeeEmergencyContact = EmployeeEmergencyContact::find($id);

        try {
            $employeeEmergencyContact->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee emergency contact updated successfully"
        ]);
    }

    public function employeeEmergencyContactDelete($id) {
        $employeeEmergencyContact = EmployeeEmergencyContact::find($id);

        try {
            $employeeEmergencyContact->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee emergency contact deleted successfully"
        ]);
    }
}
