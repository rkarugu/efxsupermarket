<?php

namespace App\Http\Controllers;

use App\ReturnedGrn;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Models\WaReturnDemand;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\WaReturnDemandItem;
use App\Http\Controllers\Controller;
use App\Models\WaStoreReturnItem;
use App\WaDemandItem;
use Illuminate\Support\Facades\Session;

class ReturnDemandController extends Controller
{
    protected $model;
    protected $base_route;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'return-demands';
        $this->base_route = 'return-demands';
        $this->base_title = 'Return Demands';
        $this->permissions_module = 'return-demands';
    }

    public function returnDemandIndex(Request $request)
    {
        if (can('view', 'return-demands')) {
            $model = $this->model;
            $title = $this->base_title;
            $base_route = $this->base_route;
            $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];

            $suppliers = WaSupplier::procurementRole($request->user())
                ->select('id', 'name')
                ->get();

            $demands = WaReturnDemand::with(['supplier', 'user.userRestaurent', 'returnDemandItems'])
                ->whereHas('supplier', fn($query) => $query->whereIn('id', $suppliers->pluck('id')->toArray()))
                ->orderBy('created_at', 'desc');

            if ($request->supplier) {
                $demands = $demands->where('wa_supplier_id', $request->supplier);
            }

            if ($request->from && $request->to) {
                $demands = $demands->whereDate('created_at', '>=', $request->from)
                    ->whereDate('created_at', '<=', $request->to);
            }

            if (!$request->supplier && !$request->from && !$request->to) {
                $demands = $demands->take(25);
            }

            $demands  = $demands->get();

            return view('admin.return-demands.index', compact('model', 'title', 'breadcum', 'base_route', 'demands', 'suppliers'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function returnDemandDetails(Request $request, $id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-demands';

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $model = $this->model;
            $title = $this->base_title;
            $base_route = $this->base_route;
            $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];

            $demand = WaReturnDemand::find($id);

            $demandItems = WaReturnDemandItem::with('inventoryItem')->where('wa_return_demand_id', $id);

            if ($request->item) {
                $demandItems = $demandItems->where('wa_inventory_item_id', $request->item);
            }

            $demandItems = $demandItems->get();

            $inventoryItems = WaInventoryItem::select('id', 'title', 'stock_id_code')->get();

            return view("admin.return-demands.details", compact('title', 'model', 'breadcum', 'base_route', 'inventoryItems', 'demand', 'demandItems'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function returnDemandPrint($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-demands';

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $demand = WaReturnDemand::with('returnDemandItems.inventoryItem')->find($id);

            $supplier = WaSupplier::find($demand->wa_supplier_id);

            $data = $demand->returnDemandItems;

            $pdf = PDF::loadView("admin.return-demands.print", compact('data', 'supplier', 'demand'))->set_option("enable_php", true);

            return $pdf->stream('Demand_' . $demand->demand_no . '.pdf');
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function returnDemandConvert($id)
    {
        if (!can('convert', 'return-demands')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = $this->base_title;
        $base_route = $this->base_route;
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => '', 'Convert' => ''];

        $page = 'Convert';

        $demand = WaReturnDemand::find($id);

        if ($demand->processed) {
            Session::flash('warning', 'Demand already converted');
            return redirect()->back();
        }

        if ($demand->returnType == 'from grn') {
            $returns = ReturnedGrn::with(['grn' => function ($query) {
                $query->with(['purchaseOrder.getBranch', 'purchaseOrder.storeLocation', 'purchaseOrder.uom', 'inventoryItem' => function ($query) {
                    $query->with('taxManager')
                        ->withSum('stockMoves', 'qauntity');
                }])
                    ->withSum(['returnedGrns' => function ($query) {
                        $query->where('rejected', false)
                            ->where('approved', true);
                    }], 'returned_quantity');
            }, 'user', 'supplier'])
                ->where('return_number', $demand->return_document_no)
                ->where('approved', true)
                ->where('rejected', false)
                ->get()->map(function ($return) {
                    $return->grn->invoice_info = is_string($return->grn->invoice_info) ? json_decode($return->grn->invoice_info) : $return->grn->invoice_info;

                    return $return;
                });

            return view('admin.return-demands.edit-grn', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'returns', 'page'));
        } else {
            $returns = WaStoreReturnItem::with([
                'storeReturn' => function ($query) {
                    $query->with('location.branch', 'uom', 'supplier');
                },
                'inventoryItem' => function ($query) {
                    $query->with('taxManager')
                        ->withSum('stockMoves', 'qauntity');
                }
            ])
                ->whereHas('storeReturn', function ($query) use ($demand) {
                    $query->where('rfs_no', $demand->return_document_no)
                        ->where('approved', true)
                        ->where('rejected', false);
                })
                ->get();

            // dd($returns[0]->storeReturn->location->branch);

            return view('admin.return-demands.edit-store', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'returns', 'page'));
        }
    }

    public function returnDemandApprove($id)
    {
        if (!can('approve', 'return-demands')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = $this->base_title;
        $base_route = $this->base_route;
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Approve' => ''];

        $page = 'Approve';

        $demand = WaReturnDemand::find($id);

        if (!$demand->processed) {
            Session::flash('warning', 'Demand not processed');
            return redirect()->back();
        }

        if ($demand->approve) {
            Session::flash('warning', 'Demand already approved');
            return redirect()->back();
        }

        if ($demand->returnType == 'from grn') {
            $returnDemandItems = WaReturnDemandItem::with([
                'returnDemand.returnedGrns' => fn($query) => $query->with(['grn' => function ($query) {
                    $query->with(['purchaseOrder.getBranch', 'purchaseOrder.storeLocation', 'purchaseOrder.uom', 'purchaseOrderItem' => function ($query) {
                        $query->with(['inventoryItem' => function ($query) {
                            $query->with('taxManager')
                                ->withSum('stockMoves', 'qauntity');
                        }]);
                    }])
                        ->withSum(['returnedGrns' => function ($query) {
                            $query->where('rejected', false)
                                ->where('approved', true);
                        }], 'returned_quantity');
                }, 'user', 'supplier'])
            ])
                ->where('wa_return_demand_id', $id)
                ->get();

            $returns = [];
            foreach ($returnDemandItems as $returnDemandItem) {
                foreach ($returnDemandItem->returnDemand->returnedGrns as $returnedGrn) {
                    $returnedGrn->grn->invoice_info = json_decode($returnedGrn->grn->invoice_info);
                    $returnedGrn->quantity = $returnDemandItem->quantity;
                    $returnedGrn->cost = $returnDemandItem->cost;
                    array_push($returns, $returnedGrn);
                }
            }

            $returns = collect($returns);

            return view('admin.return-demands.edit-grn', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'returns', 'page'));
        } else {
            $returns = WaReturnDemandItem::with([
                'returnDemand.storeReturn' => fn($query) => $query->with('location.branch', 'uom', 'supplier'),
                'inventoryItem' => function ($query) {
                    $query->with('taxManager')
                        ->withSum('stockMoves', 'qauntity');
                }
            ])
                ->where('wa_return_demand_id', $id)
                ->get()
                ->map(function ($return) {
                    $return->store_return = $return->returnDemand->storeReturn;

                    return $return;
                });

            return view('admin.return-demands.edit-store', compact('model', 'title', 'breadcum', 'base_route', 'demand', 'returns', 'page'));
        }
    }
}
