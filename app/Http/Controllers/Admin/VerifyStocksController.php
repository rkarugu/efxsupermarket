<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Http\Controllers\Controller;
use App\Model\WaStockMove;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use App\Models\UpdateItemPriceUtilityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyStocksController extends Controller
{

    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'verify-stocks';
        $this->pmodel = 'utility';
        $this->title = 'Verify Stocks';
        $this->pmodule = 'utility';
    }

    public function index()
    {
        if (!can('verify-stocks', $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $locations = WaLocationAndStore::select('id', 'location_name')->get();
        return view('admin.utility.verify_stocks', compact('title', 'model', 'pmodule', 'permission', 'locations'));
    }

    public function processVerifyStocks(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $locations = WaLocationAndStore::select('id', 'location_name')->get();
        try {

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

                $expectedHeadings = [
                    'ID',
                    'ItemLookupCode',
                    'Description',
                    'Price',
                    'Cost',
                    'QOH'
                ];
                $fileHeadings = array_map('strtoupper', array_shift($data));

                if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                    return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                }

                $records = collect($data)->transform(function ($record) {
                    return [
                        'id' => $record[0],
                        'stock_id_code' => $record[1],
                        'description' => $record[2],
                        'price' => $record[3],
                        'cost' => $record[4],
                        'qoh' => $record[5],
                    ];
                });

                $duplicates = $records->groupBy(function ($record) {
                    return $record['id'] . '-' . $record['stock_id_code'];
                })->filter(function ($group) {
                    return $group->count() > 1;
                })->map(function ($group) {
                    return $group->first()['id'];
                })->values();

                if ($duplicates->isNotEmpty()) {
                    $duplicateItemsStr = $duplicates->implode(', ');
                    return response()->json(['error' => "Failed to load excel. The following duplicate items were found: " . $duplicateItemsStr], 422);
                }

                $itemCodes = $records->pluck('stock_id_code')->unique()->values()->all();

                $qohQuery = "SELECT 
                SUM(qauntity)
                FROM
                    `wa_stock_moves`
                WHERE
                    `wa_inventory_item_id` = `wa_inventory_items`.`id`";

                if ($request->location) {
                    $qohQuery .= " AND wa_location_and_store_id = $request->location";
                }

                $verifystocks = WaInventoryItem::select([
                    'wa_inventory_items.*',
                    DB::raw("($qohQuery) as quantity"),
                ])
                    ->whereIn('wa_inventory_items.stock_id_code', $itemCodes)
                    ->with(['category', 'suppliers' => function ($query) {
                        $query->with('users');
                    }])
                    ->get()
                    ->keyBy('stock_id_code');

                $recordsToCheck = $records->map(function ($record) use ($verifystocks, $request) {
                    $item = $verifystocks->get($record['stock_id_code']);
                    $current_qoh = $item ? ($item->quantity ?? 0)  : 0;

                    $suppliers = $item ? $item->suppliers->pluck('name')->implode(', ') : null;
                    $procurement_users = $item ? $item->suppliers->flatMap(fn($supplier) => $supplier['users'] ?? [])->pluck('name')->implode(', ') : null;

                    return [
                        'id' => $record['id'],
                        'stock_id_code' => $record['stock_id_code'],
                        'description' => $record['description'],
                        'price' => $record['price'],
                        'cost' => $record['cost'],
                        'qoh' => $record['qoh'],
                        'current_price' => $item ? $item->selling_price : null,
                        'current_cost' => $item ? $item->standard_cost : null,
                        'current_qoh' => $current_qoh,
                        'status' => $item ? 'Found' : 'Not Found',
                        'suppliers' => $suppliers,
                        'procurement_users' => $procurement_users,
                    ];
                });

                $itemsNotFound = $recordsToCheck->filter(function ($record) {
                    return $record['status'] === 'Not Found';
                });

                $recordsToCheck = $recordsToCheck->filter(function ($record) {
                    return $record['status'] === 'Found';
                });

                $statuses = $recordsToCheck->pluck('status');
                $logData = [
                    'initiated_by' => Auth::id(),
                    'wa_inventory_item_id' => $recordsToCheck->pluck('id'),
                    'wa_location_and_store_id' => null,
                    'wa_inventory_item_price_id' => null,
                    'status' => $statuses,
                ];
                UpdateItemPriceUtilityLog::insert($logData);

                $mainHeadings = [
                    'ID',
                    'ItemLookupCode',
                    'Description',
                    'Price',
                    'Cost',
                    'QOH',
                    'Bizwiz Current Price',
                    'Bizwiz Current Cost',
                    'Bizwiz QOH',
                    'Status',
                    'Suppliers',
                    'Procurement User'
                ];
                $excelData = $recordsToCheck->merge($itemsNotFound)->toArray();

                $filename = "Combined-Stocks";

                return ExcelDownloadService::download($filename, collect($excelData), $mainHeadings);
            } else if ($request->intent == 'Template') {
                $mainHeadings = [
                    'ID',
                    'ItemLookupCode',
                    'Description',
                    'Price',
                    'Cost',
                    'QOH'
                ];
                $excelData = [];
                $filename = "Combined-Stocks-Template";
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
