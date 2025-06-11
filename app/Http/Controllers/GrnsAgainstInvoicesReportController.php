<?php

namespace App\Http\Controllers;

use App\Model\WaGrn;
use App\Model\WaSupplier;
use App\Services\ExcelDownloadService;
use App\WaSupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GrnsAgainstInvoicesReportController extends Controller
{
    protected $title = 'GRNs Against Invoices Report';

    protected $model = 'grns-against-invoices';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $grnSub = WaGrn::query()
            ->select([
                'grn_number',
                'delivery_date',
                DB::raw('SUM((invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) * invoice_info->"$.vat_rate" / (100 + invoice_info->"$.vat_rate")) AS vat_amount'),
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty"- IFNULL(invoice_info->"$.total_discount", 0)) AS total_amount'),
            ])
            ->groupBy('wa_grns.grn_number');

        // Here I use query builder to avoid map for date format
        $query = DB::table('wa_supplier_invoices as invoices')
            ->select([
                'suppliers.supplier_code',
                'suppliers.name AS supplier_name',
                'invoices.grn_number',
                'invoices.supplier_invoice_number',
                'grns.delivery_date',
                DB::raw('ROUND(grns.vat_amount,2) AS grn_vat_amount'),
                'grns.total_amount AS grn_total_amount',
                DB::raw("DATE_FORMAT(invoices.supplier_invoice_date, '%Y-%m-%d') AS supplier_invoice_date"),
                DB::raw('ROUND(invoices.vat_amount,2) AS vat_amount'),
                'invoices.amount',
            ])
            ->joinSub($grnSub, 'grns', 'grns.grn_number', 'invoices.grn_number')
            ->join('wa_suppliers AS suppliers', 'suppliers.id', 'invoices.supplier_id')
            ->when(request()->filled('supplier'), function ($query) {
                $query->where('supplier_id', request()->supplier);
            });

        if (request()->download == 'excel') {
            $headings = [
                'Supplier No',
                'Supplier Name',
                'GRN No.',
                'Supplier Invoice No',
                'GRN Date',
                'GRN Vat Amount',
                'GRN Total Amount',
                'Invoice Date',
                'Invoice Vat Amount',
                'Invoice Total Amount',
            ];

            $data = $query->orderBy('grn_number', 'desc')->get();

            $fileName = 'grns_against_invoices' . date('YmdHis');

            return ExcelDownloadService::download($fileName, $data, $headings);
        }

        if (request()->wantsJson()) {
            return DataTables::of($query)
                ->editColumn('grn_vat_amount', function ($invoice) {
                    return manageAmountFormat($invoice->grn_vat_amount);
                })
                ->editColumn('grn_total_amount', function ($invoice) {
                    return manageAmountFormat($invoice->grn_total_amount);
                })
                ->editColumn('vat_amount', function ($invoice) {
                    return manageAmountFormat($invoice->vat_amount);
                })
                ->editColumn('amount', function ($invoice) {
                    return manageAmountFormat($invoice->amount);
                })
                ->toJson();
        }

        return view('admin.reports.grns_against_invoices_report', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                $this->title => ''
            ],
            'suppliers' => WaSupplier::get()
        ]);
    }
}
