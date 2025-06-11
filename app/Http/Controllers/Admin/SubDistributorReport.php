<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaSupplierDistributor;
use App\Exports\ItemlistDataExport;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class SubDistributorReport  extends Controller
{
     protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'sub-distributor-suppliers-report';
        $this->title = 'Sub Distributor Suppliers Report';
        $this->pmodel = 'sub-distributor-suppliers-report';
        $this->breadcum = [$this->title => route('reports.items_list_report'), 'Listing' => ''];
    }

    public function index( Request $request)
    {
        if (!can('sub-distributor-suppliers-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

    $location = $request->store;
    $branch = $request->branch;  

   $query = DB::table('wa_suppliers')
    ->join('wa_supplier_distributors', 'wa_suppliers.supplier_code', '=', 'wa_supplier_distributors.supplier_id') 
    ->join('wa_suppliers as sub', 'wa_supplier_distributors.distributors', '=', 'sub.id')
    ->where('wa_supplier_distributors.status', '1') 
    ->select('wa_suppliers.name as mainsupplier', 'sub.name as subsupplier')    
    ->get(); 
    
    $suppliers = $query->groupBy('mainsupplier');
    
    if ($request->manage == 'excel') {
       
         $view = view('admin.maintaininvetoryitems.sub_distributor_suppliers_report_pdf',
            [            
            'title' => $this->title,
            'suppliers' => $suppliers,            
            ]);
            return Excel::download(new ItemlistDataExport($view), $this->title . '.xlsx');
    }

    return view('admin.maintaininvetoryitems.sub_distributor_suppliers_report', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $this->breadcum,
            'suppliers' => $suppliers,     
        ]);

}


public function destroy($keyId)
{
    
    $subsupplier = WaSupplierDistributor::where('id', $keyId)->first();
    if (!$subsupplier) {
        return redirect()->back()->with('error', 'Sub Distributor not found.');
    }
    $subsupplier->delete();
    return redirect()->back()->with('success', 'Sub Distributor Removed Successfully.');
}

}
