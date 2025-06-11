<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LpoLeadTime;
use App\Http\Controllers\Controller;
use App\Model\WaGrn;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use App\WaSupplierInvoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class LpoStatusAndLeadtimeReportController extends Controller
{
    protected $pmodel;
    protected $model;
    protected $base_title;

    public function __construct()
    {
        $this->pmodel = 'purchases-reports';
        $this->model = 'lpo-status-and-leadtime-report';
        $this->base_title = 'Purchases Report';
    }
    public function  index()
    {
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission[$this->model . '___lpo-status-and-leadtime']) && $permission != 'superadmin') {

            Session::flash('warning', 'You do not have permission to access this report');

            return redirect()->back();
        }

        if (request()->wantsJson() || request()->action == 'pdf') {
            $description = '';

            $query = WaSupplierInvoice::query()
                ->select('wa_supplier_invoices.*')
                ->with([
                    'supplier:id,name',
                    'lpo:id,purchase_no,created_at',
                    'user:id,name'
                ])
                ->whereHas('lpo', function ($query) use (&$description) {
                    if (request()->filled('location')) {
                        $description .= 'Location: ' . WaLocationAndStore::find(request()->location)->location_name;
                        $query->where('wa_location_and_store_id', request()->location);
                    }
                });

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

            if (request()->action == 'pdf') {
                $pdf = Pdf::loadView('admin.maintainsuppliers.processed_invoices.print', [
                    'invoices' => $query->get(),
                    'description' => $description,
                ])->setPaper('a4', 'landscape');

                return $pdf->stream('processed_invoces_' . date('Y-m-d-H-i-s') . '.pdf');
            }
            return response()->json($query->get());



            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('lpo.created_at', function ($invoice) {
                    return $invoice->lpo->created_at->format('Y-m-d');
                })
                ->editColumn('grn_date', function ($invoice) {
                    return $invoice->grn_date->format('Y-m-d');
                })
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
                ->addColumn('action', function ($invoice) {
                    return '<a target="_blanck" href="' . route('maintain-suppliers.processed_invoices.show', $invoice->id) . '"><i class="fa fa-file-alt"></i></a>';
                })
                ->toJson();
        }


        return view('admin.purchase_reports.purchases_status_and_lead_time_report', [
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::all(),
            'title' => $this->base_title . " - Processed Invoices",
            'model' => $this->model
        ]);
    }
    public function newIndex(Request $request)
    {   
        $query = WaGrn::orderBY('created_at', 'desc');
        if($request->from && $request->to){
            $start = $request->from. " 00:00:00";
            $end = $request->to. " 23:59:59";
            // $query->whereBetween('created_at', [$start, $end]);
            $query->whereHas('purchaseOrder', function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            });
        }
        if($request->supplier){
            $supplier = $request->supplier;
            $query->whereHas('purchaseOrder', function ($query) use ($supplier) {
                $query->where('wa_supplier_id', $supplier);
            });
        }
        $query = $query->get()->groupBy('grn_number');
        $query = $query->each(function ($groupedRecords, $grn_number) {
            $grnTotal = 0;
            foreach ($groupedRecords as $key => $grnEntry) {
                $invoiceInfo = json_decode($grnEntry->invoice_info);
                $grnTotal += ((float)$invoiceInfo->order_price * (float)$invoiceInfo->qty);
            }
            $groupedRecords->grnTotal = $grnTotal;
            $groupedRecords->grn_number = $groupedRecords[0]->grn_number; 
            $groupedRecords->grn_date = $groupedRecords[0]->created_at; 
           $groupedRecords->lpo = WaPurchaseOrder::find($groupedRecords[0]->wa_purchase_order_id); 
           $groupedRecords->supplierInvoice = WaSupplierInvoice::where('grn_number', $groupedRecords[0]->grn_number)->get();

           $groupedRecords->lpoTotal = $groupedRecords->lpo->getRelatedItem->sum('total_cost_with_vat');
        });
        $data = $query;
        if($request->action == 'download'){
                foreach ($data as $key => $row) {
                    $arrays[] = [
                        'lpo-date' => \Carbon\Carbon::parse($row->lpo->created_at)->toDateString(),
                        'Lpo-no.' => $row->lpo->purchase_no,
                        'lpo-user' => $row->lpo->getrelatedEmployee?->name,
                        'grn-no.' => $row->grn_number,
                        'grn-date' => \Carbon\Carbon::parse($row->grn_date)->toDateString(),
                        'grn-user' => $row->lpo->getrelatedEmployee?->name,
                        'invoice-no.' => $row->supplierInvoice[0]->invoice_number ?? '-',
                        'supplier' => $row->lpo->supplier?->name,
                        'supplier-invoice-number' => $row->supplierInvoice[0]->supplier_invoice_number ?? '-',
                        'supplier-invoice-date' => $row->supplierInvoice[0]->supplier_invoice_date ?? '-',
                        'cu-invoice-no' => $row->supplierInvoice[0]->cu_invoice_number ?? '-',
                        'invoice-user' => $row->supplierInvoice[0]->user->name ?? '-',
                        'lpo-total' => manageAmountFormat($row->lpoTotal),
                        'grn-total' => manageAmountFormat($row->grnTotal),
                        'invoice-amount' => manageAmountFormat($row->supplierInvoice[0]->amount ?? 0),
                    ];

                }
            

            $export = new LpoLeadTime(collect($arrays));
            return Excel::download($export, 'lpoLeadTime' . date('Y-m-d-H-i-s') . '.xls');


        }

        

        return view('admin.purchase_reports.purchases_status_and_lead_time_report', [
            'suppliers' => WaSupplier::get(),
            'locations' => WaLocationAndStore::all(),
            'title' => $this->base_title . " - Processed Invoices",
            'model' => $this->model,
            'data' => $data,
        ]);

    }

    
}
