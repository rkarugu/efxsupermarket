<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ItemsWithoutSupplierController extends Controller
{

    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'items-without-suppliers';
        $this->pmodel = 'utility';
        $this->title = 'Items Without Suppliers';
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

        $data = DB::table('wa_inventory_items')
            ->select(
                'wa_inventory_items.title as title',
                'wa_inventory_items.stock_id_code as stock_id_code',
                'wa_inventory_items.description as description',
                'wa_inventory_items.approval_status as approval_status',
                'wa_inventory_categories.category_description as category',
                'pack_sizes.title as pack_size',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                'wa_inventory_items.actual_margin',
                DB::RAW(' (SELECT SUM(qauntity) 
           FROM wa_stock_moves 
           WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code
           ) as item_total_quantity'),
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
            ->where('wa_inventory_items.status', 1)
            ->havingRaw("suppliers IS NULL OR suppliers = ''")
            ->get();

        return view('admin.utility.items_without_suppliers', compact('title', 'model', 'pmodule', 'permission', 'data'));
    }

    public function downloadItemsWithoutSuppliers(Request $request)
    {
        if (!can('view', $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $data = DB::table('wa_inventory_items')
            ->select(
                'wa_inventory_items.title as title',
                'wa_inventory_items.stock_id_code as stock_id_code',
                'wa_inventory_items.description as description',
                'wa_inventory_items.approval_status as approval_status',
                'wa_inventory_categories.category_description as category',
                'pack_sizes.title as pack_size',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                'wa_inventory_items.actual_margin',
                DB::RAW(' (SELECT SUM(qauntity) 
           FROM wa_stock_moves 
           WHERE wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code
           ) as item_total_quantity'),
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
            ->where('wa_inventory_items.status', 1)
            ->havingRaw("suppliers IS NULL OR suppliers = ''")
            ->get();

        if ($request->has('intent')) {
            $action = $request->input('intent');

            $filename = 'Items-Without-Suppliers.' . ($action === 'pdf' ? 'pdf' : 'xlsx');
            $fileContent = null;

            if ($action === 'pdf') {
                $pdf = \Pdf::loadView('admin.utility.items_without_suppliers_pdf', [
                    'data' => $data,
                ]);
                return $pdf->setPaper('a4', 'landscape')
                    ->setOption('isPhpEnabled', true)
                    ->setWarnings(false)
                    ->download('ITEMS-WITHOUT-SUPPLIERS-PDF-' . '.pdf');
            } else if ($action === 'excel') {
                $data = $data->collect();
                $excelData = [];
                foreach ($data as $row) {
                    $data = [
                        'item_code' => $row->stock_id_code ?? '',
                        'description' => $row->description ?? '',
                        'category' => $row->category ?? '',
                        'pack_size' => $row->pack_size ?? '',
                        'standard_cost' => $row->standard_cost ?? '',
                        'selling_price' => $row->selling_price ?? '',
                        'margin_percentage' => number_format(
                            $row->standard_cost != 0
                                ? (($row->selling_price - $row->standard_cost) / $row->standard_cost) * 100
                                : 0,
                            2
                        ),
                        'quantity_on_hand' => $row->item_total_quantity ?? 0,
                        'tax_category' => $row->tax_manager ?? '',
                    ];
                    $excelData[] = $data;
                }
                $headings = ['ITEM CODE', 'ITEM DESCRIPTION', 'CATEGORY', 'PACK SIZE', 'STANDARD COST', 'SELLING PRICE', '% MARGIN', 'QOH', 'TAX CATEGORY'];

                $filename = "Selling-Price-List";
                return ExcelDownloadService::download($filename, collect($excelData), $headings);
            }

            return response($fileContent, 200)
                ->header('Content-Type', $action === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        return view('admin.utility.items_without_suppliers', compact('title', 'model', 'pmodule', 'permission', 'data'));
    }
}
