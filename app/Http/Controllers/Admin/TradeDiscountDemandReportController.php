<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportViewToExcel;
use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Models\TradeDiscountDemand;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TradeDiscountDemandReportController extends Controller
{
    protected $model = 'trade-discount-demands-report';

    protected $title = 'Trade Discount Demands Report';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . " 00:00:00" : false;
        $to = request()->filled('to') ? request()->to . " 23:59:59" : false;

        $query = DB::table('trade_discount_demands as demands')
            ->select([
                'demands.demand_no',
                'demands.supplier_reference',
                'demands.cu_invoice_number',
                'demands.note_date',
                'demands.memo',
                DB::raw("(CASE WHEN demands.processed = 1 THEN 'Yes' ELSE 'No' END) AS processed"),
                'demands.processed_at',
                'demands.credit_note_no',
                'demands.amount',
                'suppliers.name AS supplier_name',
                'initiators.name AS prepared_by',
                'processors.name AS processed_by',
            ])
            ->join('wa_suppliers as suppliers', 'suppliers.id', 'demands.supplier_id')
            ->leftJoin('users as initiators', 'initiators.id', 'demands.prepared_by')
            ->leftJoin('users as processors', 'processors.id', 'demands.processed_by')
            ->when(request()->filled('supplier'), function ($query) {
                $query->where('demands.supplier_id', request()->supplier);
            })
            ->when(request()->filled('status'), function ($query) {
                if (request()->status == 'pending') {
                    return $query->where('demands.processed', TradeDiscountDemand::PENDING);
                }

                if (request()->status == 'processed') {
                    return $query->where('demands.processed', TradeDiscountDemand::PROCESSED);
                }
            })
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('demands.created_at', [$from, $to]);
            });

        if (request()->wantsJson()) {
            return DataTables::of($query)
                ->editColumn('amount', function ($demand) {
                    return manageAmountFormat($demand->amount);
                })
                ->editColumn('status', function ($demand) {
                    return $demand->processed ? 'Yes' : 'No';
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('amount'));
                })
                ->toJson();
        }

        if (request()->download == 'excel') {
            $view = view(
                'admin.trade_discount_demands.exports.excel',
                [
                    'demands' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                ]
            );

            return Excel::download(new ExportViewToExcel($view), 'trade_discount_demands_report' . date('Ymdhis') . '.xlsx');
        }

        if (request()->download == 'pdf') {
            $pdf = Pdf::loadView(
                'admin.trade_discount_demands.exports.pdf',
                [
                    'demands' => $query->get(),
                    'from' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                    'to' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
                    'description' => 'TRADE DISCOUNT DEMANDS REPORT'
                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download('trade_discount_demands_report' . date('Ymdhis') . '.pdf');
        }

        return view('admin.trade_discount_demands.report', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
            'breadcum' => [
                'Trade Discount Demands Report' => '',
            ]
        ]);
    }
}
