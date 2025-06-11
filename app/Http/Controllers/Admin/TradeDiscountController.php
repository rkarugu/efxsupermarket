<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SupplierInvoice\CreateTradeDiscount;
use App\Http\Controllers\Controller;
use App\Models\TradeDiscount;
use App\WaSupplierInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TradeDiscountController extends Controller
{
    protected $model = 'trade-discounts';

    protected $title = 'Trade Discounts';

    protected $months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ];

    public function index()
    {
        if (!can('view', $this->model)) {
            return response()->json([
                'success' => false,
                'message' => pageRestrictedMessage()
            ], 403);
        }

        $from = request()->filled('month') ? Carbon::parse(request()->month . ' ' . request()->year)->startOfMonth()->toDateTimeString() : false;
        $to = request()->filled('month') ? Carbon::parse(request()->month . ' ' . request()->year)->endOfMonth()->toDateTimeString() : false;

        $query = TradeDiscount::query()
            ->select([
                'trade_discounts.*',
                'agreements.discount_type',
                'demand_no'
            ])
            ->with([
                'preparedBy'
            ])
            ->join('trade_agreement_discounts as agreements', 'agreements.id', 'trade_discounts.trade_agreement_discount_id')
            ->leftJoin('trade_discount_demand_items as items', 'items.trade_discount_id', 'trade_discounts.id')
            ->leftJoin('trade_discount_demands as demands', 'demands.id', 'items.trade_discount_demand_id')
            ->where('trade_discounts.supplier_id', request()->supplier)
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('trade_discounts.invoice_date', [$from, $to]);
            });

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

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('invoice_amount', function ($discount) {
                    return manageAmountFormat($discount->invoice_amount);
                })
                ->editColumn('amount', function ($discount) {
                    return manageAmountFormat($discount->amount);
                })
                ->editColumn('approval_amount', function ($discount) {
                    return manageAmountFormat($discount->approval_amount);
                })
                ->editColumn('status', function ($discount) {
                    return $discount->status ? 'Yes' : 'No';
                })
                ->addColumn('actions', function ($discount) {
                    return view('admin.trade_discounts.actions', compact('discount'));
                })
                ->with('total_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('trade_discounts.amount'));
                })
                ->with('total_approved_amount', function () use ($query) {
                    return manageAmountFormat($query->sum('approved_amount'));
                })
                ->toJson();
        }
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, ['supplier' => 'required', 'year' => 'required']);

        $discounts = 0;

        if ($request->filled('month')) {
            $months[] = $request->month;
        } else {
            $months = $this->months;
        }

        DB::beginTransaction();

        try {
            foreach ($months as $month) {
                $from = Carbon::parse($month . ' ' . $request->year)->startOfMonth()->toDateTimeString();
                $to = Carbon::parse($month . ' ' . $request->year)->endOfMonth()->toDateTimeString();

                $invoices = WaSupplierInvoice::whereBetween('supplier_invoice_date', [$from, $to])
                    ->where('supplier_id', $request->supplier)
                    ->whereDoesntHave('discounts')
                    ->get();

                foreach ($invoices as $invoice) {
                    if (app(CreateTradeDiscount::class)->create($invoice)) {
                        $discounts++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "($discounts) discounts created"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(TradeDiscount $discount)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.trade_discounts.show', [
            'model' => $this->model,
            'title' => $this->title,
            'discount' => $discount,
            'breadcum' => [
                'Trade Discounts' => '',
                'Discount' => ''
            ]
        ]);
    }

    public function update(Request $request, TradeDiscount $discount)
    {
        if (!can('approve', $this->model)) {
            return response()->json([
                'success' => false,
                'message' => pageRestrictedMessage()
            ], 403);
        }

        $this->validate($request, ['approved_amount' => 'required']);

        $discount->update([
            'status' => $request->boolean('approve'),
            'approved_amount' => $request->approved_amount,
            'approved_by' => $request->boolean('approve') ? auth()->user()->id : null,
            'approved_at' => $request->boolean('approve') ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully'
        ]);
    }

    public function destroy(TradeDiscount $discount)
    {
        if (!can('delete', $this->model)) {
            return response()->json([
                'success' => false,
                'message' => pageRestrictedMessage()
            ], 403);
        }

        if ($discount->demand) {
            return response()->json([
                'success' => false,
                'message' => 'Discount cannot be deleted'
            ]);
        }

        $discount->items()->delete();
        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }
}
