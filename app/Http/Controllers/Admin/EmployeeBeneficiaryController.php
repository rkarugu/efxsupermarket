<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EmployeeBeneficiary;

class EmployeeBeneficiaryController extends Controller
{
    public function employeeBeneficiariesList(Employee $employee)
    {
        return response()->json($employee->beneficiaries);
    }

    public function employeeBeneficiariesUpdate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'beneficiaries' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $employee = Employee::find($request->employee_id);
    
            foreach($request->beneficiaries as $beneficiary) {
                $id = array_shift($beneficiary);

                $isMinor = $beneficiary['is_minor'];

                $data = [
                    'employee_id' => $request->employee_id,
                    'relationship_id' => $beneficiary['relationship_id'],
                    'custom_relationship' => $beneficiary['custom_relationship'],
                    'is_minor' => $beneficiary['is_minor'],
                    'full_name' => $beneficiary['full_name'],
                    'email' => $isMinor ? null : $beneficiary['email'],
                    'phone_no' => $isMinor ? null : $beneficiary['phone_no'],
                    'place_of_work' => $isMinor ? null : $beneficiary['place_of_work'],
                    'id_no' => $isMinor ? null : $beneficiary['id_no'],
                    'guardian_name' => $isMinor ? $beneficiary['guardian_name'] : null,
                    'guardian_email' => $isMinor ? $beneficiary['email'] : null,
                    'guardian_phone_no' => $isMinor ? $beneficiary['phone_no'] : null,
                    'percentage' => $beneficiary['percentage'],
                ];
    
                if ($id) {
                    $employee->beneficiaries()->where('id', $id)->update($data);
                } else {
                    $employee->beneficiaries()->create($data);
                }
            }
            
            EmployeeBeneficiary::whereIn('id', $request->deleted)->delete();
        
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Employee beneficiaries saved'
        ]);
    }
}
