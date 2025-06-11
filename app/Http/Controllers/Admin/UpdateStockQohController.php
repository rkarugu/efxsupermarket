<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UpdateStockQohController extends Controller
{
    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'update-stock-qoh';
        $this->pmodel = 'update-stock-qoh';
        $this->title = 'Update Stock QOH';
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

        $locations = WaLocationAndStore::select('id', 'location_name')->get();

        return view('admin.utility.item_stock_qoh', compact('title', 'model', 'pmodule', 'permission', 'locations'));
    }

    public function updateStocks(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->intent == 'Download Stocks') {

                $query = "
                    SELECT i.stock_id_code, i.description, 
                           COALESCE(SUM(m.qauntity), 0) as current_qoh
                    FROM wa_inventory_items i
                    LEFT JOIN wa_stock_moves m ON i.stock_id_code = m.stock_id_code
                    GROUP BY i.stock_id_code, i.description
                ";

                $inventory_items = DB::select($query);

                $data = collect($inventory_items)->map(function ($item) {
                    return [
                        'stock_id_code' => $item->stock_id_code,
                        'description' => $item->description,
                        'current_qoh' => $item->current_qoh,
                        'new_qoh' => null
                    ];
                });

                $headings = ['ITEM CODE', 'ITEM DESCRIPTION', 'CURRENT QOH', 'NEW QOH'];
                $filename = "ITEM-QOH-EXCEL";

                return ExcelDownloadService::download($filename, $data, $headings);
            } else if ($request->intent == 'Update') {

                $validator = Validator::make($request->all(), [
                    'location' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $locationdata = WaLocationAndStore::where('id', $request->location)->first();

                $file = $request->file('file');
                $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $Reader->setReadDataOnly(true);
                $spreadsheet = $Reader->load($file);
                $data = $spreadsheet->getActiveSheet()->toArray();

                $expectedHeadings = [
                    'ITEM CODE',
                    'ITEM DESCRIPTION',
                    'CURRENT QOH',
                    'NEW QOH'
                ];
                $fileHeadings = array_map('strtoupper', $data[0]);

                if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                    return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                }

                $file_item_data = [];
                $errors = [];
                $duplicates = [];
                $seen_items = [];

                foreach ($data as $index => $record) {
                    if ($index === 0) continue;

                    $recordItemCode = $record[0];
                    $recordItemDescription = $record[1];
                    $recordCurrentItemQoh = $record[2];
                    $recordItemQoh = $record[3];

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
                            'wa_inventory_new_qoh' => $recordItemQoh,
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
                    if ($inventory_item) {
                        $stock_moves = WaStockMove::where('wa_inventory_item_id', $inventory_item->id)
                            ->where('wa_location_and_store_id', $locationdata->id)
                            ->orderBy('created_at', 'asc')
                            ->get();

                        $previous_qoh = null;

                        if ($stock_moves->isEmpty()) {
                            WaStockMove::create([
                                'user_id' => Auth::user()->id,
                                'stock_id_code' => $inventory_item->stock_id_code,
                                'standard_cost' => $inventory_item->standard_cost,
                                'price' => $inventory_item->standard_cost,
                                'wa_inventory_item_id' => $inventory_item->id,
                                'wa_location_and_store_id' => $locationdata->id,
                                'restaurant_id' => $locationdata->wa_branch_id,
                                'qauntity' => $record['wa_inventory_new_qoh'] ?? 0,
                                'new_qoh' => $record['wa_inventory_new_qoh'] ?? 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } else {
                            foreach ($stock_moves as $index => $stock_move) {
                                if ($index == 0) {
                                    $stock_move->new_qoh = $stock_move->qauntity;
                                } else {
                                    $stock_move->new_qoh = $previous_qoh + $stock_move->qauntity;
                                }
                                $previous_qoh = $stock_move->new_qoh;
                                $stock_move->save();
                            }
                            $new_quantity = $record['wa_inventory_new_qoh'] ?? 0;
                            $new_qoh = $previous_qoh + $new_quantity;
                            WaStockMove::create([
                                'user_id' => Auth::user()->id,
                                'stock_id_code' => $inventory_item->stock_id_code,
                                'standard_cost' => $inventory_item->standard_cost,
                                'price' => $inventory_item->standard_cost,
                                'wa_inventory_item_id' => $inventory_item->id,
                                'wa_location_and_store_id' => $locationdata->id,
                                'restaurant_id' => $locationdata->wa_branch_id,
                                'qauntity' => $new_quantity,
                                'new_qoh' => $new_qoh,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } else {
                        return response()->json([
                            'error' => 'Something went wrong'
                        ], 500);
                    }
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
