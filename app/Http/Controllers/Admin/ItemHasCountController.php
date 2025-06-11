<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Models\UpdateItemPriceUtilityLog;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ItemHasCountController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'item-has-count';
        $this->pmodel = 'item-has-count';
        $this->title = 'Item Has Count Utility';
        $this->pmodule = 'item-has-count';
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

        return view('admin.utility.update_item_has_count', compact('title', 'model', 'pmodule', 'permission'));
    }

    public function processItemHasCount(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Process' && $request->hasFile('file')) {

                $file = $request->file('file');

                $extension = $file->getClientOriginalExtension();

                if ($extension == 'xlsx') {
                    $Reader = new Xlsx();
                } elseif ($extension == 'xls') {
                    $Reader = new Xls();
                } else {
                    throw new \Exception('Unsupported file format.');
                }

                $Reader->setReadDataOnly(true);
                $spreadsheet = $Reader->load($file);
                $data = $spreadsheet->getActiveSheet()->toArray();


                $expectedHeadings = ['ITEM CODE', 'ITEM DESCRIPTION', 'TOTAL COUNT'];
                $fileHeadings = array_map('strtoupper', $data[0]);

                if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                    return response()->json([
                        'error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)
                    ], 422);
                }

                $itemCodes = collect(array_column($data, 0));
                $itemCodes = $itemCodes->filter(fn($code) => !empty($code)); 
                $existingItems = WaInventoryItem::whereIn('stock_id_code', $itemCodes->all())->get();

                $recordsToCheck = [];
                $itemsNotFound = [];
                $duplicates = collect();
                $seen_items = collect();

                foreach ($data as $index => $record) {
                    if ($index > 0) {
                        $recordItemCode = $record[0];
                        $recordItemCount = $record[2];

                        if (empty($recordItemCount) || $recordItemCount == null || $recordItemCount == '') {
                            continue;
                        }

                        if (empty($recordItemCode)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM CODE cannot be blank',
                            ], 422);
                        }

                        if ($seen_items->has($recordItemCode)) {
                            $duplicates->push($recordItemCode);
                        } else {
                            $seen_items->put($recordItemCode, true);
                            $inventory_item = $existingItems->firstWhere('stock_id_code', $recordItemCode);

                            if ($inventory_item) {
                                $itemsToUpdate[$inventory_item->id] = ['item_count' => $recordItemCount];
                            } else {
                                return response()->json([
                                    'error' => "Item not found: " . $recordItemCode,
                                ], 422);
                            }

                            $logData = [
                                'initiated_by' => Auth::id(),
                                'wa_inventory_item_id' => $inventory_item->id,
                                'wa_location_and_store_id' => null,
                                'wa_inventory_item_price_id' => null,
                                'status' => 'Updated from ' . $inventory_item->item_count . ' to ' . $recordItemCode
                            ];

                        }
                    }
                }

                foreach ($itemsToUpdate as $itemId => $updateData) {
                    WaInventoryItem::where('id', $itemId)->update($updateData);
                }
            
                UpdateItemPriceUtilityLog::insert($logData);

                if ($duplicates->isNotEmpty()) {
                    $duplicateItemsStr = $duplicates->implode(', ');
                    return response()->json([
                        'error' => "Failed to load excel. The following duplicate items were found: " . $duplicateItemsStr,
                    ], 422);
                }

                return response()->json([
                    'success' => 'Item count updated',
                ], 200);
            } else if ($request->intent == 'Template') {
                $mainHeadings = [
                    'ITEM CODE',
                    'ITEM DESCRIPTION',
                    'TOTAL COUNT'
                ];

                $excelData = [];
                $filename = "Item-Count-Template";
                return ExcelDownloadService::download($filename, collect($excelData), $mainHeadings);
            } else {
                return response()->json([
                    'error' => 'No file uploaded or incorrect intent',
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error:' . $th->getMessage(),
            ], 422);
        }
    }
}
