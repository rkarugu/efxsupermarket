<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\User;
use App\Model\WaGrn;

use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Model\TaxManager;
use App\Exports\SupplierVatReportExport;
use App\Model\WaLocationAndStore;
use App\WaSupplierInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class VatReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'vat-report';
        $this->title = 'VAT Report';
        $this->pmodule = 'vat-report';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }


    public function index(Request $request)
    {
        //$this->managetimeForallCron();
        //dd('here');
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $start_date =$request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date =$request->end_date ?  $request->end_date : date('Y-m-d');


        $detail = [];

            $customer = WaSuppTran::           
              join('wa_suppliers','wa_supp_trans.supplier_no', '=', 'wa_suppliers.supplier_code')
            ->join('wa_grns', 'wa_supp_trans.cu_invoice_number','=', 'wa_grns.cu_invoice_number')
            ->join('wa_purchase_order_items','wa_grns.wa_purchase_order_item_id', '=','wa_purchase_order_items.id' )
            ->join('wa_inventory_items','wa_purchase_order_items.wa_inventory_item_id', '=','wa_inventory_items.id' )
            ->leftJoin('financial_notes', 'wa_supp_trans.id','=','financial_notes.wa_supp_tran_id')
             ->leftJoin('financial_notes as finance', 'wa_supp_trans.document_no','=','financial_notes.note_no')
             ->select('wa_supp_trans.id','wa_suppliers.kra_pin', 'wa_suppliers.name', 'wa_supp_trans.trans_date AS TRD','wa_supp_trans.cu_invoice_number as TCU','wa_supp_trans.total_amount_inc_vat','wa_grns.invoice_info','wa_inventory_items.tax_manager_id','wa_grns.qty_received','financial_notes.note_date','financial_notes.cu_invoice_number as FnCu', 'financial_notes.amount as FnAm', 'financial_notes.tax_amount as FnTaAm','wa_supp_trans.total_amount_inc_vat','wa_supp_trans.vat_amount'  )
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) 
            {
           
           return $query->whereBetween('wa_supp_trans.created_at', 
            [$start_date, $end_date]);
            })
             ->groupBy('wa_supp_trans.cu_invoice_number', 'wa_inventory_items.tax_manager_id');

        
            if (!empty($request->get('tax_manager_id')) && $request->get('tax_manager_id') == "1") {
            	$customer->where('wa_inventory_items.tax_manager_id', '1');
			}
			if (!empty($request->get('tax_manager_id')) && $request->get('tax_manager_id') == "2") {
                $customer->where('wa_inventory_items.tax_manager_id','2');
			}

            if (!empty($request->get('tax_manager_id')) && $request->get('tax_manager_id') == "3") {
                $customer->where('wa_inventory_items.tax_manager_id','3');
            }

        $customer = $customer->get();
  
        $restroList = $this->getRestaurantList();


        $breadcum = ['Accounts Payables' => '', 'Report' => '', $title => ''];
        return view('admin.vat_report.sheet', compact('title', 'customer', 'restroList',  'model', 'breadcum', 'detail','start_date','end_date'));
    }



     public function  vatreport()
    {
        if (!can('view', 'suppliers-invoice')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        /*$query=DB::table('wa_grns')
        ->join('wa_suppliers','wa_grns.wa_supplier_id', '!=', 'wa_suppliers.id')
        ->get();
*/
        /*dd($query);*/
/*
        $query = WaGlTran::query()
            ->select('wa_supplier_invoices.*')
            ->with([
                'supplier:id,name,kra_pin',
                'user:id,name',
                'suppTrans'
            ]);*/

            // echo "<pre>";print_r($query);exit();

            // dd($query);

        if (request()->filled('from') && request()->filled('to')) {
            $start = request()->from . " 00:00:00";
            $end = request()->to . " 23:59:59";

            $description .= " Dates: $start - $end";

            $query->whereBetween('wa_supplier_invoices.created_at', [$start, $end]);
        }

        if (request()->filled('supplier')) {
            $description .= ' Supplier: ' . WaSupplier::find(request()->supplier)->name;

            $query->where('supplier_id', request()->supplier);
        }

          if (request()->filled('tax')) {
            $description .= ' Tax: ' . TaxManager::find(request()->tax)->title;

            $query->where('tax_id', request()->tax);
        }

        if (request()->action == 'pdfvat') {
            $pdf = Pdf::loadView('admin.vat_report.print', [
                'invoices' => $query->get(),
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('VAT_reports_' . date('Y-m-d-H-i-s') . '.pdf');
        }

        $sumQuery = $query;
        $sumTotal = $sumQuery->sum('amount');
        if (request()->action == 'excelvat') {
            $data = $query->get();
            $excelData = [];
            foreach ($data as $invoice) {
                $payload = [
                    'supplier-pin' => $invoice->supplier->kra_pin,
                   'invoice-number' => $invoice->invoice_number,
                    'supplier-name' => $invoice->supplier->name,
                    'supplier-invoice' => $invoice->supplier_invoice_number,
                    'sup-invoice-date' => $invoice->supplier_invoice_date,
                    'cu-invoice-number' => $invoice->cu_invoice_number,
                    'vat' => manageAmountFormat($invoice->vat_amount),
                    'amount' => manageAmountFormat($invoice->amount),
                ];
                $excelData[] = $payload;
            }
            $excel = new SupplierVatReportExport(collect($excelData));
            $today = \Carbon\Carbon::now()->toDateString();
            return Excel::download($excel, "VAT_Reports_$today.xlsx");
        }


        if (request()->wantsJson()) {

            return DataTables::eloquent($query)
                ->addIndexColumn()
               
                ->editColumn('supplier_invoice_date', function ($invoice) {
                    return $invoice->supplier_invoice_date?->format('Y-m-d');
                })
                ->editColumn('created_at', function ($invoice) {
                    return $invoice->created_at->format('Y-m-d');
                })
                ->editColumn('vat_amount', function ($invoice) {
                    return manageAmountFormat($invoice->vat_amount);
                })
                ->editColumn('amount', function ($invoice) {
                    return manageAmountFormat($invoice->amount);
                })
                ->toJson();
        }


        return view('admin.vat_report.index', [
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::all(),
            'taxes' => TaxManager::all(),
            'title' => $this->title . " - V Invoices",
            'model' => $this->model,
            'sumTotal' => $sumTotal
        ]);
    }

  






}
