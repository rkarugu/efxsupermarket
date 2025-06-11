<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GeneralExcelFileImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GeneralExcelFileImportController extends Controller
{
    public function import(): JsonResponse
    {
        Excel::import(new GeneralExcelFileImport, public_path('file.xlsx'));
        return $this->jsonify(['message' => 'success'], 200);
    }
}
