<?php

namespace App\Http\Controllers\Admin\InventoryItem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Interfaces\Inventory\ApprovalItemInterface;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Services\ExcelDownloadService;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class RetireItemController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    private ApprovalItemInterface $approvalRepository;

    public function __construct(Request $request, ApprovalItemInterface $approvalRepository)
    {
        $this->model = 'maintain-items';
        $this->title = 'Maintain items';
        $this->pmodule = 'maintain-items';
        $this->approvalRepository = $approvalRepository;
    }

    public function retired_items()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'retired-items';
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($this->model . '.index'), 'Listing' => ''];
            return view('admin.maintaininvetoryitems.retired_items.retired_items', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function downloadInvetoryRetiredItems(Request $request)
    {
        try {
            $data_query = DB::table('wa_inventory_items')
                ->select(
                    'wa_inventory_items.title as title',
                    'wa_inventory_items.stock_id_code as stock_id_code',
                    'wa_inventory_categories.category_description as category',
                    'pack_sizes.title as pack_size',
                    'wa_inventory_items.price_list_cost',
                    'wa_inventory_items.last_grn_cost',
                    'wa_inventory_items.weighted_average_cost',
                    'wa_inventory_items.standard_cost',
                    'wa_inventory_items.selling_price',
                    'wa_inventory_items.actual_margin',
                    DB::RAW(' (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code'
                        . ($request->branch ? ' AND wa_stock_moves.wa_location_and_store_id = ' . (int)$request->branch : '')
                        . ') as item_total_quantity'),
                    'tax_managers.title as tax_manager',
                    'wa_inventory_items.image',
                    DB::RAW("(SELECT GROUP_CONCAT(wa_suppliers.name SEPARATOR ', ') 
                        FROM wa_inventory_item_suppliers 
                        LEFT JOIN wa_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_suppliers.id 
                        WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                        ) AS suppliers"),
                    DB::RAW("(SELECT GROUP_CONCAT(users.name SEPARATOR ', ')
                        FROM wa_inventory_item_suppliers
                        LEFT JOIN wa_user_suppliers ON wa_inventory_item_suppliers.wa_supplier_id = wa_user_suppliers.wa_supplier_id
                        LEFT JOIN users ON  wa_user_suppliers.user_id = users.id
                        WHERE wa_inventory_item_suppliers.wa_inventory_item_id = wa_inventory_items.id
                        ) AS users"),

                )
                ->leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', 'wa_inventory_categories.id')
                ->leftJoin('pack_sizes', 'pack_sizes.id', 'wa_inventory_items.pack_size_id')
                ->leftJoin('tax_managers', 'wa_inventory_items.tax_manager_id', 'tax_managers.id')
                ->where('wa_inventory_items.status', 0);
            $data_query = $data_query->get();
            if (!empty($data_query)) {
                foreach ($data_query as $row) {
                    $arrays[] = [
                        'Stock Id Code' => (string)($row->stock_id_code),
                        'Title' => $row->title,
                        'Item Category' => $row->category ?? '',
                        'Pack Size' => (string)($row->pack_size ?? ''),
                        'Price List Cost' => manageAmountFormat($row->price_list_cost) ?? '',
                        'Last GRN Cost' => manageAmountFormat($row->last_grn_cost) ?? '',
                        'Weighted Average Cost' => manageAmountFormat($row->weighted_average_cost) ?? '',
                        'Standard Cost' => (string)$row->standard_cost,
                        'Selling Price' => (string)$row->selling_price,
                    ];
                }
            }
            $headers = [
                'STOCK ID CODE',
                'TITLE',
                'CATEGORY',
                'PACK SIZE',
                'PRICE LIST COST',
                'LAST GRN COST',
                'WEIGHTED COST',
                'STANDARD COST',
                'SELLING PRICE',
            ];
            return ExcelDownloadService::download('inventory-retired-items-' . date('Y-m-d-H-i-s'), collect($arrays), $headers);
        } catch (\Exception $th) {
            $request->session()->flash('danger', $th->getMessage());
            return redirect()->back();
        }
    }

    public function batch_retire_items()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'batch-retire-items';
        $breadcum = [$title => route($this->model . '.index'), 'Batch Retire Items' => ''];

        $processingUpload = false;
        $errorLogger = false;
        return view('admin.maintaininvetoryitems.retired_items.batch_retire_items', compact('title', 'model', 'breadcum', 'processingUpload', 'errorLogger'));
    }

    public function batch_retire_items_upload(Request $request)
    {

        try {
            ini_set('memory_limit', '512M');
            set_time_limit(0);

            if ($request->has('intent') && $request->intent == 'Template') {

                $data = [];

                $headings = ['Stock ID Code', 'Title'];
                $filename = "Batch-Retire-Template";
                $excelData = $data;
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            } else {
                $reader = new Xlsx();
                $reader->setReadDataOnly(false);
                $fileName = $request->file('cleanup_list');
                $spreadsheet = $reader->load($fileName);
                $data = $spreadsheet->getActiveSheet()->toArray();

                $items = [];
                $itemsAll = DB::table('wa_inventory_items')
                    ->select(
                        'wa_inventory_items.*',
                        'wa_inventory_categories.category_description as category',
                    )
                    ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'wa_inventory_items.wa_inventory_category_id')
                    ->get();
                foreach ($data as $index => $record) {
                    if ($index != 0) {
                        $item = $itemsAll->where('stock_id_code', $record[0])->first();
                        if (!$item) {
                            Session::flash('warning', "An item matching Stock Id No $record[0] was not found");
                            return redirect()->back();
                        }

                        $suppliers = DB::table('wa_inventory_item_suppliers')->where('wa_inventory_item_id', $item->id)
                            ->leftJoin('wa_suppliers', 'wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                            ->select('wa_suppliers.id', 'wa_suppliers.name')
                            ->get();

                        $qoh = DB::table('wa_stock_moves')->where('wa_inventory_item_id', $item->id)->sum('qauntity');
                        $items[] = [
                            'id' => $item->id,
                            'stock_id_code' => $item->stock_id_code,
                            'title' => $item->title,
                            'category' => $item->category,
                            'selling_price' => $item->selling_price,
                            'suppliers' => implode(',', $suppliers->pluck('name')->toArray()),
                            'qoh' => $qoh
                        ];
                    }
                }

                $model = 'batch-retire-items';
                $title = 'Batch Retire Items';
                $breadcum = [$title => route($this->model . '.index'), 'Suspend' => ''];
                $processingUpload = true;
                $errorLogger = false;
                return view('admin.maintaininvetoryitems.retired_items.batch_retire_items', compact('title', 'model', 'breadcum', 'processingUpload', 'items', 'errorLogger'));
            }
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function batch_retire_items_store(Request $request)
    {
        DB::beginTransaction();
        try {
            $records = json_decode($request->records, true);
            $user = getLoggeduserProfile();
            $stockIdCodes = [];
            $failedItems = [];
            foreach ($records as $record) {
                $item = DB::table('wa_inventory_items')
                    ->select(
                        'wa_inventory_items.*',
                        'wa_inventory_categories.category_description as category',
                    )
                    ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'wa_inventory_items.wa_inventory_category_id')
                    ->where('wa_inventory_items.id', $record['id'])->first();
                $qoh = DB::table('wa_stock_moves')->where('wa_inventory_item_id', $item->id)->sum('qauntity');
                if ($qoh) {
                    $failedItems[] = [
                        'id' => $item->id,
                        'stock_id_code' => $item->stock_id_code,
                        'title' => $item->title,
                        'category' => $item->category,
                        'selling_price' => $item->selling_price,
                        'qoh' => $qoh
                    ];
                } else {
                    DB::table('wa_inventory_items')->where('id', $record['id'])->update(['status' => 0]);
                    $stockIdCodes[] = $record['stock_id_code'];
                }
            }

            $deletedTrans = implode(',', $stockIdCodes);
            UserLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'module' => 'reconciliation',
                'activity' => "Batch Retire Items $deletedTrans",
                'entity_id' => $user->id,
                'user_agent' => 'Bizwiz WEB',
            ]);

            DB::commit();
            Session::flash('success', 'Batch Items Retire successfully');
            Session::flash('errorItems', $failedItems);
            return  redirect()->route('admin.utility.batch.retire.items');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
}
