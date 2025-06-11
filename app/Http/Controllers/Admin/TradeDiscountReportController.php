<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Models\TradeDiscount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TradeDiscountReportController extends Controller
{
    protected $model = 'trade-discounts-report';

    protected $title = 'Trade Discounts Report';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . " 00:00:00" : false;
        $to = request()->filled('to') ? request()->to . " 23:59:59" : false;

        $query = DB::table('trade_discounts as discounts')
            ->select([
                'discounts.*',
                'agreements.discount_type',
                'suppliers.name AS supplier_name',
                'initiators.name AS prepared_by',
                'approvers.name AS approved_by',
                'demand_no'
            ])
            ->join('trade_agreement_discounts as agreements', 'agreements.id', 'discounts.trade_agreement_discount_id')
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'discounts.supplier_id')
            ->leftJoin('users as initiators', 'initiators.id', 'discounts.prepared_by')
            ->leftJoin('users as approvers', 'approvers.id', 'discounts.approved_by')
            ->leftJoin('trade_discount_demand_items as items', 'items.trade_discount_id', 'discounts.id')
            ->leftJoin('trade_discount_demands as demands', 'demands.id', 'items.trade_discount_demand_id')
            ->when(request()->filled('supplier'), function ($query) {
                $query->where('discounts.supplier_id', request()->supplier);
            })
            ->when(request()->filled('status'), function ($query) {
                if (request()->status == 'pending') {
                    return $query->where('discounts.status', TradeDiscount::PENDING);
                }

                if (request()->status == 'approved') {
                    return $query->where('discounts.status', TradeDiscount::APPROVED);
                }
            })
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('discounts.created_at', [$from, $to]);
            });

        if (request()->wantsJson()) {
            return DataTables::of($query)
                ->editColumn('amount', function ($discount) {
                    return manageAmountFormat($discount->amount);
                })
                ->editColumn('status', function ($discount) {
                    return $discount->status ? 'Yes' : 'No';
                })
                ->addColumn('actions', function($discount){
                    return view('admin.trade_discounts.partials.link', compact('discount'));
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('discounts.amount'));
                })
                ->with('total_approved_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('discounts.approved_amount'));
                })
                ->toJson();
        }

        if (request()->download == 'excel') {
            $view = view(
                'admin.trade_discounts.exports.excel',
                [
                    'discounts' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                ]
            );

            return Excel::download(new ExportViewToExcel($view), 'trade_discounts_report' . date('Ymdhis') . '.xlsx');
        }

        if (request()->download == 'pdf') {
            $pdf = Pdf::loadView(
                'admin.trade_discounts.exports.pdf',
                [
                    'discounts' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                    'description' => 'TRADE DISCOUNTS REPORT'
                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download('trade_discounts_report' . date('Ymdhis') . '.pdf');
        }

        return view('admin.trade_discounts.report', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
            'breadcum' => [
                'Trade Discounts Report' => '',
            ]
        ]);
    }
}
