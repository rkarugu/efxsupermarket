<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\GrnSummaryExport;
use App\Http\Controllers\Controller;
use App\Model\WaGrn;
use App\Model\WaSupplier;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class GrnSummaryBySupplierReportController extends Controller
{
    protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'grn-summary-by-supplier-report';
    }

    public function index()
    {
        if (!can('grn-summary-by-supplier-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->title = 'GRN Summary By Supplier Report';
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $grnSub = WaGrn::query()
            ->select([
                'wa_supplier_id',
                'grn_number',
                DB::raw('COUNT(grn_number) grn_items')
            ])
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('grn_number');

        $grnCountSub =  WaSupplier::query()
            ->select([
                'wa_supplier_id',
                DB::raw('IFNULL(COUNT(grn_number), 0) as grn_count')
            ])
            ->leftJoinSUb($grnSub, 'grns', 'grns.wa_supplier_id', '=', 'wa_suppliers.id')
            ->groupBy('wa_supplier_id');

        $grnValueSub = WaGrn::query()
            ->select([
                'wa_supplier_id',
                DB::raw('SUM((invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) * invoice_info->"$.vat_rate" / (100 + invoice_info->"$.vat_rate")) AS vat_amount'),
                DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty"- IFNULL(invoice_info->"$.total_discount", 0)) AS total_amount'),
            ])
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('wa_supplier_id');

        $query = WaSupplier::query()
            ->select([
                'supplier_code',
                'name',
                'grns_count.grn_count',
                DB::raw('IFNULL(grn_values.vat_amount, 0) AS grn_vat'),
                DB::raw('IFNULL(grn_values.total_amount, 0) AS grn_value'),
            ])
            ->leftJoinSub($grnCountSub, 'grns_count', 'grns_count.wa_supplier_id', '=', 'wa_suppliers.id')
            ->leftJoinSub($grnValueSub, 'grn_values', 'grn_values.wa_supplier_id', '=', 'wa_suppliers.id');

        if (request()->action) {
            return Excel::download(new GrnSummaryExport($query->orderBy('grn_value','DESC')->get()), 'grn_summary_by_supplier_' . date('YmdHis') . '.xlsx');
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('grn_vat', function ($record) {
                    return manageAmountFormat($record->grn_vat);
                })
                ->editColumn('grn_value', function ($record) {
                    return manageAmountFormat($record->grn_value);
                })
                ->toJson();
        }

        return view('admin.reports.grn_summary_by_supplier_report', [
            'model' => $this->model,
            'title' => $this->title,
        ]);
    }
}
