<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\Stocks\VerifyStocksExport;
use App\Model\WaInventoryItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;

class ItemMarginProcessingController extends Controller
{
    protected $model;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'item-margins';
        $this->permissions_module = 'utility';
    }

    public function index()
    {
        $title = 'Update Item Prices';
        $model = $this->model;

        if (!can('item-margins', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        return view('admin.utility.item_margins', compact('title', 'model'));
    }

    public function downloadItemMargins(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Download') {

                $inventory_items = WaInventoryItem::with('category', 'sub_category', 'suppliers')->get();

                $data = $inventory_items->map(function ($item) {
                    return [
                        'STOCK ID CODE' => $item?->stock_id_code ?? '',
                        'DESCRIPTION' => $item?->description ?? '',
                        'CATEGORY' => $item->category ? ($item->category->id ?? '') . '-' . ($item->category->category_description ?? '') : '',
                        'SUB CATEGORY' => $item->sub_category ? ($item->sub_category->id ?? '') . '-' . ($item->sub_category->description ?? '') : '',
                        'SUPPLIERS' => $item?->suppliers->map(function ($supplier) {
                            return $supplier->id . '-' . $supplier->name;
                        })->join(', ') ?? '',
                        'SELLING PRICE' => $item?->selling_price ?? '',
                        'STANDARD COST' => $item?->standard_cost ?? '',
                        'MARGIN TYPE' => $item->margin_type === null ? '' : ($item->margin_type == 1 ? 'Percentage' : 'Value'),
                        'MIN MARGIN' => $item?->percentage_margin ?? '',
                        'ACTUAL MARGIN' => $item?->actual_margin ?? '',
                        'STATUS' => $item->status === null ? '' : ($item->status == 1 ? 'Active' : 'Retired'),
                    ];
                });

                $headings = [
                    'STOCK ID CODE', 'DESCRIPTION', 'CATEGORY', 'SUB CATEGORY',
                    'SUPPLIERS', 'SELLING PRICE', 'STANDARD COST', 'MARGIN TYPE', 'MIN MARGIN',
                    'ACTUAL MARGIN', 'STATUS'
                ];
                $filename = "Item-List-Excel";
                $excelData = $data;
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else if ($request->intent == 'Update') {
                return response()->json([
                    'error' => 'Coming soon.',
                ], 422);
            } else {
                return response()->json([
                    'error' => 'No file uploaded or incorrect intent',
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error: ' . $th->getMessage()], 500);
        }
    }
}
