<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ExcelDownloadService;

class SupplierListingController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'supplier-listing';
        $this->title = 'Supplier Listing';
        $this->pmodule = 'supplier-listing';

        set_time_limit(30000000);
    }


    public function index(Request $request)
    {

        // revert and update permissions
        
        if (!can('view', 'supplier-listing')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $breadcum = ['Accounts Payables' => '', 'Report' => '', $title => ''];

        if ($request->wantsJson() || $request->filled('action')) {
            $sub = WaSuppTran::query()
                ->selectRaw('supplier_no, SUM(total_amount_inc_vat) as supplier_balance')
                ->groupBy('supplier_no');

            $query = WaSupplier::query()
                ->with('paymentTerm')
                ->select('wa_suppliers.*', DB::Raw('IFNULL(balances.supplier_balance,0.00) as supplier_balance'))
                ->leftJoinSub($sub, 'balances', 'balances.supplier_no', '=', 'wa_suppliers.supplier_code')
                ->whereNotNull('supplier_code');

            if ($request->balance == 'zero') {
                $query->where('supplier_balance', '=', 0)->orWhereNull('supplier_balance');
            } else if ($request->balance == 'less') {
                $query->where('supplier_balance', '<',  "0.00");
            } else if ($request->balance == 'more') {
                $query->where('supplier_balance', '>',  "0.00");
            }
            $query->orderByDesc('supplier_balance');

            if ($request->filled('action')) {
                $report_name = 'supplier_listing_' . date('Y_m_d_H_i_A');
                if ($request->action=='pdf') {
                    $pdf = Pdf::loadView('admin.supplier_listing.pdf', [
                        'suppliers' => $query->get()
                    ]);
                    $pdf->setPaper('A4', 'landscape');
                    return $pdf->download($report_name . '.pdf');
                }
                if($request->action == 'excel'){
                    $suppliers = $query->get();
                    $export_array = [];
                    foreach($suppliers as $supplier){
                        $export_array[]=[
                            $supplier->supplier_code,
                            $supplier->name,
                            $supplier->address,
                            $supplier->telephone,
                            $supplier->email,
                            $supplier->supplier_since,
                            $supplier->paymentTerm?->term_description,
                            manageAmountFormat($supplier->supplier_balance)
                        ];
                    }
                    $export_array[]=[
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'Grand Total',
                        manageAmountFormat($suppliers->sum('supplier_balance'))
                    ];
                    return ExcelDownloadService::download($report_name, collect($export_array), ['SUPPLIER CODE','SUPPLIER NAME','ADDRESS','TELEPHONE','EMAIL','SUPPLIER SINCE','PAYMENT TERMS','TOTAL BALANCE']);
                    
                }
                
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('supplier_balance', function ($supplier) {
                    return manageAmountFormat($supplier->supplier_balance);
                })
                ->with('total', function () use ($query) {
                    return $query->sum('supplier_balance');
                })
                ->toJson();
        }

        return view('admin.supplier_listing.index', compact('title', 'model', 'breadcum'));
    }
}
