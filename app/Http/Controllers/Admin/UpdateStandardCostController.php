<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationUom;
use App\Models\UpdateItemPriceUtilityLog;
use App\Models\WaInventoryItemPrice;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\Stocks\VerifyStocksExport;
use App\Model\WaInventoryAssignedItems;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;

class UpdateStandardCostController extends Controller
{
    protected $model;
    protected $permissions_module;
    protected $cost_classes;

    public function __construct()
    {
        $this->model = 'update-item-standard-cost';
        $this->permissions_module = 'utility';
        $this->cost_classes = [
            'standard_cost',
            'last_grn_cost',
            'weighted_average_cost',
            'price_list_cost'
        ];
    }

    public function index()
    {
        $title = 'Update Item Standard Cost';
        $model = $this->model;

        if (!can('item-standard-cost', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $costs = $this->cost_classes;

        return view('admin.utility.update_item_standard_cost_utility', compact('title', 'model', 'costs'));
    }

    public function updateItemStandardCost(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            $validator = Validator::make($request->all(), [
                'cost_type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->intent == 'Download Cost') {

                $cost_type = $request->cost_type;
                // $inventory_items = WaInventoryItem::get(['id', 'stock_id_code', 'description', $cost_type]);

                // $data = $inventory_items->collect();
                $data = [];
                $headings = ['ITEM CODE', 'DESCRIPTION', strtoupper(str_replace('_', ' ', $cost_type))];
                $filename = ucfirst(str_replace('_', '-', $cost_type)) . "-EXCEL";
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

                    if ($request->cost_type == 'standard_cost') {
                        $expectedHeadings = ['ITEM CODE', 'DESCRIPTION', 'STANDARD COST'];
                    } else if ($request->cost_type == 'last_grn_cost') {
                        $expectedHeadings = ['ITEM CODE', 'DESCRIPTION', 'LAST GRN COST'];
                    } else if ($request->cost_type == 'weighted_average_cost') {
                        $expectedHeadings = ['ITEM CODE', 'DESCRIPTION', 'WEIGHTED AVERAGE COST'];
                    } else if ($request->cost_type == 'price_list_cost') {
                        $expectedHeadings = ['ITEM CODE', 'DESCRIPTION', 'PRICE LIST COST'];
                    } else {
                        return response()->json(['error' => 'Something went wrong'], 500);
                    }

                    $fileHeadings = array_map('strtoupper', $data[0]);

                    if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                        return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                    }

                    $recordsToUpdate = [];
                    $duplicates = [];
                    $seenItems = [];

                    foreach ($data as $index => $record) {
                        if ($index === 0) continue;

                        $recordItemCode = $record[0];
                        $recordItemDescription = $record[1];
                        $recordItemStandardCost = $record[2];

                        if (empty($recordItemCode)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM CODE cannot be blank',
                            ], 422);
                        }

                        $itemKey = "$recordItemCode";
                        if (isset($seenItems[$itemKey])) {
                            $duplicates[] = $recordItemCode;
                        } else {
                            $seenItems[$itemKey] = true;
                            $recordsToUpdate[] = [
                                'wa_inventory_item_code' => $recordItemCode,
                                'wa_inventory_item_description' => $recordItemDescription,
                                'standard_cost' => $recordItemStandardCost,
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
                        if (isset($request->cost_type) && $request->cost_type == 'standard_cost') {
                            $inventory_item = WaInventoryItem::where('stock_id_code', $record['wa_inventory_item_code'])->first();
                            $inventory_item->standard_cost = $record['standard_cost'];
                            $inventory_item->save();
                            $child_items = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventory_item->id)
                                ->get(['destination_item_id', 'conversion_factor']);
                            $child_inventory_items = WaInventoryItem::whereIn('id', $child_items->pluck('destination_item_id'))
                                ->get()
                                ->each(function ($item) use ($child_items, $record) {
                                    $conversion_factor = $child_items->firstWhere('destination_item_id', $item->id)->conversion_factor;
                                    $calculated_child_cost = $record['standard_cost'] / $conversion_factor;
                                    $item->standard_cost = $calculated_child_cost;
                                    $item->save();
                                });
                        } else if (isset($request->cost_type) && $request->cost_type == 'last_grn_cost') {
                            $inventory_item = WaInventoryItem::where('stock_id_code', $record['wa_inventory_item_code'])->first();
                            $inventory_item->last_grn_cost = $record['standard_cost'];
                            $inventory_item->save();
                            $child_items = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventory_item->id)
                                ->get(['destination_item_id', 'conversion_factor']);
                            $child_inventory_items = WaInventoryItem::whereIn('id', $child_items->pluck('destination_item_id'))
                                ->get()
                                ->each(function ($item) use ($child_items, $record) {
                                    $conversion_factor = $child_items->firstWhere('destination_item_id', $item->id)->conversion_factor;
                                    $calculated_child_cost = $record['standard_cost'] / $conversion_factor;
                                    $item->last_grn_cost = $calculated_child_cost;
                                    $item->save();
                                });
                        } else if (isset($request->cost_type) && $request->cost_type == 'weighted_average_cost') {
                            $inventory_item = WaInventoryItem::where('stock_id_code', $record['wa_inventory_item_code'])->first();
                            $inventory_item->weighted_average_cost = $record['standard_cost'];
                            $inventory_item->save();
                            $child_items = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventory_item->id)
                                ->get(['destination_item_id', 'conversion_factor']);
                            $child_inventory_items = WaInventoryItem::whereIn('id', $child_items->pluck('destination_item_id'))
                                ->get()
                                ->each(function ($item) use ($child_items, $record) {
                                    $conversion_factor = $child_items->firstWhere('destination_item_id', $item->id)->conversion_factor;
                                    $calculated_child_cost = $record['standard_cost'] / $conversion_factor;
                                    $item->weighted_average_cost = $calculated_child_cost;
                                    $item->save();
                                });
                        } else if (isset($request->cost_type) && $request->cost_type == 'price_list_cost') {
                            $inventory_item = WaInventoryItem::where('stock_id_code', $record['wa_inventory_item_code'])->first();
                            $inventory_item->price_list_cost = $record['standard_cost'];
                            $inventory_item->save();
                            $child_items = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventory_item->id)
                                ->get(['destination_item_id', 'conversion_factor']);
                            $child_inventory_items = WaInventoryItem::whereIn('id', $child_items->pluck('destination_item_id'))
                                ->get()
                                ->each(function ($item) use ($child_items, $record) {
                                    $conversion_factor = $child_items->firstWhere('destination_item_id', $item->id)->conversion_factor;
                                    $calculated_child_cost = $record['standard_cost'] / $conversion_factor;
                                    $item->price_list_cost = $calculated_child_cost;
                                    $item->save();
                                });
                        } else {
                            return response()->json([
                                'error' => 'Something went wrong'
                            ], 500);
                        }

                        UpdateItemPriceUtilityLog::create([
                            'initiated_by' => Auth::id(),
                            'wa_inventory_item_id' => $inventory_item->id,
                            'wa_location_and_store_id' => null,
                            'wa_inventory_item_price_id' => null,
                            's_c' => 1
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
}
