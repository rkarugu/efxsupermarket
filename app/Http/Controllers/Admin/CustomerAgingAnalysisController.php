<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaCustomer;
use App\Model\WaEsdDetails;
use App\Model\WaInternalRequisitionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerAgingAnalysisController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Customer Aging Analysis';
    }

    public function index(Request $request)
    {
        if (!can('customer-aging-analysis', 'sales-and-receivables-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $date = request()->filled('date') ? Carbon::parse(request()->date) : now();

        $intervals = [];
        $end_30 = $date;
        $start_30 = $end_30->copy()->subDays(30);

        $end_60 = $start_30->copy()->subDay(1);
        $start_60 = $end_60->copy()->subDays(30);

        $end_90 = $start_60->copy()->subDay(1);
        $start_90 = $end_90->copy()->subDays(30);

        $end_120 = $start_90->copy()->subDay(1);
        $start_120 = $end_120->copy()->subDays(30);

        $intervals = [
            '0-30_days' => [$start_30->format('Y-m-d'), $end_30->format('Y-m-d')],
            '31-60_days' => [$start_60->format('Y-m-d'), $end_60->format('Y-m-d')],
            '61-90_days' => [$start_90->format('Y-m-d'), $end_90->format('Y-m-d')],
            '91-120_days' => [$start_120->format('Y-m-d'), $end_120->format('Y-m-d')],
            '>120_days' => [$start_120->copy()->subDay(1)->format('Y-m-d')],
        ];

        // TODO: Associate Payments to invoices
        // Currently there is no way of associating a payment with an invoice
        $query = WaCustomer::query()
            ->select([
                'customer_name',
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at BETWEEN '{$intervals['0-30_days'][0]}' AND '{$intervals['0-30_days'][1]}' AND wa_debtor_trans.wa_customer_id = wa_customers.id) AS days_0_30"),
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at BETWEEN '{$intervals['31-60_days'][0]}' AND '{$intervals['31-60_days'][1]}' AND wa_debtor_trans.wa_customer_id = wa_customers.id) AS days_31_60"),
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at BETWEEN '{$intervals['61-90_days'][0]}' AND '{$intervals['61-90_days'][1]}' AND wa_debtor_trans.wa_customer_id = wa_customers.id) AS days_61_90"),
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at BETWEEN '{$intervals['91-120_days'][0]}' AND '{$intervals['91-120_days'][1]}' AND wa_debtor_trans.wa_customer_id = wa_customers.id) AS days_91_120"),
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%') AND created_at < '{$intervals['>120_days'][0]}' AND wa_debtor_trans.wa_customer_id = wa_customers.id) AS days_120"),
            ]);

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('days_0_30', function ($record) {
                    return manageAmountFormat($record->days_0_30);
                })
                ->editColumn('days_31_60', function ($record) {
                    return manageAmountFormat($record->days_31_60);
                })
                ->editColumn('days_61_90', function ($record) {
                    return manageAmountFormat($record->days_61_90);
                })
                ->editColumn('days_91_120', function ($record) {
                    return manageAmountFormat($record->days_91_120);
                })
                ->editColumn('days_120', function ($record) {
                    return manageAmountFormat($record->days_120);
                })
                ->toJson();
        }

        if ($request->print) {
            $items = $query->get();
            $pdf = Pdf::loadView('admin.customer_aging_analysis.pdf', [$items]);
            $report_name = 'customer_aging_analysis_' . date('Y_m_d_H_i_A');

            return $pdf->download($report_name . '.pdf');
        }

        $breadcum = ['Sales & Receivables' => '', 'Report' => '', $this->title => ''];

        return view('admin.customer_aging_analysis.sheet', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum
        ]);
    }

    public function newvatreport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $permission =  $this->mypermissionsforAModule();
        $start_date = $request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ?  $request->end_date : date('Y-m-d');
        $tax = $request->tax_manager_id;
        $pin = $request->pin;

        $detail = [];

        //      echo "coming"; die;
        $customer =  WaInternalRequisitionItem::join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->select('wa_internal_requisitions.customer_pin', 'wa_internal_requisitions.name', 'wa_internal_requisitions.requisition_date', 'wa_internal_requisition_items.tax_manager_id', 'wa_internal_requisition_items.vat_rate', 'wa_internal_requisition_items.selling_price', 'wa_internal_requisition_items.quantity', 'wa_esd_details.cu_invoice_number')
            /*  ->whereNotNull('wa_internal_requisitions.customer_pin')*/
            ->join('wa_esd_details', function ($join) use ($start_date, $end_date, $tax, $pin) {
                $query = $join->on('wa_internal_requisitions.requisition_no', '=', 'wa_esd_details.invoice_number');
                if ($start_date || $end_date) {
                    $query->whereDate(
                        'wa_internal_requisitions.requisition_date',
                        '>=',
                        $start_date
                    );

                    $query->whereDate(
                        'wa_internal_requisitions.requisition_date',
                        '<=',
                        $end_date
                    );
                }

                if ($tax) {
                    $query->where('wa_internal_requisition_items.tax_manager_id', $tax);
                }

                if ($pin) {
                    $query->where('wa_internal_requisitions.customer_pin', '!=', NULL);
                }

                if (!$pin) {
                    $query->where('wa_internal_requisitions.customer_pin', NULL);
                }
            });



        $customer = $customer->get();

        $restroList = $this->getRestaurantList();


        $breadcum = ['Accounts Payables' => '', 'Report' => '', $title => ''];
        return view('admin.customer_aging_analysis.vatReport', compact('title', 'customer', 'restroList',  'model', 'breadcum', 'detail', 'start_date', 'end_date', 'tax', 'pin'));
    }

    public function esdVatReport(Request $request)
    {
        $title = 'ESD Vat Report';
        $model = 'esd-vat-report';
        $pmodule = 'esd-vat-report';
        $permission =  $this->mypermissionsforAModule();
        if (!isset($permission['sales-and-receivables-reports___' . $pmodule]) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        if ($request->manage) {

            ############################################### CASH SALES ESD ###################################################

            $esdCashSales = WaEsdDetails::selectRaw('tax_managers.title as tax_manager_title, wa_esd_details.id,wa_esd_details.invoice_number,wa_esd_details.description,wa_esd_details.created_at,wa_pos_cash_sales.id,SUM(wa_pos_cash_sales_items.selling_price) as selling_price, wa_pos_cash_sales_items.vat_percentage,SUM(wa_pos_cash_sales_items.vat_amount) as vat_amount,wa_pos_cash_sales_items.tax_manager_id,SUM(wa_pos_cash_sales_items.total) as total')

                ->join('wa_pos_cash_sales', 'wa_pos_cash_sales.sales_no', '=', 'wa_esd_details.invoice_number')
                ->join('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id', '=', 'wa_pos_cash_sales.id')
                ->join('tax_managers', 'wa_pos_cash_sales_items.tax_manager_id', '=', 'tax_managers.id');

            $esdCashSales->where('wa_esd_details.description', 'Signed successfully.');
            $esdCashSales->where('wa_esd_details.invoice_number', 'LIKE', '%CS-%');

            if ($request->from && $request->to) {
                $esdCashSales = $esdCashSales->where(function ($dates) use ($request) {
                    $date = [$request->from . ' 00:00:00', $request->to . ' 23:59:59'];
                    $dates->whereBetween('wa_esd_details.created_at', $date);
                });
            }
            $esdCashSales = $esdCashSales->groupBy('wa_pos_cash_sales_items.tax_manager_id')->get()->map(function ($e) {
                $e->vat_amount_managed = manageAmountFormat($e->vat_amount);
                $e->total_managed = manageAmountFormat($e->total);
                return $e;
            });
            ############################################### INVOICE ESD #################################################

            $esdInvoice = WaEsdDetails::selectRaw('tax_managers.title as tax_manager_title,wa_esd_details.id,wa_esd_details.invoice_number,wa_esd_details.description,wa_esd_details.created_at,wa_inventory_location_transfers.id,wa_inventory_location_transfer_items.vat_rate,SUM(wa_inventory_location_transfer_items.vat_amount) as vat_amount,wa_inventory_location_transfer_items.tax_manager_id,SUM(wa_inventory_location_transfer_items.total_cost_with_vat) as total_cost_with_vat')

                ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfers.transfer_no', '=', 'wa_esd_details.invoice_number')
                ->join('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
                ->join('tax_managers', 'wa_inventory_location_transfer_items.vat_rate', '=', 'tax_managers.tax_value');

            $esdInvoice->where('wa_esd_details.description', 'Signed successfully.');
            $esdInvoice->where('wa_esd_details.invoice_number', 'LIKE', '%INV-%');

            if ($request->from && $request->to) {
                $esdInvoice = $esdInvoice->where(function ($dates) use ($request) {
                    $date = [$request->from . ' 00:00:00', $request->to . ' 23:59:59'];
                    $dates->whereBetween('wa_esd_details.created_at', $date);
                });
            }
            $esdInvoice = $esdInvoice->groupBy('wa_inventory_location_transfer_items.vat_rate')->get()
                ->map(function ($e) {
                    $e->vat_amount_managed = manageAmountFormat($e->vat_amount);
                    $e->total_cost_with_vat_managed = manageAmountFormat($e->total_cost_with_vat);
                    return $e;
                });
            $date = [$request->from . ' 00:00:00', $request->to . ' 23:59:59'];
            $monthlyInvoice = \App\Model\WaInventoryLocationTransferItem::whereHas('getTransferLocation', function ($p) use ($date) {
                $p->whereBetween('created_at', $date);
            })->sum("total_cost_with_vat");
            $monthlyReturns = \App\Model\WaInventoryLocationTransferItem::where('is_return', 1)
                ->whereBetween('return_date', $date)->sum(DB::RAW("selling_price * return_quantity"));
            $monthlyinvoices =     $monthlyInvoice - $monthlyReturns;

            $monthlySale = \App\Model\WaPosCashSalesItems::whereHas('parent', function ($p) {
                $p->where('status', "Completed");
            })->whereBetween('created_at', $date)->sum(DB::RAW("selling_price * qty"));

            $esd_total = $esdInvoice->sum('total_cost_with_vat') + $esdCashSales->sum('total');
            $sale_invoice_total = $monthlyinvoices + $monthlySale;

            $unsigned_esd = $sale_invoice_total - $esd_total;

            if ($request->manage == 'pdf') {
                $pdf = PDF::loadView('admin.customer_aging_analysis.esdvatReportpdf', [
                    'invoiceData' => $esdInvoice,
                    'monthlyinvoices' => manageAmountFormat($monthlyinvoices),
                    'monthlySale' => manageAmountFormat($monthlySale),
                    'esd_total' => manageAmountFormat($esd_total),
                    'unsigned_esd' => manageAmountFormat($unsigned_esd),
                    'sale_invoice_total' => manageAmountFormat($sale_invoice_total),
                    'total_sales_with_vat_invoice' => manageAmountFormat($esdInvoice->sum('total_cost_with_vat')),
                    'cashSalesData' => $esdCashSales,
                    'total_sales_with_vat_cash_sales' => manageAmountFormat($esdCashSales->sum('total')),
                    'grand_total_vat' => manageAmountFormat($esdInvoice->sum('vat_amount') + $esdCashSales->sum('vat_amount')),
                ]);
                return $pdf->download('esd_vat_report.pdf');
            }

            return response()->json([
                'invoiceData' => $esdInvoice,
                'monthlyinvoices' => manageAmountFormat($monthlyinvoices),
                'monthlySale' => manageAmountFormat($monthlySale),
                'esd_total' => manageAmountFormat($esd_total),
                'unsigned_esd' => manageAmountFormat($unsigned_esd),
                'sale_invoice_total' => manageAmountFormat($sale_invoice_total),
                'total_sales_with_vat_invoice' => manageAmountFormat($esdInvoice->sum('total_cost_with_vat')),
                'cashSalesData' => $esdCashSales,
                'total_sales_with_vat_cash_sales' => manageAmountFormat($esdCashSales->sum('total')),
                'grand_total_vat' => manageAmountFormat($esdInvoice->sum('vat_amount') + $esdCashSales->sum('vat_amount')),
            ]);
        }

        return view('admin.customer_aging_analysis.newEsdVatReport', compact('title', 'model', 'pmodule', 'permission'));
    }
}
