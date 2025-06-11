<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\InvoiceBalancingReport;
use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InvoiceBalancingReportController extends Controller
{
    protected $model = 'invoice-balancing-report';

    protected $title = 'Invoice Balancing Report';

    public function index()
    {
        if (!can('invoice-balancing-report', '')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $query = WaInternalRequisition::query()
            ->select([
                'wa_internal_requisitions.id',
                'wa_internal_requisitions.customer_id',
                'wa_internal_requisitions.requisition_no',
                'wa_internal_requisitions.created_at',
                DB::raw("(SELECT SUM(total_cost_with_vat) FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id) AS invoice_amount"),
                DB::raw("(SELECT SUM(price) FROM wa_stock_moves WHERE wa_stock_moves.document_no = wa_internal_requisitions.requisition_no) AS stocks_amount"),
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans WHERE wa_debtor_trans.document_no = wa_internal_requisitions.requisition_no) AS debtors_amount"),
            ])
            ->with([
                'customer',
            ])
            ->when((request()->filled('from') && request()->filled('to')), function ($query) {
                $query->whereBetween('wa_internal_requisitions.created_at', [request()->from, request()->to]);
            });

        if (request()->action == 'excel') {

            $data = $query->orderBy('requisition_no', 'asc')->get();

            $export = new InvoiceBalancingReport($data);

            return Excel::download($export, 'invoice_balancing_report_' . date('YmdHis') . '.csv');
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($invoice) {
                    return $invoice->created_at?->format('Y-m-d H:i:s');
                })
                ->editColumn('invoice_amount', function ($invoice) {
                    return manageAmountFormat($invoice->invoice_amount);
                })
                ->editColumn('stocks_amount', function ($invoice) {
                    return manageAmountFormat($invoice->stocks_amount);
                })
                ->editColumn('debtors_amount', function ($invoice) {
                    return manageAmountFormat($invoice->debtors_amount);
                })
                ->toJson();
        }

        return view('admin.reports.invoice_balancing_report', [
            'title' => $this->title,
            'model' => 'invoice-balancing-report',
            'breadcum' => [
                'Sales & Recieveables' => '',
                $this->title => '',
            ]
        ]);
    }
}
