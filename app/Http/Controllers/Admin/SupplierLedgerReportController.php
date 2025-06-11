<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\SupplierLedgerReport;
use App\Http\Controllers\Controller;
use App\Model\WaSuppTran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierLedgerReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'supplier-ledger-report';
        $this->title = 'Supplier Ledger Report';
    }


    public function index(Request $request)
    {
        // revert and update permissions

        if (!can('view', 'supplier-ledger-report')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Accounts Payables' => '', 'Report' => '', $title => ''];

        $query = WaSuppTran::query()
            ->with('supplier');

        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->input('from') . ' 00:00:00';
            $to = $request->input('to') . ' 23:59:59';

            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->action == 'excel') {
            $export = new SupplierLedgerReport($query->get());

            return Excel::download($export, 'supplier_ledger_report_'.date('YmdHis').'.xlsx');
        }

        if ($request->wantsJson()) {
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('vat_amount', function ($transaction) {
                    return manageAmountFormat($transaction->vat_amount);
                })
                ->editColumn('trans_date', function ($transaction) {
                    return $transaction->trans_date->format('Y-m-d');
                })
                ->editColumn('withholding_amount', function ($transaction) {
                    return manageAmountFormat($transaction->withholding_amount);
                })
                ->editColumn('total_amount_inc_vat', function ($transaction) {
                    return manageAmountFormat($transaction->total_amount_inc_vat);
                })
                ->with('total_vat', function () use ($query) {
                    return manageAmountFormat($query->sum('vat_amount'));
                })
                ->with('total_withholding', function () use ($query) {
                    return manageAmountFormat($query->sum('withholding_amount'));
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('total_amount_inc_vat'));
                })
                ->toJson();
        }

        return view('admin.reports.supplier_ledger_report.index', compact('title', 'model', 'breadcum'));
    }
}
