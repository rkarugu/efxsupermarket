<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::withCount('employeeDocuments')->orderBy('name')->get();
        
        return response()->json($documentTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_during_onboarding' => 'required|boolean',
            'system_reserved' => 'nullable|boolean'
        ]);

        try {
            DocumentType::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Document type created successfully'
        ]);
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
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_during_onboarding' => 'required|boolean',
            'system_reserved' => 'nullable|boolean'
        ]);

        try {
            $documentType = DocumentType::find($id);

            $documentType->update($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Document type updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $documentType = DocumentType::find($id);
            
            $documentType->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Document type deleted successfully'
        ]);
    }
}
