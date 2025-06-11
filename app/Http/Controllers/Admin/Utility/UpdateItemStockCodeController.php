<?php

namespace App\Http\Controllers\Admin\Utility;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\Stocks\VerifyStocksExport;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Facades\Excel;

class UpdateItemStockCodeController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'update-item-code';
        $this->pmodel = 'update-item-code';
        $this->title = 'Update Stock Code';
        $this->pmodule = 'utility';
    }

    public function index()
    {
        if (!can('view', $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        return view('admin.utility.update_item_stock_code', compact('title', 'model', 'pmodule', 'permission'));
    }

    public function processItemStockCode(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Update') {

                $file = $request->file('file');

                $Reader = new Xlsx();
                $Reader->setReadDataOnly(true);
                $spreadsheet = $Reader->load($file);
                $data = $spreadsheet->getActiveSheet()->toArray();

                $expectedHeadings = ['CURRENT STOCK CODE', 'NEW STOCK CODE', 'ITEM DESCRIPTION'];
                $fileHeadings = array_map('strtoupper', $data[0]);

                if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                    return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                }

                $recordsToUpdate = [];
                $duplicates = [];
                $seenItems = [];

                foreach ($data as $index => $record) {
                    if ($index === 0) continue;

                    $recordCurrentStockCode = $record[0];
                    $recordNewStockCode = $record[1];
                    $recordItemDescription = $record[2];

                    if (empty($recordCurrentStockCode)) {
                        return response()->json([
                            'error' => 'Failed to load excel, CURRENT STOCK CODE cannot be blank',
                        ], 422);
                    }

                    if (empty($recordNewStockCode)) {
                        return response()->json([
                            'error' => 'Failed to load excel, NEW STOCK CODE cannot be blank',
                        ], 422);
                    }

                    $itemKey = "$recordCurrentStockCode";
                    if (isset($seenItems[$itemKey])) {
                        $duplicates[] = $recordCurrentStockCode;
                    } else {
                        $seenItems[$itemKey] = true;
                        WaInventoryItem::where('stock_id_code', $recordCurrentStockCode)->update(['stock_id_code' => $recordNewStockCode]);
                        WaStockMove::where('stock_id_code', $recordCurrentStockCode)
                            ->update(['stock_id_code' => $recordNewStockCode]);
                    }
                }

                if (!empty($duplicates)) {
                    $duplicateItemsStr = implode(', ', $duplicates);
                    return response()->json([
                        'error' => "Failed to load excel. The following duplicate items were found: " . $duplicateItemsStr,
                    ], 422);
                }

                return response()->json(['message' => 'Item Code Updated'], 200);
            } elseif ($request->intent == 'Template') {
                $data = [];
                $headings = ['CURRENT STOCK CODE', 'NEW STOCK CODE', 'ITEM DESCRIPTION'];
                $filename = "ITEM-BATCH-STOCK-ID-UPDATE-TEMPLATE";
                $excelData = $data;
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else {
                return response()->json([
                    'error' => 'No file uploaded or incorrect intent',
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error:' . $th->getMessage(),
            ], 500);
        }
    }

    public function processSingleItemStockCode(Request $request)
    {
        try {
            if ($request->intent == 'Process') {
                $item = WaInventoryItem::where('stock_id_code', $request->input('current_item_code'))->first();
                if (!$item) {
                    return response()->json(['message' => 'Item does not exist'],500);
                }
                $item_qoh = WaStockMove::where('stock_id_code', $request->input('current_item_code'))->sum('qauntity');
                return response()->json([
                    'item' => $item,
                    'item_qoh' => $item_qoh,
                    'message' => 'Item stock code updated'
                ], 200);
            } elseif ($request->intent == 'Save') {
                $newStockCode = $request->input('new_stock_code');
                $currentItemCode = $request->input('current_item_code');

                $item = WaInventoryItem::where('stock_id_code', $currentItemCode)->firstOrFail();
                WaStockMove::where('stock_id_code', $currentItemCode)->update(['stock_id_code' => $newStockCode]);

                $item->update(['stock_id_code' => $newStockCode]);

                return response()->json([
                    'item' => $item,
                    'message' => 'Item stock code updated'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
