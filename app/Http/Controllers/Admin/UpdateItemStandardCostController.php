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

class UpdateItemStandardCostController extends Controller
{
    protected $model;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'update-item-standard-cost';
        $this->permissions_module = 'utility';
    }

    public function index()
    {
        $title = 'Update Item Standard Cost';
        $model = $this->model;

        if (!can('item-standard-cost', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        return view('admin.utility.update_item_standard_cost_utility', compact('title', 'model'));
    }

    public function updateItemStandardCost(Request $request)
    {
        if (!can('item-standard-cost', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Download Prices') {

                if (!can('download-standard-cost', $this->permissions_module)) {
                    Session::flash('warning', pageRestrictedMessage());
                    return redirect()->back();
                }

                $request->validate([
                    'branch' => 'required',
                ]);

                $location = $request->branch;

                $branch_items = WaInventoryLocationUom::where('location_id', $location)
                    ->get();

                $inventory_ids_from_branch = $branch_items->pluck('inventory_id');
                $location_ids = $branch_items->pluck('location_id');

                $branch_item_prices = WaInventoryItemPrice::whereIn('wa_inventory_item_id', $inventory_ids_from_branch)
                    ->whereIn('store_location_id', $location_ids)
                    ->get(['wa_inventory_item_id', 'store_location_id', 'selling_price']);

                $inventory_ids_from_prices = $branch_item_prices->pluck('wa_inventory_item_id');

                $all_inventory_ids = $inventory_ids_from_branch->merge($inventory_ids_from_prices)->unique()->toArray();

                $inventory_items = WaInventoryItem::whereIn('id', $all_inventory_ids)->get(['id', 'standard_cost']);

                $default_branch_id = $branch_item_prices->isNotEmpty() ? $branch_item_prices->first()->store_location_id : null;

                $data = collect();

                $inventory_ids_from_branch->each(function ($inventory_id) use ($branch_item_prices, $inventory_items, $default_branch_id, $data) {
                    $item = $inventory_items->firstWhere('id', $inventory_id);
                    $price = $branch_item_prices->where('wa_inventory_item_id', $inventory_id)->first();

                    if (!$data->contains('ITEM ID', $inventory_id)) {
                        $data->push([
                            'BRANCH ID' => $default_branch_id,
                            'ITEM ID' => $inventory_id,
                            'SELLING PRICE' => $price ? $price->selling_price : null,
                            'STANDARD COST' => $item ? $item->standard_cost : null,
                        ]);
                    }
                });

                $headings = ['BRANCH ID', 'ITEM ID', 'SELLING PRICE', 'STANDARD COST'];
                $filename = "Branch-Item-prices-Excel";
                $excelData = $data;
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else if ($request->intent == 'Upload' && $request->hasFile('file')) {

                if (!can('update-standard-cost', $this->permissions_module)) {
                    Session::flash('warning', pageRestrictedMessage());
                    return redirect()->back();
                }

                try {
                    ini_set('memory_limit', '512M');
                    set_time_limit(0);

                    $file = $request->file('file');

                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($file);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $expectedHeadings = ['BRANCH ID', 'ITEM ID', 'SELLING PRICE', 'STANDARD COST'];
                    $fileHeadings = array_map('strtoupper', $data[0]);

                    if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                        return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                    }

                    $recordsToUpdate = [];
                    $duplicates = [];
                    $seenItems = [];

                    foreach ($data as $index => $record) {
                        if ($index === 0) continue;

                        $recordBranchId = $record[0];
                        $recordItemId = $record[1];
                        $recordItemSellingPrice = $record[2];
                        $recordItemStandardCost = $record[3];

                        if (empty($recordBranchId) || empty($recordItemId)) {
                            return response()->json([
                                'error' => 'Failed to load excel, BRANCH ID and ITEM ID cannot be blank',
                            ], 422);
                        }

                        if (empty($recordItemId)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM ID and ITEM ID cannot be blank',
                            ], 422);
                        }

                        if (!is_numeric($recordBranchId) || !is_numeric($recordItemId)) {
                            return response()->json([
                                'error' => 'Failed to load excel, BRANCH ID and ITEM ID must be numbers',
                            ], 422);
                        }

                        if (!is_numeric($recordItemId)) {
                            return response()->json([
                                'error' => 'Failed to load excel, ITEM ID and ITEM ID must be numbers',
                            ], 422);
                        }

                        $itemKey = "$recordBranchId-$recordItemId";
                        if (isset($seenItems[$itemKey])) {
                            $duplicates[] = $recordItemId;
                        } else {
                            $seenItems[$itemKey] = true;
                            $recordsToUpdate[] = [
                                'wa_inventory_item_id' => $recordItemId,
                                'store_location_id' => $recordBranchId,
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
                        $itemPrice = WaInventoryItemPrice::updateOrCreate(
                            [
                                'wa_inventory_item_id' => $record['wa_inventory_item_id'],
                                'store_location_id' => $record['store_location_id'],
                            ],
                            [
                                'selling_price' => $record['selling_price'],
                                'user_id' => $record['user_id'],
                            ]
                        );

                        UpdateItemPriceUtilityLog::create([
                            'initiated_by' => Auth::id(),
                            'wa_inventory_item_id' => $record['wa_inventory_item_id'],
                            'wa_location_and_store_id' => $record['store_location_id'],
                            'wa_inventory_item_price_id' => $itemPrice->id,
                        ]);
                    }
                } catch (\Throwable $th) {
                    return response()->json([
                        'error' => $th->getMessage(),
                    ], 422);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error: ' . $th->getMessage()], 500);
        }
    }
}
