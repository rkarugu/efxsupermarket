<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Suppliers\UsersSuppliersExport;
use Session;
use App\Model\User;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use App\Model\WaUserSupplier;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ExcelDownloadService;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Model\WaInventoryLocationStockStatus;
use Maatwebsite\Excel\Facades\Excel;

class UtilityController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'utilities';
        $this->title = 'Utility';
        $this->pmodule = 'utility';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        return view('admin.utility.index', compact('title', 'model', 'pmodule', 'permission'));
    }

    public function update(Request $request)
    {
        try {
            $branch = WaLocationAndStore::find($request->branch);
            $locationstatus = WaInventoryLocationStockStatus::where('wa_location_and_stores_id', $request->branch)->get();
            $locationids = $locationstatus->pluck('wa_location_and_stores_id');

            $manualData = $this->processExcel($request);

            $itemIds = [];
            $itemCodes = [];

            foreach ($manualData as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                // $itemId = $row[0];
                $itemCode = $row[0];

                // $itemIds[] = $itemId;
                $itemCodes[] = $itemCode;
            }

            $inventoryProcessedItems = WaInventoryItem::whereIn('stock_id_code', $itemCodes)->get();
            $itemIds = $inventoryProcessedItems->pluck('id');

            $inventoryItems = WaInventoryLocationStockStatus::whereIn('wa_inventory_item_id', $itemIds)
                ->where('wa_location_and_stores_id', $request->branch)
                ->with('item')
                ->get();

            foreach ($manualData as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                $itemCode = $row[0];
                $inventoryItem = $inventoryProcessedItems->firstWhere('stock_id_code', $itemCode);

                if (!$inventoryItems->contains('wa_inventory_item_id', $inventoryItem->id)) {
                    $newInventoryItem = new WaInventoryLocationStockStatus();
                    $newInventoryItem->wa_inventory_item_id = $inventoryItem->id;
                    $newInventoryItem->wa_location_and_stores_id = $request->branch;
                    if ($request->intent === "Process Max Stock") {
                        // $newInventoryItem->max_stock = $row[2];
                        $newInventoryItem->max_stock = 0;
                    } elseif ($request->intent === "Process Reorder Level") {
                        // $newInventoryItem->re_order_level = $row[2];
                        $newInventoryItem->re_order_level = 0;
                    }
                    $newInventoryItem->save();
                    $matchedInventoryItems[] = $newInventoryItem;
                }
            }

            $inventoryItems = WaInventoryLocationStockStatus::whereIn('wa_inventory_item_id', $itemIds)
                ->where('wa_location_and_stores_id', $request->branch)
                ->with('item')
                ->get();

            $matchedInventoryItems = [];
            if ($request->intent === "Process Max Stock") {
                foreach ($inventoryItems as $inventoryItem) {
                    foreach ($manualData as $row) {
                        if ($inventoryItem->item && $inventoryItem->item->stock_id_code == $row[0]) {
                            $inventoryItem->suggested_max_stock = $row[2];
                            // $inventoryItem->suggested_reorder_level = $row[4];
                            $inventoryItem->re_order_level = $inventoryItem->re_order_level ?? null;
                            $inventoryItem->max_stock = $inventoryItem->max_stock ?? null;
                            $inventoryItem->id = $inventoryItem->item->id ?? null;
                            $inventoryItem->stock_id_code = $inventoryItem->item->stock_id_code ?? null;
                            $inventoryItem->description = $inventoryItem->item->description ?? null;
                            $matchedInventoryItems[] = $inventoryItem;
                            break;
                        }
                    }
                }
            } else if ($request->intent === "Process Reorder Level") {
                foreach ($inventoryItems as $inventoryItem) {
                    foreach ($manualData as $row) {
                        if ($inventoryItem->item && $inventoryItem->item->stock_id_code == $row[0]) {
                            // $inventoryItem->suggested_max_stock = $row[3];
                            $inventoryItem->suggested_reorder_level = $row[2];
                            $inventoryItem->re_order_level = $inventoryItem->re_order_level ?? null;
                            $inventoryItem->max_stock = $inventoryItem->max_stock ?? null;
                            $inventoryItem->id = $inventoryItem->item->id ?? null;
                            $inventoryItem->stock_id_code = $inventoryItem->item->stock_id_code ?? null;
                            $inventoryItem->description = $inventoryItem->item->description ?? null;
                            $matchedInventoryItems[] = $inventoryItem;
                            break;
                        }
                    }
                }
            }


            return response()->json([
                'success' => true,
                'data' => $matchedInventoryItems,
                'message' => 'Inventory data fetched successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateData(Request $request)
    {
        try {
            $requestData = json_decode($request->input('data'), true);

            $result = [];

            foreach ($requestData as $item) {
                $inventoryItemId = $item['id'];
                $suggestedMaxStock = $item['suggested_max_stock'] ?? null;
                $suggestedReorderLevel = $item['suggested_reorder_level'] ?? null;
                $locationId = $item['wa_location_and_stores_id'];

                $inventoryLocationStockStatus = WaInventoryLocationStockStatus::where('wa_location_and_stores_id', $locationId)->where('wa_inventory_item_id', $inventoryItemId)->first();

                if ($inventoryLocationStockStatus) {
                    if (isset($suggestedMaxStock) && $suggestedMaxStock != 0 || $suggestedMaxStock != null) {
                        $inventoryLocationStockStatus->max_stock = $suggestedMaxStock;
                    }
                    if (isset($suggestedReorderLevel) && intval($suggestedReorderLevel) != 0 || intval($suggestedReorderLevel) != null) {
                        $inventoryLocationStockStatus->re_order_level = intval($suggestedReorderLevel);
                    }
                    $inventoryLocationStockStatus->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Inventory data updated. Redirecting...']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function processExcel($request)
    {
        $excelFile = $request->file('file');
        if ($excelFile) {
            $manualFileReader = new Xlsx();
            $manualFileReader->setReadDataOnly(false);
            $manualFile = $excelFile;
            $manualSpreadSheet = $manualFileReader->load($manualFile);
            $manualData = $manualSpreadSheet->getActiveSheet()->toArray();
        }
        return $manualData;
    }


    public function supplierUserManagement(Request $request)
    {

        if (!can('supplier-user-management', 'utility')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'supplier-user-management';
        $this->title = 'Suppliers User Management';
        $this->pmodule = 'supplier-user-management';
        $this->breadcum = [$this->title => route('utility.supplier_user_management'), 'Listing' => ''];

        $start_date = $request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ?  $request->end_date : date('Y-m-d', strtotime('+ 1 days'));
        $userx = $request->username;

        $supplier = WaUserSupplier::join('users', 'wa_user_suppliers.user_id', '=', 'users.id')->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')->select('users.name', 'wa_suppliers.name as suppname')->get()->groupBy('name');


        $supplier->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->whereBetween('wa_user_suppliers.created_at', [$start_date, $end_date]);
        });

        $supplier->when($userx, function ($query) use ($userx) {
            return $query->where('users.name', $userx);
        });

        $suppliers = $supplier;
        $users = User::all();


        if ($request->manage == 'pdf') {

            $pdf = \Pdf::loadView(
                'admin.maintaininvetoryitems.supplier_user_management_pdf',
                [
                    'title' => $this->title,
                    'suppliers' => $suppliers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download($this->title . date('_d_m_Y_H_i_s') . '.pdf');
        }

        return view('admin.utility.supplier_user_management', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'suppliers' => $suppliers,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'users' => $users,
        ]);
    }

    public function supplierUserManagementEdit(Request $request, $userId)
    {
        if (!can('supplier-user-management', 'utility')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'supplier-user-management';
        $this->title = 'Suppliers User Edit';
        $this->pmodule = 'supplier-user-management';
        $this->breadcum = [$this->title => route('utility.supplier_user_management'), 'Listing' => ''];

        $supplier = WaUserSupplier::join('users', 'wa_user_suppliers.user_id', '=', 'users.id')->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
            ->select('users.name', 'wa_suppliers.name as suppname', 'wa_suppliers.id as suppid')->where('wa_user_suppliers.user_id', $userId)->get();

        $suppliers = $supplier;
        $allsuppliers = WaSupplier::get();
        $currentuserId = $userId;
        // dd($username);
        return view('admin.utility.supplier_user_management_edit', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'suppliers' => $suppliers,
            'allsuppliers' => $allsuppliers,
            'currentuserId' => $currentuserId,
            'request' => $request,
        ]);
    }

    public function supplierUserManagementDownload(Request $request, $userId)
    {
        if (!can('supplier-user-management', 'utility')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->title = 'Suppliers';

        $supplier = WaUserSupplier::join('users', 'wa_user_suppliers.user_id', '=', 'users.id')->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
            ->select('users.name', 'wa_suppliers.name as suppname')->where('wa_user_suppliers.user_id', $userId)->get();


        $users = User::find($userId);
        $pdfs = $supplier;

        $pdfs = \Pdf::loadView(
            'admin.utility.supplier_user_management_download',
            [
                'title' => $this->title,
                'pdfs' => $pdfs,
                'users' => $users,

            ]
        );

        return $pdfs->setPaper('a4', 'landscape')
            ->setWarnings(false)
            ->download($this->title . '_for_' . $users->name . '.pdf');
    }

    public function downloadUsersSuppliersDocuments(Request $request)
    {
        try {

            $userId = $request->input('user_id');

            $query = WaUserSupplier::join('users', 'wa_user_suppliers.user_id', '=', 'users.id')
                ->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                ->select('users.name as username', 'wa_suppliers.name as supplier_name');

            if ($userId) {
                $query->where('users.id', $userId);
            }

            $suppliers = $query->get()->groupBy('username');

            if ($request->intent === 'Download Pdf' || $request->intent_name == 'Download Pdf') {
                $pdf = \Pdf::loadView('pdfs.user_suppliers', [
                    'suppliers' => $suppliers,
                ]);

                return $pdf->setPaper('a4', 'portrait')
                    ->setOption('isPhpEnabled', true)
                    ->setWarnings(false)
                    ->download('Users-Suppliers-PDF-' . date('d_m_Y_H_i_s') . '.pdf');
            } elseif ($request->intent === 'Download Excel' || $request->intent_name == 'Download Excel') {
                $data = [];

                foreach ($suppliers as $username => $userSuppliers) {
                    $data[] = ['ID' => '', 'TITLE' => $username];
                    $data[] = ['ID' => '', 'TITLE' => 'Supplier Name'];
                    foreach ($userSuppliers as $index => $supplier) {
                        $data[] = [
                            'ID' => $index + 1,
                            'TITLE' => $supplier->supplier_name
                        ];
                    }
                    $data[] = ['ID' => '', 'TITLE' => ''];
                }

                $filename = "Users-Suppliers-EXCEL-" . date('d_m_Y_H_i_s') . ".xlsx";
                return Excel::download(new UsersSuppliersExport($data), $filename);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }


    public function supplierUserManagementUpdate(Request $request)
    {
        if (!can('supplier-user-management', 'utility')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->title = 'Suppliers Update';
        $supplierId = $request->wa_supplier_id;
        $userId = $request->user_id;

        $existingRelationship = WaUserSupplier::where('user_id', $userId)->where('wa_supplier_id', $supplierId)->exists();

        if ($existingRelationship) {
            return redirect()->back()->with('error', 'This supplier is already attached to this user');
        }

        try {
            $userSupplier = new WaUserSupplier();
            $userSupplier->user_id = $userId;
            $userSupplier->wa_supplier_id = $supplierId;
            $userSupplier->save();

            return redirect()->back()->with('success', 'Supplier attached to user successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
        }
    }

    public function supplierUserManagementDelete(Request $request, $userId, $supplierId)
    {

        if (!can('supplier-user-management', 'utility')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $existingRelationship = WaUserSupplier::where('user_id', $userId)->where('wa_supplier_id', $supplierId)->first();

        if ($existingRelationship) {
            try {
                $existingRelationship->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier detached from user successfully'
                ], 200);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong'
            ], 404);
        }
    }

    public function generateSampleExcel(Request $request)
    {
        $headings = [];
        $filename = '';
        if ($request->action == 'download max stock') {
            $headings = ['ITEM CODE', 'ITEM DESCRIPTION', 'SUGGESTED MAX STOCK'];
            $filename = 'Max-stock-sample';
        } else if ($request->action == 'download reorder level') {
            $headings = ['ITEM CODE', 'ITEM DESCRIPTION', 'SUGGESTED REORDER LEVEL'];
            $filename = 'Reorder-level-sample';
        }
        $excelData = [];
        return ExcelDownloadService::download($filename, collect($excelData), $headings);
    }
}
