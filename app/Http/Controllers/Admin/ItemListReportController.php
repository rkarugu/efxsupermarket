<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use App\Exports\ItemlistDataExport;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class ItemListReportController  extends Controller
{
     protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'items-list-report';
        $this->title = 'Item List Report';
        $this->pmodel = 'items-list-report';
        $this->breadcum = [$this->title => route('reports.items_list_report'), 'Listing' => ''];
    }

    public function index( Request $request)
    {
        if (!can('items-list-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

    $location = $request->store;
    $branch = $request->branch;

   $query = DB::table('wa_inventory_items')->join('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
    ->leftjoin('wa_item_sub_categories', 'wa_inventory_items.item_sub_category_id', '=', 'wa_item_sub_categories.id')   
    ->leftjoin('wa_inventory_location_uom', 'wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
    ->leftjoin('wa_unit_of_measures', 'wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id')
    ->leftjoin('wa_inventory_item_suppliers', 'wa_inventory_item_suppliers.wa_inventory_item_id', '=', 'wa_inventory_items.id')
    ->leftjoin('wa_suppliers', 'wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
    ->leftjoin('wa_user_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
    ->leftjoin('users', 'wa_user_suppliers.user_id', '=', 'users.id')
    ->select('wa_inventory_items.*', 'wa_item_sub_categories.title as subcategory', 'wa_unit_of_measures.title as bin', 'wa_inventory_categories.category_description as category', 'wa_suppliers.name as supplier', DB::raw('GROUP_CONCAT(users.name SEPARATOR \', \') as userMAIN'))
    ->groupBy('stock_id_code');

   

    $query->when($branch , function ($filter) use ($branch) {
        return $filter->where('location_id', $branch);
    });

    $query->when($location , function ($filter) use ($location) {
        return $filter->where('uom_id', $location);
    });


    
    if ($request->manage == 'excel') {

        $itemlistPDF= $query->get();
       
         $view = view('admin.maintaininvetoryitems.items_list_pdf',
            [            
            'title' => $this->title,
            'itemlistPDF' => $itemlistPDF,            
            ]);
            return Excel::download(new ItemlistDataExport($view), $this->title . '.xlsx');
    }else{

       $itemlists= $query->paginate(50);
    }
     
    $branchs=WaLocationAndStore::get();
    $stores =WaUnitOfMeasure::get();

    return view('admin.maintaininvetoryitems.items_list', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $this->breadcum,
            'itemlists' => $itemlists, 
            'stores' => $stores, 
            'branchs' => $branchs,     
        ]);

}
}
