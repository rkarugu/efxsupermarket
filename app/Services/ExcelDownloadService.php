<?php

namespace App\Services;

use App\Exports\GeneralExcelExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelDownloadService
{
    /**
     * @param string $filename The download file name, without the extension
     * @param Collection $data Download data
     * @param array $headings Column headers
     * @return BinaryFileResponse
     */
    static public function download(string $filename, Collection $data, array $headings): BinaryFileResponse
    {
        $export = new GeneralExcelExport($data, $headings);
        return Excel::download($export, "$filename.xlsx");
    }
}