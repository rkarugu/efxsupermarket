<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Exports\CommonReportDataExport;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class NoSupplierItemsReportController  extends Controller
{
     protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'no-supplier-items-report';
        $this->title = 'No Supplier Items Report';
        $this->pmodel = 'no-supplier-items-report';
        $this->breadcum = [$this->title => route('reports.no_supplier_items_report'), 'Listing' => ''];
    }

    public function index( Request $request)
    {
        if (!can('no-supplier-items-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }


    $branch = $request->branch;

    $lastPurchaseSUb = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('Max(created_at) as last_purchase')
            ])
            ->where('document_no', 'like', 'GRN-%')
            ->groupBy('stock_id_code');

    $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->groupBy('stock_id_code');

    $query = DB::table('wa_inventory_items')->join('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
    ->leftjoin('wa_item_sub_categories', 'wa_inventory_items.item_sub_category_id', '=', 'wa_item_sub_categories.id') 
    ->join('wa_inventory_item_suppliers', 'wa_inventory_item_suppliers.wa_inventory_item_id', '=', 'wa_inventory_items.id')
    ->leftjoin('wa_suppliers', 'wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
    ->leftJoinSub($lastPurchaseSUb, 'last_purchase', 'last_purchase.wa_inventory_item_id', '=', 'wa_inventory_items.id')
    ->leftJoinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
    ->leftjoin('wa_inventory_location_uom','wa_inventory_location_uom.inventory_id' ,'=','wa_inventory_items.id')    
    ->select('wa_inventory_items.*', 'wa_item_sub_categories.title as subcategory', 'wa_inventory_categories.category_description as category','last_purchase.last_purchase as last_purchase','wa_suppliers.name as sup', DB::raw('IFNULL(qoh.quantity, 0) as qoh'),)
    ->groupBy('stock_id_code');
   

    $query->when($branch , function ($filter) use ($branch) {
        return $filter->where('wa_inventory_location_uom.location_id', $branch);
    });

    $itemlists= $query->get();

    if ($request->manage == 'excel') {
       
         $view = view('admin.maintaininvetoryitems.no_items_suppliers_pdf',
            [            
            'title' => $this->title,
            'itemlists' => $itemlists,            
            ]);
            return Excel::download(new CommonReportDataExport($view), $this->title . '.xlsx');
    }else{

    $branches=WaLocationAndStore::get();

    return view('admin.maintaininvetoryitems.no_items_suppliers', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $this->breadcum,
            'itemlists' => $itemlists,
            'branches' => $branches,     
        ]);

}
}
}
