<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeDocument;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function employeeDocumentList(Employee $employee)
    {
        return response()->json($employee->documents->load('documentType'));
    }

    public function employeeDocumentCreate(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'document_type_id' => 'required|integer|exists:document_types,id',
            'file_path' => 'required|file'
        ]);

        try {
            $data['file_path'] = $data['file_path']->store('uploads/employee_documents', 'public');

            EmployeeDocument::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee document created successfully"
        ]);
    }

    public function employeeDocumentEdit(Request $request, $id)
    {
        $data = $request->validate([
            'document_type_id' => 'required|integer|exists:document_types,id',
            'file_path' => 'nullable|file'
        ]);

        $employeeDocument = EmployeeDocument::find($id);

        try {
            if (isset($data['file_path']) && $data['file_path'] instanceof UploadedFile) {
                if (Storage::disk('public')->exists($employeeDocument->file_path)) {
                    Storage::disk('public')->delete($employeeDocument->file_path);
                }
                
                $data['file_path'] = $data['file_path']->store('uploads/employee_images', 'public');
            }
            
            $employeeDocument->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee document updated successfully"
        ]);
    }

    public function employeeDocumentDelete($id) {
        $employeeDocument = EmployeeDocument::find($id);

        try {
            if (Storage::disk('public')->exists($employeeDocument->file_path)) {
                Storage::disk('public')->delete($employeeDocument->file_path);
            }
            
            $employeeDocument->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Employee document deleted successfully"
        ]);
    }
}
