<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\WaInventoryItemPrice;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\WaInventoryLocationUom;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\UpdateItemPriceUtilityLog;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\Stocks\VerifyStocksExport;

class UpdateItemPriceController extends Controller
{

    protected $model;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'update-item-selling-price';
        $this->permissions_module = 'utility';
    }

    public function index()
    {
        $title = 'Update Item Prices';
        $model = $this->model;

        if (!can('item-prices', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        return view('admin.utility.update_item_price_utility', compact('title', 'model'));
    }

    public function updateItemPrices(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Download Prices') {

                $inventory_items = WaInventoryItem::get(['id', 'stock_id_code', 'description', 'selling_price']);

                $data = $inventory_items->collect();
                $headings = ['ITEM ID', 'ITEM CODE', 'DESCRIPTION', 'SELLING PRICE'];
                $filename = "Selling-Price-List";
                $excelData = $data;
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else if ($request->intent == 'Update' && $request->hasFile('file')) {
                try {
                    ini_set('memory_limit', '512M');
                    set_time_limit(0);

                    $file = $request->file('file');

                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($file);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $expectedHeadings = ['ITEM ID', 'ITEM CODE', 'DESCRIPTION', 'SELLING PRICE'];
                    $fileHeadings = array_map('strtoupper', $data[0]);

                    if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                        return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                    }

                    $recordsToUpdate = [];
                    $duplicates = [];
                    $seenItems = [];

                    foreach ($data as $index => $record) {
                        if ($index === 0) continue;

                        $recordItemId = $record[0];
                        $recordItemCode = $record[1];
                        $recordItemDescription = $record[2];
                        $recordItemSellingPrice = $record[3];

                        if (empty($recordItemId)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM ID cannot be blank',
                            ], 422);
                        }

                        if (empty($recordItemCode)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM Code cannot be blank',
                            ], 422);
                        }

                        // if (!is_numeric($recordItemId)) {
                        //     return response()->json([
                        //         'error' => 'Failed to load excel, ITEM ID and ITEM ID must be numbers',
                        //     ], 422);
                        // }

                        $itemKey = "$recordItemId-$recordItemCode";
                        if (isset($seenItems[$itemKey])) {
                            $duplicates[] = $recordItemId;
                        } else {
                            $seenItems[$itemKey] = true;
                            $recordsToUpdate[] = [
                                'wa_inventory_item_id' => $recordItemId,
                                'wa_inventory_item_code' => $recordItemCode,
                                'wa_inventory_item_description' => $recordItemDescription,
                                'selling_price' => $recordItemSellingPrice,
                                'user_id' => Auth::id(),
                            ];
                        }
                    }

                    if (!empty($duplicates)) {
                        $duplicateItemsStr = implode(', ', $duplicates);
                        return response()->json([
                            'error' => "Failed to load excel. The following duplicate items were found: " . $duplicateItemsStr,
                        ], 422);
                    }

                    foreach ($recordsToUpdate as $record) {
                        $inventory_item = WaInventoryItem::where('stock_id_code', $record['wa_inventory_item_code'])->first();
                        if(!$inventory_item) {
                            return response()->json([
                                'error' => "Failed to load excel. The following item was not found: " . $record['wa_inventory_item_code'],
                            ], 422);
                        }

                        $inventory_item->selling_price = $record['selling_price'];
                        $inventory_item->save();

                        UpdateItemPriceUtilityLog::create([
                            'initiated_by' => Auth::id(),
                            'wa_inventory_item_id' => $record['wa_inventory_item_id'],
                            'wa_location_and_store_id' => null,
                            'wa_inventory_item_price_id' => null,
                            's_p' => 1
                        ]);
                    }
                } catch (\Throwable $th) {
                    return response()->json([
                        'error' => $th->getMessage(),
                    ], 422);
                }
            } else {
                return response()->json([
                    'error' => 'No file uploaded or incorrect intent',
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    private function logUpdate($inventoryItemId, $locationId, $itemPriceId)
    {
        try {
            $log = UpdateItemPriceUtilityLog::create([
                'user_id' => Auth::id(),
                'wa_inventory_item_id' => $inventoryItemId,
                'wa_location_and_store_id' => $locationId,
                'wa_inventory_item_price_id' => $itemPriceId,
            ]);

            Log::info('UpdateItemPriceUtilityLog created successfully.', ['log_id' => $log->id]);
        } catch (\Throwable $th) {
            Log::error('Error creating UpdateItemPriceUtilityLog: ' . $th->getMessage());
            throw $th;
        }
    }
}
