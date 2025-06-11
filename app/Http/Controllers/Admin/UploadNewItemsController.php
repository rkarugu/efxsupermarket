<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Models\WaLocationStoreUom;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryLocationUom;
use App\Model\WaInventoryItemSupplier;
use App\Models\UpdateNewItemInventoryUtilityLog;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use App\Models\WaInventoryItemApprovalStatus;
use Illuminate\Support\Facades\Auth;

class UploadNewItemsController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $permission;

    public function __construct()
    {
        $this->model = 'upload-new-items';
        $this->pmodel = 'utility';
        $this->title = 'Upload New Items Utility';
        $this->permission = 'upload-new-items';
    }

    public function index()
    {
        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Upload New Items Utility' => ''];

        if (!can($this->permission, $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        return view('admin.utility.branch_utility.upload_new_items', compact('title', 'model', 'breadcum'));
    }

    public function uploadNewItems(Request $request)
    {

        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Template') {
                $headings = [
                    'ID',
                    'STOCK ID CODE',
                    'DESCRIPTION',
                    'CATEGORY',
                    'SUB CATEGORY',
                    'BIN',
                    'SUPPLIERS',
                    'SELLING PRICE + VAT',
                    'STANDARD COST',
                    'MAX ORDER QTY',
                    'MARGIN TYPE',
                    'MIN MARGIN',
                    'TAX CATEGORY',
                    'PACK SIZE',
                    'ALT CODE',
                    'GROSS WEIGHT (kgs)',
                    'NET WEIGHT (kgs)',
                    'HS CODE'
                ];
                $filename = "Upload-Items-Template";
                $excelData = [
                    ['0001', 'A001', 'Product A', '1', '1', '5', '1,2', 100.00, 80.00, 100, '1', 20, '1', '1', 'ALT001', 2.5, 2.0, '123456'],
                    ['0002', 'A002', 'Product B', '2', '2', '7', '3,4', 150.00, 120.00, 150, '1', 25, '2', '2', 'ALT002', 3.0, 2.5, '654321'],
                    ['0003', 'A003', 'Product C', '3', '3', '5', '1,2', 200.00, 160.00, 200, '2', null, '1', '3', 'ALT003', 1.5, 1.0, '987654'],
                    ['0004', 'A004', 'Product D', '1', '4', '3', '3,4', 120.00, 100.00, 120, '1', 15, '2', '2', 'ALT004', 2.0, 1.5, '456789'],
                    ['0005', 'A005', 'Product E', '2', '5', '5', '5,6', 180.00, 150.00, 180, '2', null, '1', '1', 'ALT005', 3.5, 3.0, '789012'],
                ];
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            }

            if ($request->intent == 'Process' && $request->hasFile('file')) {
                try {

                    $location = $request->branch;

                    $file = $request->file('file');
                    $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($file);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $expectedHeadings = [
                        'ID',
                        'STOCK ID CODE',
                        'DESCRIPTION',
                        'CATEGORY',
                        'SUB CATEGORY',
                        'BIN',
                        'SUPPLIERS',
                        'SELLING PRICE + VAT',
                        'STANDARD COST',
                        'MAX ORDER QTY',
                        'MARGIN TYPE',
                        'MIN MARGIN',
                        'TAX CATEGORY',
                        'PACK SIZE',
                        'ALT CODE',
                        'GROSS WEIGHT (kgs)',
                        'NET WEIGHT (kgs)',
                        'HS CODE'
                    ];
                    $fileHeadings = array_map('strtoupper', $data[0]);

                    if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                        return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                    }

                    $file_item_ids = collect();
                    $file_item_data = collect();
                    $errors = [];
                    $duplicates = collect();
                    $seen_items = collect();
                    $existingItems = collect();

                    foreach ($data as $index => $record) {

                        if ($index > 0) {

                            $file_item_id = $record[0];
                            $file_item_code = $record[1];
                            $description = $record[2];
                            $category = $record[3];
                            $sub_category = $record[4];
                            $bin = $record[5];
                            $suppliers = $record[6];
                            $selling_price = $record[7];
                            $standard_cost = $record[8];
                            $max_order_qty = $record[9];
                            $margin_type = $record[10];
                            $min_margin = $record[11];
                            $tax_category = $record[12];
                            $pack_size = $record[13];
                            $alt_code = $record[14];
                            $gross_weight = $record[15];
                            $net_weight = $record[16];
                            $hs_code = $record[17];

                            if ($file_item_id === null || $file_item_id === '') {
                                return response()->json(['error' => 'Failed to load excel, ITEM ID cannot be blank'], 422);
                            }
                            if ($file_item_code === null || $file_item_code === '') {
                                return response()->json(['error' => 'Failed to load excel, ITEM CODE cannot be blank'], 422);
                            }
                            if ($description === null || $description === '') {
                                return response()->json(['error' => 'Failed to load excel, ITEM DESCRIPTION cannot be blank'], 422);
                            }
                            if ($category === null || $category === '') {
                                return response()->json(['error' => 'Failed to load excel, CATEGORY cannot be blank'], 422);
                            }
                            if ($sub_category === null || $sub_category === '') {
                                return response()->json(['error' => 'Failed to load excel, SUB CATEGORY cannot be blank'], 422);
                            }
                            if ($bin === null || $bin === '') {
                                return response()->json(['error' => 'Failed to load excel, BIN cannot be blank'], 422);
                            }
                            if ($suppliers === null || $suppliers === '') {
                                return response()->json(['error' => 'Failed to load excel, SUPPLIERS cannot be blank'], 422);
                            }
                            if ($selling_price === null || $selling_price === '') {
                                return response()->json(['error' => 'Failed to load excel, SELLING PRICE cannot be blank'], 422);
                            }
                            if ($standard_cost === null || $standard_cost === '') {
                                return response()->json(['error' => 'Failed to load excel, STANDARD COST cannot be blank'], 422);
                            }
                            if ($max_order_qty === null || $max_order_qty === '') {
                                return response()->json(['error' => 'Failed to load excel, MAX ORDER QTY cannot be blank'], 422);
                            }
                            if ($margin_type === null || $margin_type === '') {
                                return response()->json(['error' => 'Failed to load excel, MARGIN TYPE cannot be blank'], 422);
                            }
                            if ($min_margin === null || $min_margin === '') {
                                return response()->json(['error' => 'Failed to load excel, MIN MARGIN cannot be blank'], 422);
                            }
                            if ($tax_category === null || $tax_category === '') {
                                return response()->json(['error' => 'Failed to load excel, TAX CATEGORY cannot be blank'], 422);
                            }
                            if ($pack_size === null || $pack_size === '') {
                                return response()->json(['error' => 'Failed to load excel, PACK SIZE cannot be blank'], 422);
                            }
                            if ($gross_weight === null || $gross_weight === '') {
                                return response()->json(['error' => 'Failed to load excel, GROSS WEIGHT cannot be blank'], 422);
                            }

                            if (!is_numeric($category)) {
                                return response()->json(['error' => 'Failed to load excel, CATEGORY must be a number'], 422);
                            }
                            if (!is_numeric($sub_category)) {
                                return response()->json(['error' => 'Failed to load excel, SUB CATEGORY must be a number'], 422);
                            }
                            if (!is_numeric($bin)) {
                                return response()->json(['error' => 'Failed to load excel, BIN must be a number'], 422);
                            }
                            if (!preg_match('/^\d+(,\d+)*$/', $suppliers)) {
                                return response()->json(['error' => 'Failed to load excel, SUPPLIERS must contain only numbers and commas'], 422);
                            }
                            if (!is_numeric($selling_price)) {
                                return response()->json(['error' => 'Failed to load excel, SELLING PRICE must be a number'], 422);
                            }
                            if (!is_numeric($standard_cost)) {
                                return response()->json(['error' => 'Failed to load excel, STANDARD COST must be a number'], 422);
                            }
                            if (!is_numeric($max_order_qty)) {
                                return response()->json(['error' => 'Failed to load excel, MAX ORDER QTY must be a number'], 422);
                            }
                            if (!is_numeric($margin_type)) {
                                return response()->json(['error' => 'Failed to load excel, MARGIN TYPE must be a number'], 422);
                            }
                            if (!is_numeric($min_margin)) {
                                return response()->json(['error' => 'Failed to load excel, MIN MARGIN must be a number'], 422);
                            }
                            if (!is_numeric($tax_category)) {
                                return response()->json(['error' => 'Failed to load excel, TAX CATEGORY must be a number'], 422);
                            }
                            if (!is_numeric($pack_size)) {
                                return response()->json(['error' => 'Failed to load excel, PACK SIZE must be a number'], 422);
                            }

                            $item_key = $file_item_code;
                            if ($seen_items->has($item_key)) {
                                $duplicates->push($file_item_code);
                            } else {
                                $seen_items->put($item_key, true);

                                $existingItem = DB::table('wa_inventory_items')->where('stock_id_code', $file_item_code)->first();
                                if ($existingItem) {
                                    $existingItems->put($item_key, true);
                                } else {
                                    $file_item_data[] = [
                                        'stock_id' => $file_item_id,
                                        'stock_id_code' => $file_item_code,
                                        'description' => $description,
                                        'category' => $category,
                                        'sub_category' => $sub_category,
                                        'bin' => $bin,
                                        'suppliers' => $suppliers,
                                        'selling_price' => $selling_price,
                                        'standard_cost' => $standard_cost,
                                        'max_order_qty' => $max_order_qty,
                                        'margin_type' => $margin_type,
                                        'min_margin' => $min_margin,
                                        'tax_category' => $tax_category,
                                        'pack_size' => $pack_size,
                                        'alt_code' => $alt_code,
                                        'gross_weight' => $gross_weight,
                                        'net_weight' => $net_weight,
                                        'hs_code' => $hs_code,
                                    ];
                                }
                            }
                        }
                    }

                    if ($duplicates->isNotEmpty()) {
                        $duplicateItemsStr = $duplicates->implode(', ');
                        return response()->json(['error' => "Failed to load excel. The following duplicate items were found: $duplicateItemsStr"], 422);
                    }

                    if ($existingItems->isNotEmpty()) {
                        $existingItemsStr = $existingItems->implode(', ');
                        return response()->json(['error' => "Failed to process items. The following items are already in the system: $existingItemsStr"], 422);
                    }

                    DB::beginTransaction();
                    try {
                        $emptybinsdata = [];
                        foreach ($file_item_data as $item) {
                            $inventoryItem = WaInventoryItem::create(
                                [
                                    'stock_id_code' => $item['stock_id_code'],
                                    'title' => $item['description'],
                                    'description' => $item['description'],
                                    'wa_inventory_category_id' => $item['category'],
                                    'item_sub_category_id' => $item['sub_category'],
                                    'selling_price' => $item['selling_price'],
                                    'standard_cost' => $item['standard_cost'],
                                    'max_order_quantity' => $item['max_order_qty'],
                                    'margin_type' => $item['margin_type'],
                                    'percentage_margin' => $item['min_margin'],
                                    'tax_manager_id' => $item['tax_category'],
                                    'pack_size_id' => $item['pack_size'],
                                    'alt_code' => $item['alt_code'],
                                    'gross_weight' => $item['gross_weight'],
                                    'net_weight' => $item['net_weight'],
                                    'hs_code' => $item['hs_code'],
                                    'image' => '',
                                    'approval_status' => 'Pending New Approval'
                                ]
                            );

                            $jsonData = [
                                'stock_id_code' => $inventoryItem->stock_id_code,
                                'title' => $inventoryItem->description,
                                'description' => $inventoryItem->description,
                                'wa_inventory_category_id' => $inventoryItem->wa_inventory_category_id,
                                'item_sub_category_id' => $inventoryItem->item_sub_category_id,
                                'selling_price' => $inventoryItem->selling_price,
                                'standard_cost' => $inventoryItem->standard_cost,
                                'max_order_quantity' => $inventoryItem->max_order_quantity,
                                'margin_type' => $inventoryItem->margin_type,
                                'percentage_margin' => $inventoryItem->percentage_margin,
                                'tax_manager_id' => $inventoryItem->tax_manager_id,
                                'pack_size_id' => $inventoryItem->pack_size_id,
                                'alt_code' => $inventoryItem->alt_code,
                                'gross_weight' => $inventoryItem->gross_weight,
                                'net_weight' => $inventoryItem->net_weight,
                                'hs_code' => $inventoryItem->hs_code,
                                'image' => $inventoryItem->image,
                                'approval_status' => $inventoryItem->approval_status,
                                '_token' => csrf_token(),
                                'status' => '1',
                                '_method' => 'PATCH',
                                'suppliers' => $item['suppliers'],
                                'current_step' => '3',
                                'packaged_volume' => '0'
                            ];

                            $approvalStatus = WaInventoryItemApprovalStatus::updateOrCreate(
                                ['wa_inventory_items_id' => $inventoryItem->id],
                                [
                                    'status' => 'Pending New Approval',
                                    'approval_by' => getLoggeduserProfile()->id,
                                    'changes' => "[]",
                                    'new_data' => json_encode($jsonData),
                                ]
                            );

                            $suppliers = explode(',', $item['suppliers']);
                            $supplierIds = [];
                            foreach ($suppliers as $supplier) {
                                $supplierModel = WaInventoryItemSupplier::updateOrCreate(
                                    [
                                        'wa_inventory_item_id' => $inventoryItem->id,
                                        'wa_supplier_id' => trim($supplier),
                                    ],
                                    []
                                );
                                $supplierIds[] = $supplierModel->id;
                            }

                            if ($item['bin'] === null) {
                                $emptybinsdata[] = $item;
                            }

                            $location = WaLocationStoreUom::where('uom_id', $item['bin'])->first();

                            $locationUom = WaInventoryLocationUom::updateOrCreate(
                                [
                                    'inventory_id' => $inventoryItem->id,
                                    'uom_id' => $item['bin'],
                                ],
                                [
                                    'location_id' => $location ? $location->location_id : null,
                                ]
                            );

                            $this->logUpdate(
                                $inventoryItem->id,
                                $approvalStatus->id,
                                $supplierIds,
                                $locationUom->id,
                                false
                            );
                        }

                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        return response()->json(['error' => 'Database error: ' . $th->getMessage()], 500);
                    }

                    return redirect()->route('utility.upload_new_items');
                } catch (\Throwable $th) {
                    return response()->json(['error' => 'Error processing request: ' . $th->getMessage()], 400);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error processing request: ' . $th->getMessage()], 400);
        }
    }

    private function logUpdate($inventoryItemId, $approvalStatusId, $supplierIds, $locationUomId, $hasDuplicate)
    {
        try {
            $supplierIdsString = implode(',', $supplierIds);
            UpdateNewItemInventoryUtilityLog::create([
                'initiated_by' => Auth::id(),
                'wa_inventory_item_id' => $inventoryItemId,
                'wa_inventory_item_approval_status_id' => $approvalStatusId,
                'wa_inventory_item_supplier_id' => $supplierIdsString,
                'wa_inventory_location_uom_id' => $locationUomId,
                'has_duplicate' => $hasDuplicate,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
