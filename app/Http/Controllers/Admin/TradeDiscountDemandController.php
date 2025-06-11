<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SupplierInvoice\ConvertTradeDiscountDemand;
use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Models\TradeDiscount;
use App\Models\TradeDiscountDemand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class TradeDiscountDemandController extends Controller
{
    protected $model = 'trade-discount-demands';

    protected $title = 'Trade Discount Demands';

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
        $from = request()->filled('month') ? Carbon::parse(request()->month . ' ' . request()->year)->startOfMonth()->toDateString() : false;
        $to = request()->filled('month') ? Carbon::parse(request()->month . ' ' . request()->year)->endOfMonth()->toDateString() : false;

        $query = TradeDiscountDemand::query()
            ->select([
                'demands.*',
                'suppliers.name AS supplier_name',
                'initiators.name AS prepared_by',
                'processors.name AS processed_by',
            ])
            ->from('trade_discount_demands as demands')
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
                $query->where('start_at', $from)
                    ->where('end_at', $to);
            });

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('amount', function ($demand) {
                    return manageAmountFormat($demand->amount);
                })
                ->editColumn('status', function ($demand) {
                    return $demand->isProcessed() ? 'Yes' : 'No';
                })
                ->addColumn('actions', function ($demand) {
                    return view('admin.trade_discount_demands.actions', compact('demand'));
                })
                ->toJson();
        }

        return view('admin.trade_discount_demands.index', [
            'model' => $this->model,
            'title' => $this->title,
            'suppliers' => WaSupplier::get(),
            'breadcum' => [
                'Trade Discount Demands' => '',
            ]
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return response()->json([
                'success' => false,
                'message' => pageRestrictedMessage()
            ]);
        }

        $this->validate($request, [
            'supplier' => 'required',
            'year' => 'required',
        ]);

        if ($request->filled('month')) {
            $months[] = $request->month;
        } else {
            $months = $this->months;
        }

        $demandsCount = 0;

        DB::beginTransaction();

        try {
            foreach ($months as $month) {
                $from = Carbon::parse($month . ' ' . $request->year)->startOfMonth()->toDateString();
                $to = Carbon::parse($month . ' ' . $request->year)->endOfMonth()->toDateString();

                $discounts = TradeDiscount::query()
                    ->whereBetween('invoice_date', [$from, $to])
                    ->where('supplier_id', $request->supplier)
                    ->whereDoesntHave('demand')
                    ->approved()
                    ->get();

                if (!$discounts->count()) {
                    continue;
                }

                $items = [];
                $totalAmount = 0;
                foreach ($discounts as $discount) {
                    $items[] = [
                        'trade_discount_id' => $discount->id,
                        'amount' => $discount->approved_amount,
                    ];

                    $totalAmount += $discount->approved_amount;
                }

                $demand = TradeDiscountDemand::create([
                    'supplier_id' => $request->supplier,
                    'start_at' => $from,
                    'end_at' => $to,
                    'demand_no' => getCodeWithNumberSeries('TRADE_DISCOUNT_DEMANDS'),
                    'amount' => $totalAmount,
                    'prepared_by' => auth()->user()->id,
                ]);

                updateUniqueNumberSeries('TRADE_DISCOUNT_DEMANDS',  $demand->demand_no);

                foreach ($items as $item) {
                    $demand->items()->create($item);
                }

                $demandsCount++;
            }

            DB::commit();

            if ($demandsCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "($demandsCount) demands created"
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "No eligible discounts found"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(TradeDiscountDemand $demand)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.trade_discount_demands.show', [
            'model' => $this->model,
            'title' => $this->title,
            'demand' => $demand,
            'breadcum' => [
                'Trade Discount Demands' => '',
                'Demand' => ''
            ]
        ]);
    }

    public function edit(TradeDiscountDemand $demand)
    {
        if (!can('convert', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.trade_discount_demands.edit', [
            'model' => $this->model,
            'title' => $this->title,
            'demand' => $demand,
            'breadcum' => [
                'Trade Discount Demands' => '',
                'Convert Demand' => ''
            ]
        ]);
    }

    public function update(Request $request, TradeDiscountDemand $demand)
    {
        if (!can('convert', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'supplier_reference' => 'required',
            'cu_invoice_number' => 'required',
            'note_date' => 'required',
        ]);

        DB::beginTransaction();

        try {

            if (!auth()->user()->isAdministrator() && WaSuppTran::where('document_no', $demand->demand_no)->exists()) {
                throw new Exception('Demand already converted', 422);
            }

            if ($demand->credit_note_no) {
                $demand->update([
                    'supplier_reference' => $request->supplier_reference,
                    'cu_invoice_number' => $request->cu_invoice_number,
                    'note_date' => $request->note_date,
                    'memo' => $request->memo,
                ]);
            } else {
                app(ConvertTradeDiscountDemand::class)->convert($demand, $request->only([
                    'supplier_reference',
                    'cu_invoice_number',
                    'note_date',
                    'memo',
                ]), auth()->user());
            }

            DB::commit();

            Session::flash('success', 'Demand updated successfully');

            return redirect()->route('maintain-suppliers.vendor_centre', [$demand->supplier->supplier_code, '#trade-discounts'],);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->route('trade-discount-demands.edit', $demand->id)
                ->withErrors($th->getMessage());
        }
    }

    public function destroy(TradeDiscountDemand $demand)
    {
        if (!can('delete', $this->model)) {
            return response()->json([
                'success' => false,
                'message' => pageRestrictedMessage()
            ]);
        }

        if ($demand->credit_note_no) {
            return response()->json([
                'success' => false,
                'message' => 'Demand cannot be deleted'
            ]);
        }

        $demand->items()->delete();
        $demand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Demand deleted successfully'
        ]);
    }
}
