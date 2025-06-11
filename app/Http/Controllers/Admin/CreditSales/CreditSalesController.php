<?php

namespace App\Http\Controllers\Admin\CreditSales;

use App\Enums\Status\CashSaleDispatchStatus;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesDispatch;
use App\Model\WaPosCashSalesItemReturns;
use App\Model\WaUnitOfMeasure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CreditSalesController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected $smsService;


    public function __construct(SmsService $smsService)
    {
        $this->model = 'credit-sales';
        $this->title = 'Credit sales Sales';
        $this->pmodule = 'credit-sales';
        $this->smsService = $smsService;
    }
    public function dispatchScreen(Request $request)
    {
        $user = getLoggeduserProfile();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = "Credit sales";
        $model = 'credit-sales';
        $startDate = $request->from ?? now()->startOfDay();
        $endDate = $request->to  ?? now()->endOfDay();
        $bins = WaUnitOfMeasure::pluck('title', 'id');;


        if ($user->is_hq_user) {
            $bin = (int) $request->bin_id;
        } else {
            $bin = $user->wa_unit_of_measures_id;
        }

        if (isset($permission[$model . '___dispatch']) || $permission == 'superadmin') {


            if ($request->ajax()) {

                $query = WaInternalRequisition::with(['items' => function ($query) use ($bin) {
                        $query->where('is_dispatched', false)
                        ->with('getInventoryItemDetail')
                        ->whereHas('getInventoryItemDetail', function ($query) use ($bin) {
                            $query->whereHas('bin_locations',  function ($t) use ($bin) {
                                $t->where('uom_id',  $bin);
                            });
                        });
                }])
                    ->where('status', '=', 'COMPLETED')
                    ->where('invoice_type', '=','Backend')
                    ->whereHas('items', function ($q) use ($bin) {
                        $q->whereHas('getInventoryItemDetail', function ($k) use ($bin) {
                            $k->whereHas('bin_locations',  function ($t) use ($bin) {
                                $t->where('uom_id',  $bin);
                            });
                        });
                    })
                    ->whereBetween('requisition_date', [$startDate, $endDate])
                    ->withCount(['items' => function ($q) use ($bin) {
                        $q ->where('is_dispatched', false)
                            ->whereHas('getInventoryItemDetail', function ($k) use ($bin) {
                            $k->whereHas('bin_locations',  function ($t) use ($bin) {
                                $t->where('uom_id',  $bin);
                            });
                        });
                    }])
                    ->having('items_count', '>', 0)
                    ->latest();


                return DataTables::eloquent($query)
                    ->editColumn('created_at', function ($row) {
                        return Carbon::parse($row->created_at)->format('Y-m-d, H:i:s');
                    })
                    ->addIndexColumn()
                    ->toJson();
            }
            return view('admin.credit-sales.dispatcher-list', compact('title', 'model', 'pmodule', 'permission','bins','user'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function process(Request $request, $id)
    {

        if (!$request->ajax()) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $request->validate([
            'itemQuantities' => 'required|array',
            'itemQuantities.*.itemId' => 'required',
        ]);
        DB::transaction(function () use ($id, $request) {
            $sale = WaInternalRequisition::with('items')->find($id);
            $ids = [];
            foreach ($request->itemQuantities as $itemQuantity) {
                $ids[] = $itemQuantity['itemId'];
            }
            $items  = $sale->items->whereIn('id', $ids);


            $count = 0;

            foreach ($items as $item) {
                $item->update([
                    'dispatched_time' => now(),
                    'dispatched_by' => getLoggeduserProfile()->id,
                    'is_dispatched' => true,
                ]);
            }

        });

        return response()->json([
            'status' => true,
            'message' => 'Dispatched Successfully',
        ], 200);
    }
}