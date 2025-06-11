<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use Carbon\Carbon;
use App\Models\ExistingBin;
use App\Models\UnmatchedBin;
use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Model\WaUnitOfMeasure;
use App\Model\WaLocationAndStore;
use App\Models\PendingBinApproval;
use App\Models\WaLocationStoreUom;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\WaInventoryLocationUom;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\Stocks\VerifyStocksExport;
use App\Models\UpdateBinInventoryUtilityLog;
use App\Models\UpdateItemBin;
use App\Models\UpdateItemBinLog;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UpdateBinLocationController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'utility';
        $this->title = 'Update Bin Location';
        $this->pmodule = 'bin-utility';
    }

    public function index()
    {
        if (!can('view', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        ini_set('max_execution_time', 300); // Set to 5 minutes
        ini_set('memory_limit', '512M'); // Increase memory limit

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        
        // Get location ID from request or default
        $location_id = request()->location ?? request()->branch ?? 46;

        // Get locations with specific columns
        $locations = WaLocationAndStore::select('id', 'location_name')
            ->orderBy('location_name')
            ->get();

        // Build base query with necessary relations and pagination
        $itemsPerPage = request('per_page', 50); // Allow customizable items per page
        $itemsquery = WaInventoryLocationUom::select('id', 'location_id', 'inventory_id', 'uom_id')
            ->with([
                'location:id,location_name',
                'item:id,title,stock_id_code',
                'uom:id,title'
            ])
            ->when(request()->has('location') && !empty(request()->location), function($query) {
                return $query->where('location_id', request()->location);
            });

        // Get bins efficiently with caching
        $all_bins = cache()->remember('all_bins', 60, function() {
            return WaUnitOfMeasure::select('id', 'title')
                ->orderBy('title')
                ->get();
        });
        
        // Get unmatched bins efficiently using a single query and caching
        $unmatched_bins = cache()->remember('unmatched_bins_' . $location_id, 60, function() use ($location_id) {
            $matched_bin_ids = WaLocationStoreUom::where('location_id', $location_id)
                ->pluck('uom_id');
            
            return WaUnitOfMeasure::select('id', 'title')
                ->whereNotIn('id', $matched_bin_ids)
                ->orderBy('title')
                ->get();
        });

        // Get other data with specific columns and chunking
        $main_existing_bins_data = ExistingBin::select(
                'id', 'location_id', 'uom_id', 'inventory_id', 
                'item_code', 'item_title', 'created_at'
            )
            ->with([
                'bin:id,title',
                'location:id,location_name',
                'item:id,title,stock_id_code'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($itemsPerPage);

        $main_unmatched_bins_data = UnmatchedBin::select(
                'id', 'location_id', 'uom_id', 'inventory_id', 
                'item_code', 'item_title', 'created_at'
            )
            ->with([
                'bin:id,title',
                'location:id,location_name',
                'item:id,title,stock_id_code'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($itemsPerPage);

        $main_pending_approval_bins_data = PendingBinApproval::select(
                'id', 'location_id', 'uom_id', 'inventory_id', 
                'item_code', 'item_title', 'created_at'
            )
            ->with([
                'bin:id,title',
                'location:id,location_name',
                'item:id,title,stock_id_code'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($itemsPerPage);

        $main_update_item_bins_data = UpdateItemBin::select(
                'id', 'location_id', 'uom_id', 'inventory_id',
                'item_code', 'item_title', 'created_at'
            )
            ->with([
                'bin:id,title',
                'location:id,location_name',
                'item:id,title,stock_id_code'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($itemsPerPage);

        // Get items with pagination
        $items = $itemsquery->paginate($itemsPerPage);

        return view('admin.utility.update_bin', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'locations',
            'location_id',
            'items',
            'all_bins',
            'unmatched_bins',
            'main_existing_bins_data',
            'main_unmatched_bins_data',
            'main_pending_approval_bins_data',
            'main_update_item_bins_data',
            'itemsPerPage'
        ));
    }

    public function updateBinLocation(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);
            $location = $request->branch;

            if ($request->intent == 'Process Bins' && $request->hasFile('file')) {
                $file = $request->file('file');
                $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $Reader->setReadDataOnly(true);
                $spreadsheet = $Reader->load($file);
                $data = $spreadsheet->getActiveSheet()->toArray();

                $expectedHeadings = [
                    'ITEM CODE',
                    'ITEM TITLE',
                    'BIN ID'
                ];
                $fileHeadings = array_map('strtoupper', $data[0]);

                if ($fileHeadings !== array_map('strtoupper', $expectedHeadings)) {
                    return response()->json(['error' => 'Invalid file format. Please ensure the headings and the data are in the following order: ' . implode(', ', $expectedHeadings)], 422);
                }

                $file_item_data = collect();
                $errors = [];
                $duplicates = collect();
                $seen_items = collect();

                foreach ($data as $index => $record) {
                    if ($index > 0) {
                        $file_item_code = $record[0];
                        $file_item_title = $record[1];
                        $file_bin_id = $record[2];

                        if (empty($file_item_code)) {
                            return response()->json(['error' => 'Failed to load excel, ITEM CODE cannot be blank'], 422);
                        }
                        if (empty($file_item_title)) {
                            return response()->json(['error' => 'Failed to load excel, ITEM TITLE cannot be blank'], 422);
                        }
                        if (empty($file_bin_id)) {
                            return response()->json(['error' => 'Failed to load excel, BIN cannot be blank'], 422);
                        }

                        if (!is_numeric($file_bin_id)) {
                            return response()->json(['error' => 'Failed to load excel, BIN must be a number'], 422);
                        }

                        $item_key = $file_item_code . '-' . $file_bin_id;
                        if ($seen_items->has($item_key)) {
                            $duplicates->push($file_item_code);
                        } else {
                            $seen_items->put($item_key, true);

                            if (!$file_item_data->has($file_bin_id)) {
                                $file_item_data->put($file_bin_id, collect());
                            }
                            $file_item_data->get($file_bin_id)->push([
                                'item_code' => $file_item_code,
                                'item_title' => $file_item_title
                            ]);
                        }
                    }
                }

                if ($duplicates->isNotEmpty()) {
                    $duplicateItemsStr = $duplicates->implode(', ');
                    return response()->json(['error' => "Failed to load excel. The following duplicate items were found: $duplicateItemsStr"], 422);
                }

                ExistingBin::truncate();
                UnmatchedBin::truncate();
                PendingBinApproval::truncate();
                UpdateItemBin::truncate();

                $file_bin_ids = $file_item_data->keys()->toArray();
                $existingBinsData = [];
                $updateItemBinsData = [];
                $missingBinsData = [];
                $file_bin_ids_chunks = array_chunk($file_bin_ids, 1000);
                $missingItemIds = [];
                $noExistingBinsData = [];

                foreach ($file_bin_ids_chunks as $chunk) {
                    $existing_uom_ids = WaLocationStoreUom::whereIn('uom_id', $chunk)->where('location_id', $location)->pluck('id')->toArray();
                    $non_existing_uom_ids = WaLocationStoreUom::whereNotIn('uom_id', $chunk)->where('location_id', $location)->pluck('id')->toArray();

                    foreach ($chunk as $file_bin_id) {
                        $binExists = WaLocationStoreUom::where('uom_id', $file_bin_id)->where('location_id', $location)->exists();
                        if ($binExists) {
                            foreach ($file_item_data[$file_bin_id] as $item_data) {
                                $wa_inventory_item = WaInventoryItem::where('stock_id_code', $item_data['item_code'])->first();

                                if ($wa_inventory_item) {

                                    $itemInBin = WaInventoryLocationUom::where('inventory_id', $wa_inventory_item->id)
                                        ->where('location_id', $location)
                                        ->first();

                                    if ($itemInBin) {
                                        $existingBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $itemInBin->uom_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $updateItemBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $file_bin_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $this->logUpdateBinInventoryUtility($wa_inventory_item->id, $location, $itemInBin->uom_id, 'existing');
                                    } else {
                                        $missingBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $file_bin_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $this->logUpdateBinInventoryUtility($wa_inventory_item->id, $location, $file_bin_id, 'pending');
                                    }
                                } else {
                                    $missingItemIds[] = $item_data['item_code'];
                                }
                            }
                        } else if (!$binExists) {
                            foreach ($file_item_data[$file_bin_id] as $item_data) {
                                $wa_inventory_item = WaInventoryItem::where('stock_id_code', $item_data['item_code'])->first();
                                if ($wa_inventory_item) {

                                    $itemInBin = WaInventoryLocationUom::where('uom_id', $file_bin_id)
                                        ->where('inventory_id', $wa_inventory_item->id)
                                        ->where('location_id', $location)
                                        ->first();

                                    if ($itemInBin) {
                                        $existingBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $itemInBin->uom_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $updateItemBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $file_bin_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $this->logUpdateBinInventoryUtility($wa_inventory_item->id, $location, $itemInBin->uom_id, 'existing');
                                    } else {
                                        $noExistingBinsData[] = [
                                            'location_id' => $location,
                                            'uom_id' => $file_bin_id,
                                            'inventory_id' => $wa_inventory_item->id,
                                            'item_code' => $item_data['item_code'],
                                            'item_title' => $item_data['item_title'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $this->logUpdateBinInventoryUtility($wa_inventory_item->id, $location, $file_bin_id, 'rejected');
                                    }
                                } else {
                                    $missingItemIds[] = $item_data['item_code'];
                                }
                            }
                        }
                    }
                }

                if (!empty($missingItemIds)) {
                    $missingItemIdsStr = implode(', ', $missingItemIds);
                    return response()->json(['error' => "Failed to load excel. The following Items do not exist in the item centre: $missingItemIdsStr"], 422);
                }

                DB::beginTransaction();
                try {
                    if (!empty($existingBinsData)) {
                        ExistingBin::insert($existingBinsData);
                    }
                    if (!empty($missingBinsData)) {
                        PendingBinApproval::insert($missingBinsData);
                    }
                    if (!empty($noExistingBinsData)) {
                        UnmatchedBin::insert($noExistingBinsData);
                    }
                    if (!empty($updateItemBinsData)) {
                        UpdateItemBin::insert($updateItemBinsData);
                    }
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollback();
                    return response()->json(['error' => 'Database error: ' . $th->getMessage()], 500);
                }
                return redirect()->route('utility.update_bin', compact('location'));
            } else if ($request->intent == 'Template') {
                $headings = ['ITEM CODE', 'ITEM TITLE', 'BIN ID'];
                $filename = "Bin-Sample-Excel";
                $excelData = [];
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else {
                return response()->json(['error' => 'No file uploaded or incorrect intent.'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
    }

    public function approveUpdateBinLocation(Request $request)
    {
        try {
            $main_pending_approval_bins_data = json_decode($request->input('main_pending_approval_bins_data'), true);

            $location_id = !empty($main_pending_approval_bins_data) ? $main_pending_approval_bins_data[0]['location_id'] : null;

            if (!$location_id) {
                return response()->json(['error' => 'Location ID is missing.'], 422);
            }

            $main_pending_approval_bins = PendingBinApproval::where('location_id', $location_id)
                ->pluck('uom_id', 'inventory_id');

            if ($main_pending_approval_bins->isEmpty()) {
                return response()->json(['error' => 'No pending approvals found.'], 422);
            }

            foreach ($main_pending_approval_bins as $inventory_id => $uom_id) {
                WaLocationStoreUom::firstOrCreate([
                    'uom_id' => $uom_id,
                    'location_id' => $location_id,
                ]);

                WaInventoryLocationUom::firstOrCreate([
                    'uom_id' => $uom_id,
                    'location_id' => $location_id,
                    'inventory_id' => $inventory_id,
                ]);

                $log = UpdateBinInventoryUtilityLog::where('wa_inventory_item_id', $inventory_id)->first();
                $log->approved_by = auth()->id();
                $log->approved = 1;
                $log->save();
            }

            ExistingBin::truncate();
            UnmatchedBin::truncate();
            PendingBinApproval::truncate();

            return response()->json(['message' => 'Items Uploaded.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function logUpdateBinInventoryUtility($itemId, $locationId, $binId, $status)
    {
        $log = new UpdateBinInventoryUtilityLog();
        $log->initiated_by = auth()->id();
        $log->wa_inventory_item_id = $itemId;
        $log->wa_location_and_store_id = $locationId;
        $log->wa_unit_of_measure_id = $binId;
        $log->pending_approval_status = ($status === 'pending') ? 1 : 0;
        $log->rejected_bin_status = ($status === 'rejected') ? 1 : 0;
        $log->existing_bin_status = ($status === 'existing') ? 1 : 0;
        $log->approved = ($status === 'approved') ? 1 : 0;
        $log->save();
    }

    public function updateItemBinLocation(Request $request)
    {
        try {
            $main_update_item_bins_data = json_decode($request->input('main_update_item_bins_data'), true);

            foreach ($main_update_item_bins_data as $main_update_item_bins) {
                $inventory_item_location = WaInventoryLocationUom::where('inventory_id', $main_update_item_bins['inventory_id'])
                    ->where('location_id', $main_update_item_bins['location_id'])
                    ->first();

                if (!$inventory_item_location) {
                    return response()->json(['message' => 'Item does not exist']);
                }

                UpdateItemBinLog::create([
                    'user_id' => Auth::user()->id,
                    'inventory_item_id' => $main_update_item_bins['inventory_id'],
                    'previous_uom_id' => $inventory_item_location->uom_id,
                    'new_uom_id' => $main_update_item_bins['uom_id'],
                    'location_id' => $inventory_item_location->location_id,
                ]);

                $inventory_item_location->uom_id = $main_update_item_bins['uom_id'];
                $inventory_item_location->save();
            }

            ExistingBin::truncate();
            UnmatchedBin::truncate();
            PendingBinApproval::truncate();
            UpdateItemBin::truncate();

            return response()->json(['message' => 'Item bin updated'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
