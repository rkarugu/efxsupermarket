<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChildVsMotherExport; 
use App\Exports\CommonReportDataExport;
use App\Model\WaInventoryAssignedItems;
use Illuminate\Support\Facades\Session;
use App\Exports\MissingSplitrDataExport; 




class ChildVsMotherQoh extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'maintain-items';
        $this->title = 'Maintain items';
        $this->pmodule = 'inventory-reports';
    }
    public function  generate( Request  $request)
    {
        $model = 'child-vs-mother-qoh-report';
        $title = $this->title;
        $pmodule = $this->pmodule;
       
        $inventoryItems = DB::select("
        SELECT 
            wa_inventory_assigned_items.*,
            (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_assigned_items.destination_item_id) AS quantity,
            (SELECT SUM(qauntity) FROM wa_stock_moves WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_assigned_items.wa_inventory_item_id) AS parent_quantity,
            parent_item.stock_id_code AS parent_stock_id,
            parent_item.title AS parent_title,
            parent_item.selling_price AS parent_selling_price,
            parent_item.pack_size_id AS parent_pack_size,
            parent_pack_size.title AS parent_pack_title,
            child_item.stock_id_code AS child_stock_id,
            child_item.title AS child_title,
            child_item.selling_price AS child_selling_price,
            child_item.pack_size_id AS child_pack_size,
            child_pack_size.title AS child_pack_title

        FROM 
            wa_inventory_assigned_items
        LEFT JOIN 
            wa_inventory_items parent_item ON wa_inventory_assigned_items.wa_inventory_item_id = parent_item.id
        LEFT  JOIN 
            pack_sizes parent_pack_size ON parent_item.pack_size_id = parent_pack_size.id
        LEFT JOIN 
            wa_inventory_items child_item ON wa_inventory_assigned_items.destination_item_id = child_item.id
        LEFT  JOIN 
            pack_sizes child_pack_size ON child_item.pack_size_id = child_pack_size.id
    ");

        return view('admin.child_vs_mother_qoh.generate', compact('inventoryItems','title', 'model', 'pmodule'));
    }

    public function downloadChildVsMotherQoh()
    {
        try {
            $data_query = WaInventoryAssignedItems::select(
                'wa_inventory_assigned_items.*',
                DB::RAW(' (select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id=wa_inventory_assigned_items.destination_item_id) as quantity'),
                DB::RAW(' (select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id=wa_inventory_assigned_items.wa_inventory_item_id) as parent_quantity'),)->get();
            $arrays = [];
            if (!empty($data_query)) {
                foreach ($data_query as $key => $row) {
                    $arrays[] = [
                        'mother-stock-id-code' => $row->parent_item?->stock_id_code,
                        'mother-title' => $row->parent_item?->title,
                        'mother-pack-size' => $row->parent_item?->pack_size?->title,
                        'mother-selling-price' => manageAmountFormat($row->parent_item?->selling_price),
                        'motherqoh' => $row->parent_quantity,
                        'child-stock-id-code' => $row->destinated_item?->stock_id_code,
                        'title' => $row->destinated_item?->title,
                        'pack-size' => $row->destinated_item?->pack_size?->title,
                        'selling-price' => manageAmountFormat($row->destinated_item?->selling_price),
                        'qoh' => $row->quantity,
                        'conversion-factor' => $row->conversion_factor,
                    ];

                }
            }

            $export = new ChildVsMotherExport($arrays);
            return Excel::download($export, 'childVsMotherQoh' . date('Y-m-d-H-i-s') . '.xls');
        } catch (\Exception $th) {
            Session::flash('danger', $th->getMessage());
            return redirect()->back();
        }
    }


    public function  missingSplit( Request  $request)
    {
        
    if (!can('missing-split-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $model = 'missing-split-report';
        $title = 'Missing Split Report';
        $pmodule = 'missing-split-report';
        $breadcum = [$this->title => route('report.missingsplit-report'), 'Listing' => ''];
       
        $user = $request->user();

        $branchId = $request->branch ?? WaLocationAndStore::where('wa_branch_id', $user->restaurant_id)->first()?->id;

        $branches = WaLocationAndStore::query();
        if ($user->role_id == 1) {
            $branches = $branches->get();
        } else {
            $branches = $branches->whereIn('wa_branch_id', $user->branches()->pluck('restaurant_id')->push($user->restaurant_id))->get();
        }

        $branchIds = $branches->pluck('id');
        
        $quantitySubQuery = "";
        $parentQuantitySubQuery = "";
        
        $quantitySubQuery = "SELECT SUM(qauntity) 
            FROM wa_stock_moves 
            WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_assigned_items.destination_item_id 
            AND wa_stock_moves.wa_location_and_store_id = $branchId
        ";

        $parentQuantitySubQuery = "SELECT SUM(qauntity) 
            FROM wa_stock_moves 
            WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_assigned_items.wa_inventory_item_id
            AND wa_stock_moves.wa_location_and_store_id = $branchId
        ";
        
        $inventoryItems = DB::table('wa_inventory_assigned_items')
            ->select([
                'wa_inventory_assigned_items.*',
                DB::raw("($quantitySubQuery) AS quantity"),
                DB::raw("($parentQuantitySubQuery) AS parent_quantity"),
                'parent_item.stock_id_code AS parent_stock_id',
                'parent_item.title AS parent_title',
                'parent_item.selling_price AS parent_selling_price',
                'parent_item.pack_size_id AS parent_pack_size',
                'parent_pack_size.title AS parent_pack_title',
                'child_item.stock_id_code AS child_stock_id',
                'child_item.title AS child_title',
                'child_item.selling_price AS child_selling_price',
                'child_item.pack_size_id AS child_pack_size',
                'child_pack_size.title AS child_pack_title'
            ])
            ->leftJoin('wa_inventory_items AS parent_item', 'wa_inventory_assigned_items.wa_inventory_item_id', '=', 'parent_item.id')
            ->leftJoin('pack_sizes AS parent_pack_size', 'parent_item.pack_size_id', '=', 'parent_pack_size.id')
            ->leftJoin('wa_inventory_items AS child_item', 'wa_inventory_assigned_items.destination_item_id', '=', 'child_item.id')
            ->leftJoin('pack_sizes AS child_pack_size', 'child_item.pack_size_id', '=', 'child_pack_size.id')
            ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', '=', 'wa_inventory_assigned_items.wa_inventory_item_id')
            ->whereRaw("($parentQuantitySubQuery) > 0")
            ->whereRaw("($quantitySubQuery) = 0")
            ->where('wa_inventory_location_uom.location_id', $branchId)
            ->get();
        

    if ($request->manage == 'excel') {
            $view = view('admin.child_vs_mother_qoh.missingsplit_excel',
            [            
            'title' => $title,
            'inventoryItems' => $inventoryItems
            
            ]);
            return Excel::download(new MissingSplitrDataExport($view), $title . '.xlsx');
    }
    
        return view('admin.child_vs_mother_qoh.missingsplit', compact('inventoryItems','title', 'model', 'pmodule', 'branches', 'branchId'));
    }




    public function  motherNoChildReport( Request  $request)
    {
        $model = 'CTN-without-children';
        $title = 'CTN without Children';
        $pmodule = 'CTN-without-children';

    $inventoryItems = WaInventoryItem::leftJoin('wa_inventory_assigned_items', 'wa_inventory_items.id', '=', 'wa_inventory_assigned_items.wa_inventory_item_id')
    ->Join('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id')
    ->join('pack_sizes', 'wa_inventory_items.pack_size_id','=','pack_sizes.id')
    ->whereNull('wa_inventory_assigned_items.wa_inventory_item_id')
    ->select('pack_sizes.title as pack', 'wa_inventory_items.selling_price as parent_selling_price','wa_inventory_items.title','wa_inventory_items.stock_id_code','wa_inventory_items.id', DB::raw('SUM(wa_stock_moves.qauntity) AS parent_quantity'))
    ->where('wa_inventory_items.pack_size_id','3')
    ->where('wa_inventory_items.status', 1)
    ->groupBy('wa_inventory_items.id')
    ->get();

    if ($request->manage == 'excel') {
            $view = view('admin.child_vs_mother_qoh.nochildreport_excel',
            [
               'inventoryItems' => $inventoryItems,
            ]);
            return Excel::download(new CommonReportDataExport($view), 'CTN without Children Report' . '.xlsx');
           }



        return view('admin.child_vs_mother_qoh.nochildreport', compact('inventoryItems','title', 'model', 'pmodule'));
    }


}