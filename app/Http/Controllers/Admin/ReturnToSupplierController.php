<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Grns\ApproveGrnReturn;
use App\Actions\Grns\CreateGrnReturn;
use App\FinancialNote;
use App\FinancialNoteItem;
use App\User;
use Exception;
use App\WaDemand;
use Carbon\Carbon;
use App\Model\WaGrn;
use App\ReturnedGrn;
use App\Model\WaGlTran;
use App\Model\TaxManager;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Model\WaStockMove;
use App\WaSupplierInvoice;
use App\ItemSupplierDemand;
use Illuminate\Http\Request;
use App\Models\WaStoreReturn;
use App\Model\WaInventoryItem;
use App\Model\WaPurchaseOrder;
use App\Models\WaReturnDemand;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Model\WaChartsOfAccount;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaLocationAndStore;
use App\Models\WaStoreReturnItem;
use Illuminate\Http\JsonResponse;
use App\Models\WaReturnDemandItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Model\WaUnitOfMeasure;
use App\Models\WaLocationStoreUom;
use App\WaDemandItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReturnToSupplierController extends Controller
{
    public function locationAndStoresByBranch($branch_id)
    {
        $locationAndStores = WaLocationAndStore::where('wa_branch_id', $branch_id)
            ->orderBy('location_name')
            ->get();

        $data = [];
        foreach ($locationAndStores as $locationAndStore) {
            $data[$locationAndStore->id] = $locationAndStore->location_name . ' (' . $locationAndStore->location_code . ')';
        }

        return response()->json($data);
    }

    public function locationStoreUoM($locationId)
    {
        $bins = WaUnitOfMeasure::whereHas('location', fn ($query) => $query->where('location_id', $locationId))
            ->orderBy('title')
            ->get()
            ->pluck('title', 'id');

        return response()->json($bins);
    }

    public function suppliersByUoM($uomId)
    {
        $suppliers = WaSupplier::whereHas('products', function ($query) use ($uomId) {
            $query->whereHas('bin_locations', fn ($query) => $query->where('uom_id', $uomId));
        })
            ->distinct('supplier_code')
            ->get();

        $data = [];
        foreach ($suppliers as $supplier) {
            $data[$supplier->id] = $supplier->name . ' (' . $supplier->supplier_code . ')';
        }

        return response()->json($data);
    }

    public function showReturnFromGrnCreatePage(): View
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-grn';
        $user = Auth::user();
        $bins = WaUnitOfMeasure::leftJoin('wa_location_store_uom', 'wa_location_store_uom.uom_id', 'wa_unit_of_measures.id')
            ->where('wa_location_store_uom.location_id', $user->wa_location_and_store_id)
            ->pluck('wa_unit_of_measures.title', 'wa_unit_of_measures.id')
            ->toArray();

        if (isset($permission[$pmodule . '___create']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-grn-create';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From GRN' => ''];
            $title = 'Return To Supplier From GRN';

            return view('admin.return_to_supplier.from-grn.create', compact('model', 'title', 'breadcum', 'bins'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showReturnFromGrnPendingPage(Request $request): View
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-grn';

        if (isset($permission[$pmodule . '___view-pending']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-grn-pending';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From GRN' => '', 'Pending Returns' => ''];
            $title = 'Return To Supplier From GRN';

            $page = 'pending';

            $returns = ReturnedGrn::with('grn', 'user', 'supplier')
                ->when($request->query('from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->query('from'));
                })
                ->when($request->query('to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->query('to'));
                })
                ->when($request->query('supplier'), function ($query) use ($request) {
                    $query->where('wa_supplier_id', $request->query('supplier'));
                })
                ->where('approved', false)
                ->where('rejected', false)
                ->groupBy('return_number')
                ->latest()
                ->get();

            return view('admin.return_to_supplier.from-grn.index', compact('model', 'title', 'breadcum', 'page', 'returns'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showReturnFromGrnApprovedPage(Request $request): View
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-grn';

        if (isset($permission[$pmodule . '___view-approved']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-grn-approved';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From GRN' => '', 'Approved Returns' => ''];
            $title = 'Return To Supplier From GRN';

            $page = 'approved';

            $returns = ReturnedGrn::with('grn', 'user', 'supplier')
                ->when($request->query('from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->query('from'));
                })
                ->when($request->query('to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->query('to'));
                })
                ->when($request->query('supplier'), function ($query) use ($request) {
                    $query->where('wa_supplier_id', $request->query('supplier'));
                })
                ->where('approved', true)
                ->where('rejected', false)
                ->groupBy('return_number')
                ->latest()
                ->get();

            return view('admin.return_to_supplier.from-grn.index', compact('model', 'title', 'breadcum', 'page', 'returns'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showReturnFromGrnApprovePage($return_no)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-grn';

        if (isset($permission[$pmodule . '___approve']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-grn-approve';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From GRN' => '', 'Approve Return' => ''];
            $title = 'Return To Supplier From GRN';

            $returns = ReturnedGrn::with(['grn' => function ($query) {
                $query->with(['purchaseOrder.getBranch', 'purchaseOrder.storeLocation', 'purchaseOrder.uom', 'purchaseOrderItem' => function ($query) {
                    $query->with(['inventoryItem' => function ($query) {
                        $query->withSum('stockMoves', 'qauntity');
                    }]);
                }])
                    ->withSum(['returnedGrns' => function ($query) {
                        $query->where('rejected', false)
                            ->where('approved', true);
                    }], 'returned_quantity');
            }, 'user', 'supplier'])
                ->where('return_number', $return_no)
                ->where('approved', false)
                ->where('rejected', false)
                ->get()->map(function ($return) {
                    $return->grn->invoice_info = json_decode($return->grn->invoice_info);

                    return $return;
                });


            if (!$returns->count()) {
                Session::flash('warning', 'Invalid action!');
                return redirect()->back();
            }

            return view('admin.return_to_supplier.from-grn.approve', compact('model', 'title', 'breadcum', 'returns'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function printReturnFromGrn($return_no)
    {
        if (!can('print', 'return-to-supplier-from-grn')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $returns = ReturnedGrn::with(['grn' => function ($query) {
            $query->with('purchaseOrder.storeLocation', 'purchaseOrderItem.inventoryItem')
                ->withSum(['returnedGrns' => function ($query) {
                    $query->where('rejected', false)
                        ->where('approved', true);
                }], 'returned_quantity');
        }, 'user', 'supplier', 'inventoryItem', 'approvedBy'])
            ->where('return_number', $return_no)
            ->where('approved', true)
            ->where('rejected', false)
            ->get()->map(function ($return) {
                $return->grn->invoice_info = is_string($return->grn->invoice_info) ? json_decode($return->grn->invoice_info) : $return->grn->invoice_info;

                return $return;
            });

        if (!$returns->count()) {
            Session::flash('warning', 'Invalid action!');
            return redirect()->back();
        }

        ReturnedGrn::where('return_number', $return_no)
            ->update([
                'is_printed' => 1
            ]);

        $return['store_location'] = $returns[0]->grn->purchaseOrder->storeLocation->location_name;
        $return['return_no'] = $return_no;
        $return['user'] = $returns[0]->user;
        $return['supplier'] = $returns[0]->supplier;
        $return['approvedBy'] = $returns[0]->approvedBy;
        $return['returns'] = $returns;
        $return['created_at'] = $returns[0]->created_at;
        $return['approved_date'] = $returns[0]->approved_date;
        $return['grn'] = $returns[0]->grn;

        $pdf = PDF::loadView('admin.return_to_supplier.from-grn.print', compact('return'));

        return $pdf->stream("$return_no.pdf");
    }

    public function showReturnFromStoreCreatePage()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-store';

        if (isset($permission[$pmodule . '___create']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-store-create';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Create Return' => ''];
            $title = 'Return To Supplier From Store';

            return view('admin.return_to_supplier.from-store.create', compact('model', 'title', 'breadcum'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showReturnFromStoreApprovePage($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-store';
        $user = Auth::user();

        if (isset($permission[$pmodule . '___view-pending-details']) || $permission == 'superadmin') {
            $model = 'return-to-supplier-from-store-approve';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Approve Return' => ''];
            $title = 'Return To Supplier From Store';

            $return = WaStoreReturn::with(['user.userRestaurent', 'location', 'uom', 'supplier', 'storeReturnItems' => function ($query) {
                $query->with(['inventoryItem' => function ($query) {
                    $query->withSum('stockMoves', 'qauntity');
                }, 'inventoryItem.taxManager']);
            }])->find($id);

            if (!$return || $return->rejected) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }

            return view('admin.return_to_supplier.from-store.approve', compact('model', 'title', 'breadcum', 'return', 'permission', 'user'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showReturnFromStorePendingPage(Request $request): View
    {
        if (can('view', 'return-to-supplier-from-store')) {
            $model = 'return-to-supplier-from-store-pending';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Pending Returns' => ''];
            $title = 'Return To Supplier From Store';

            $page = 'pending';
            $user = Auth::user();

            $returns = WaStoreReturn::with('user', 'location', 'uom', 'supplier')
                ->whereHas('supplier', fn ($query) => $query->procurementRole($user))
                ->when($request->query('from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->query('from'));
                })
                ->when($request->query('to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->query('to'));
                })
                ->when($request->query('store'), function ($query) use ($request) {
                    $query->where('location_id', $request->query('store'));
                })
                ->when($request->query('supplier'), function ($query) use ($request) {
                    $query->where('supplier_id', $request->query('supplier'));
                })
                // ->when(!can('view-all-pending', 'return-to-supplier-from-store'), function ($query) {
                //     $query->whereHas('supplier', function ($query) {
                //         $query->whereHas('users', function ($query) {
                //             $query->where('users.id', auth()->user()->id);
                //         });
                //     });
                // })
                ->where('approved', false)
                ->where('rejected', false);
            if (!can('view-all-pending', 'return-to-supplier-from-store')) {
                $returns = $returns->where('uom_id', $user->wa_unit_of_measures_id);
            }
            $returns = $returns->latest()
                ->get();


            return view('admin.return_to_supplier.from-store.index', compact('model', 'title', 'breadcum', 'page', 'returns'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function showReturnFromStoreApprovedPage(Request $request): View
    {
        if (can('view-approved', 'return-to-supplier-from-store')) {
            $model = 'return-to-supplier-from-store-approved';
            $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Approved Returns' => ''];
            $title = 'Return To Supplier From Store';

            $page = 'approved';

            $returns = WaStoreReturn::with('user', 'location', 'uom', 'supplier', 'approvedBy')
                ->whereHas('supplier', fn ($query) => $query->procurementRole($request->user()))
                ->when($request->query('from'), function ($query) use ($request) {
                    $query->whereDate('approved_date', '>=', $request->query('from'));
                })
                ->when($request->query('to'), function ($query) use ($request) {
                    $query->whereDate('approved_date', '<=', $request->query('to'));
                })
                ->when($request->query('store'), function ($query) use ($request) {
                    $query->where('location_id', $request->query('store'));
                })
                ->when($request->query('supplier'), function ($query) use ($request) {
                    $query->where('supplier_id', $request->query('supplier'));
                })
                ->where('approved', true)
                ->orderBy('approved_date', 'desc')
                ->get();

            return view('admin.return_to_supplier.from-store.index', compact('model', 'title', 'breadcum', 'page', 'returns'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function printReturnFromStore($id)
    {
        if (!can('print', 'return-to-supplier-from-store')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = 'return-to-supplier-from-store';

        $return = WaStoreReturn::with('user', 'location', 'uom', 'supplier', 'storeReturnItems.inventoryItem', 'approvedBy')->find($id);

        $pdf = PDF::loadView('admin.return_to_supplier.from-store.print', compact('return'));

        return $pdf->stream("$return->rfs_no.pdf");
    }

    public function showReturnFromStoreRejectedPage(Request $request)
    {
        if (!can('view-rejected', 'return-to-supplier-from-store')) {
            return returnAccessDeniedPage();
        }

        $model = 'return-to-supplier-from-store-rejected';
        $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Rejected Returns' => ''];
        $title = 'Return To Supplier From Store | Rejected';


        $user = $request->user();

        $returns = WaStoreReturn::with('user', 'location', 'uom', 'supplier', 'rejectedBy')
            ->whereHas('supplier', fn ($query) => $query->procurementRole($request->user()))
            ->when($request->query('from'), function ($query) use ($request) {
                $query->whereDate('rejected_date', '>=', $request->query('from'));
            })
            ->when($request->query('to'), function ($query) use ($request) {
                $query->whereDate('rejected_date', '<=', $request->query('to'));
            })
            ->when($request->query('store'), function ($query) use ($request) {
                $query->where('location_id', $request->query('store'));
            })
            ->when($request->query('supplier'), function ($query) use ($request) {
                $query->where('supplier_id', $request->query('supplier'));
            })
            ->where('rejected', true)
            ->orderBy('rejected_date', 'desc')
            ->get();

        return view('admin.return_to_supplier.from-store.rejected', compact('model', 'title', 'breadcum', 'returns', 'user'));


    }

    public function showReturnFromStoreRejectedDetailsPage($id)
    {
        if (!can('view-rejected', 'return-to-supplier-from-store')) {
            return returnAccessDeniedPage();
        }

        $return = WaStoreReturn::with('storeReturnItems.inventoryItem')->find($id);

        $model = 'return-to-supplier-from-store-rejected';
        $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'From Store' => '', 'Rejected Returns' => route('return-to-supplier.from-store.rejected'), 'Details' => ''];
        $title = 'Return To Supplier From Store | Rejected Details';

        return view('admin.return_to_supplier.from-store.rejected-details', compact('model', 'title', 'breadcum', 'return'));
    }

    public function grnsList(Request $request)
    {
        $grns = WaGrn::with('purchaseOrder', 'supplier')
            ->whereHas('inventoryItem', function ($query) use ($request) {
                $query->whereHas('bin_locations', fn ($query) => $query->where('uom_id', $request->uom_id));
            })
            ->whereDoesntHave('invoice')
            ->groupBy('grn_number')
            ->get()
            ->map(function ($grn) {
                return [
                    'id' => $grn->id,
                    'grn_number' => $grn->grn_number,
                    'purchase_no' => $grn->purchaseOrder->purchase_no,
                    'supplier_name' => $grn->supplier->name,
                ];
            });

        return response()->json($grns);
    }

    // public function grnLineItems(Request $request)
    // {
    //     $grnLineItems = WaGrn::with(['purchaseOrderItem' => function ($query) use ($request) {
    //         $query->where('unit_of_measure', $request->uom_id)->with(['inventoryItem' => function ($query) use ($request) {
    //             $query->withSum('stockMoves', 'qauntity');
    //                 // ->whereHas('bin_locations', fn ($query) => $query->where('uom_id', $request->uom_id));
    //         }]);
    //     }])
    //         ->withSum(['returnedGrns' => function ($query) {
    //             $query->where('rejected', false)->where('approved', true);
    //         }], 'returned_quantity')
    //         ->where('grn_number', $request->grn_number)
    //         ->whereHas('purchaseOrderItem', fn ($query) => $query->whereHas('inventoryItem'))
    //         ->get()
    //         ->map(function ($grn) {
    //             $grn->invoice_info = json_decode($grn->invoice_info);

    //             return $grn;
    //         });

    //     return response()->json($grnLineItems);
    // }
    public function grnLineItems(Request $request)
    {
        $grnLineItems = WaGrn::with(['inventoryItem' => fn ($query) => $query->withSum('stockMoves', 'qauntity')])
            ->withSum(['returnedGrns' => fn ($query) => $query->where('rejected', false)->where('approved', true)], 'returned_quantity')
            ->whereHas('inventoryItem.bin_locations', fn ($query) => $query->where('uom_id', $request->uom_id))
            ->where('grn_number', $request->grn_number)
            ->get()
            ->map(function ($grn) {
                $grn->invoice_info = json_decode($grn->invoice_info);

                return $grn;
            });

        return response()->json($grnLineItems);
    }

    public function processReturnFromGrn(Request $request): JsonResponse
    {
        $grn = WaGrn::where('id', $request->grn_id)->whereHas('invoice')->first();

        if ($grn) {
            return response()->json([
                'error' => "GRN has already been invoiced"
            ], 422);
        }

        $filtered = array_filter($request->lineItems, function ($lineItem) {
            return $lineItem['quantity'] > 0;
        });

        if (!count($filtered)) {
            return response()->json([
                'error' => "At least one item needs a return quantity"
            ], 422);
        }

        DB::beginTransaction();

        try {
            app(CreateGrnReturn::class)->create($request->lineItems, Auth::user());

            DB::commit();

            return response()->json([
                'message' => "Return from GRN processed successfully"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process GRN'
            ], 500);
        }
    }

    public function approveReturnFromGrn(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            app(ApproveGrnReturn::class)->approve($request->lineItems, Auth::user());

            DB::commit();

            return response()->json([
                'message' => "Return from GRN approved successfully"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to approved Return from GRN'
            ], 500);
        }
    }

    public function rejectReturnFromGrn(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            foreach ($request->lineItems as $lineItem) {
                ReturnedGrn::where('id', $lineItem['id'])->update([
                    'rejected' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Return from GRN rejected successfully"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to reject Return from GRN'
            ], 500);
        }
    }

    public function processReturnFromStore(Request $request)
    {
        try {
            
            $request->validate(
                [
                    'user_id' => 'required',
                    'location_id' => 'required|exists:wa_location_and_stores,id',
                    'uom_id' => 'required|exists:wa_unit_of_measures,id',
                    'supplier_id' => 'required|exists:wa_suppliers,id',
                    'lineItems' => 'required|array'
                ],
                [
                    'location_id.required' => 'Store location is required',
                    'location_id.exists' => 'Invalid store location',
                    'uom_id.required' => 'Bin is required',
                    'uom_id.exists' => 'Invalid bin',
                    'supplier_id.required' => 'Supplier is required',
                    'supplier_id.exists' => 'Invalid supplier',
                    'lineItems.required' => 'Item(s) are required',
                ]
            );

            foreach ($request->lineItems as $lineItem) {
                if ($lineItem['quantity'] > $lineItem['qoh']) {
                    return response()->json([
                        'error' => "Item {$lineItem['code']} return quantity exceeds QOH"
                    ], 422);
                }
            }

            DB::beginTransaction();

            try {
                $storeReturn = WaStoreReturn::create([
                    'user_id' => $request->user_id,
                    'location_id' => $request->location_id,
                    'uom_id' => $request->uom_id,
                    'supplier_id' => $request->supplier_id,
                    'rfs_no' => getCodeWithNumberSeries('RETURN_FROM_STORE'),
                    'note' => $request->note,
                ]);

                updateUniqueNumberSeries('RETURN_FROM_STORE', $storeReturn->rfs_no);

                foreach ($request->lineItems as $lineItem) {
                    $storeReturn->storeReturnItems()->create([
                        'wa_inventory_item_id' => $lineItem['item_id'],
                        'quantity' => $lineItem['quantity'],
                        'weight' => $lineItem['weight'],
                        'cost' => $lineItem['cost'],
                        'total_cost' => $lineItem['total_cost'],
                    ]);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Failed to process store return'
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process store return'
            ], 500);
        }
    }

    public function approveReturnFromStore(Request $request, $id)
    {
        try {
            $return = WaStoreReturn::with('user')->find($id);

            $user = $return->user;

            DB::beginTransaction();
            try {
                // Delete removed items
                foreach ($request->deletedItems as $deletedItem) {
                    WaStoreReturnItem::find($deletedItem)->delete();
                }

                // Update all line items
                $vatAmount = [];
                foreach ($request->lineItems as $lineItem) {
                    WaStoreReturnItem::where('id', $lineItem['id'])->update([
                        'quantity' => $lineItem['quantity'],
                        'weight' => $lineItem['weight'],
                        'cost' => $lineItem['cost'],
                        'total_cost' => $lineItem['total_cost'],
                    ]);

                    array_push($vatAmount, (float)$lineItem['vat']);
                }

                $return->load('storeReturnItems.inventoryItem');

                // Update stock moves
                foreach ($return->storeReturnItems as $returnItem) {

                    $stockMove = new WaStockMove();
                    $stockMove->user_id = $user->id;
                    $stockMove->restaurant_id = $user->restaurant_id;
                    $stockMove->wa_location_and_store_id = $return->location_id;
                    $stockMove->wa_inventory_item_id = $returnItem->inventoryItem->id;
                    $stockMove->standard_cost = $returnItem->inventoryItem->standard_cost;
                    $stockMove->selling_price = $returnItem->inventoryItem->selling_price;
                    $stockMove->qauntity = $returnItem->quantity * -1;
                    $stockMove->new_qoh = ($returnItem->inventoryItem->getAllFromStockMoves->where('wa_location_and_store_id', $return->location_id)->sum('qauntity') ?? 0) - $returnItem->quantity;
                    $stockMove->stock_id_code = $returnItem->inventoryItem->stock_id_code;
                    $stockMove->document_no = $return->rfs_no;
                    $stockMove->refrence = "Return from store $return->rfs_no";
                    $stockMove->price = $returnItem->cost;
                    $stockMove->total_cost = $returnItem->cost;
                    $stockMove->save();
                }

                // Update return
                $return->update([
                    'note' => $request->note,
                    'approved' => true,
                    'approved_by' => $request->user_id,
                    'approved_date' => now()
                ]);

                // Create demand
                $demandCode = getCodeWithNumberSeries('DELTA');

                $demandAmount = $return->totalCost();

                $delta = new WaReturnDemand();
                $delta->demand_no = $demandCode;
                $delta->created_by = $user->id;
                $delta->wa_supplier_id = $return->supplier_id;
                $delta->return_document_no = $return->rfs_no;
                $delta->demand_amount = $demandAmount;
                $delta->edited_demand_amount = $demandAmount;
                $delta->vat_amount = array_sum($vatAmount);
                $delta->note = $request->note;
                $delta->save();

                updateUniqueNumberSeries('DELTA', $demandCode);

                // Create demand items
                foreach ($return->storeReturnItems as $returnItem) {
                    WaReturnDemandItem::create([
                        'wa_inventory_item_id' => $returnItem->wa_inventory_item_id,
                        'wa_return_demand_id' => $delta->id,
                        'quantity' => $returnItem->quantity,
                        'cost' => $returnItem->cost,
                        'demand_cost' => $returnItem->total_cost,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Request approved successfully'
                ], 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Failed to approve request'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to approve request'
            ], 500);
        }
    }

    public function rejectReturnFromStore(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string'
        ]);
        
        $return = WaStoreReturn::find($id);
        
        if (!$return || $return->approved || $return->rejected) {
            return response()->json([
                'error' => 'Invalid request'
            ], 422);
        }
        
        try {
            $return->update([
                'rejected' => true,
                'rejected_by' => $request->user()->id,
                'rejected_date' => now(),
                'reject_reason' => $request->reject_reason
            ]);

            return response()->json([
                'message' =>  'Request rejected successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to reject request'
            ], 500);
        }
    }

    public function showProcessedReturnsPage(): View
    {
        $model = 'processed-grns';
        $breadcum = ['Maintain Items' => route('maintain-items.index'), 'RTS' => '', 'Processed' => ''];
        $title = 'Processed Returns';

        $suppliers = DB::table('wa_suppliers')->select('id', 'name')->get();
        $grnNames = DB::table('wa_grns')->whereNot('return_status', 'Returned')->orderBy('created_at', 'DESC')->pluck('grn_number')->toArray();
        $grnNames = array_unique($grnNames);

        $returns = DB::table('returned_grns')->orderBy('return_number', 'DESC')->get()->map(function ($return) {
            $return->date = Carbon::parse($return->created_at)->toFormattedDayDateString();
            $return->user = User::find($return->initiated_by)?->name;
            $return->supplier = DB::table('wa_suppliers')->where('id', $return->wa_supplier_id)->first()?->name;

            $grn = DB::table('wa_grns')->where('id', $return->grn_id)->first();
            $grn->date = Carbon::parse($grn->created_at)->toFormattedDayDateString();

            $return->grn = $grn;
            return $return;
        });

        return view('admin.return_to_supplier.processed', compact('model', 'title', 'breadcum', 'suppliers', 'grnNames', 'returns'));
    }

    public function supplierItems($supplierId)
    {
        $inventoryItems = WaInventoryItem::with([
            'taxManager',
            'pendingGrnReturnItem',
            'pendingStoreReturnItem',
            'assignedItem.destinated_item' => function ($query) {
                $query->with('taxManager', 'pendingStoreReturnItem', 'pendingGrnReturnItem')
                    ->withSum(['stockMoves' => fn ($query) => $query->whereHas('location', fn ($query) => $query->where('id', request()->location_id))], 'qauntity');
            }
        ])
            ->withSum(['stockMoves' => fn ($query) => $query->whereHas('location', fn ($query) => $query->where('id', request()->location_id))], 'qauntity')
            ->whereHas('suppliers', fn ($query) => $query->where('wa_suppliers.id', $supplierId))
            ->whereHas('bin_locations', fn ($query) => $query->where('uom_id', request()->uom_id))
            ->get();

        $data = collect([]);

        foreach ($inventoryItems as $inventoryItem) {
            $pendingStoreReturnsQuantity = $inventoryItem->pendingStoreReturnItem->sum('quantity');
            $pendingGrnReturnsQuantity = $inventoryItem->pendingGrnReturnItem->sum('returned_quantity');
            $inventoryItem->stock_moves_sum_qauntity = $inventoryItem->stock_moves_sum_qauntity - $pendingStoreReturnsQuantity - $pendingGrnReturnsQuantity;
            $data->push($inventoryItem);

            if ($inventoryItem->assignedItem?->destinated_item) {
                $item = $inventoryItem->assignedItem->destinated_item;
                $pendingStoreReturnsQuantity = $item->pendingStoreReturnItem->sum('quantity');
                $pendingGrnReturnsQuantity = $item->pendingGrnReturnItem->sum('returned_quantity');
                $item->stock_moves_sum_qauntity = $item->stock_moves_sum_qauntity - $pendingStoreReturnsQuantity - $pendingGrnReturnsQuantity;
                $data->push($item);
            }
        }

        return response()->json($data);
    }

    public function supplierReturnDemands($supplier_id)
    {
        $demands = WaReturnDemand::with('user.userRestaurent')
            ->withCount('returnDemandItems')
            ->where('wa_supplier_id', $supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($demands);
    }

    public function processDemandFromStore(Request $request)
    {
        $demand = WaReturnDemand::with('user', 'supplier')->find($request->demand_id);

        if ($demand->processed) {
            return response()->json([
                'message' => 'Demand already converted'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $demand_costs = [];
            foreach ($request->lineItems as $lineItem) {
                $cost = str_replace(',', '', $lineItem['cost']);
                $demand_cost = (float)$cost * $lineItem['quantity'];

                WaReturnDemandItem::where('wa_return_demand_id', $demand->id)
                    ->whereHas('inventoryItem', function ($query) use ($lineItem) {
                        $query->where('stock_id_code', $lineItem['item_code']);
                    })
                    ->update([
                        'cost' => $cost,
                        'demand_cost' => $demand_cost
                    ]);

                array_push($demand_costs, $demand_cost);
            }

            $demand->update([
                'demand_amount' => round(array_sum($demand_costs)),
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'processed' => true
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand processed successfully'
        ]);
    }

    public function approveDemandFromStore(Request $request)
    {
        $demand = WaReturnDemand::with('user', 'supplier')->find($request->demand_id);

        if (!$demand->processed) {
            return response()->json([
                'message' => 'Demand not processed'
            ], 422);
        }

        if ($demand->approved) {
            return response()->json([
                'message' => 'Demand already approved'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $series = WaNumerSeriesCode::where('module', 'RETURN_FROM_STORE')->first();

            $date = date('Y-m-d H:i:s');
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

            $cr_amount = [];
            $vat_amount_arr = [];

            foreach ($request->lineItems as $lineItem) {
                $inventoryItem = WaInventoryItem::with('taxManager')->where('stock_id_code', $lineItem['item_code'])->first();

                $dr = new WaGlTran();
                $dr->grn_type_number = $series->type_number;
                $dr->grn_last_used_number = $series->last_number_used;
                $dr->transaction_type = $series->description;
                $dr->transaction_no = $demand->return_document_no;
                $dr->trans_date = $date;
                $dr->restaurant_id = $demand->user->restaurant_id;
                $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $dr->account = $inventoryItem->getInventoryCategoryDetail->getStockGlDetail->account_code;

                $cost = str_replace(',', '', $lineItem['cost']);

                //managae dr accounts end/
                $vat_amm = 0;
                $total_price = (float)$cost * $lineItem['quantity'];
                $cr_amount[] = $total_price;
                if ($inventoryItem->taxManager->tax_value && (float)$inventoryItem->taxManager->tax_value > 0) {
                    $vat_amm = $total_price - (($total_price * 100) / ((float)$inventoryItem->taxManager->tax_value + 100));
                    $vat_amount_arr[] = round($vat_amm, 2);
                }
                $dr->amount = - ($total_price - $vat_amm);
                $dr->narrative = "Return from store demand $demand->demand_no - $demand->return_document_no";
                $dr->save();

                WaReturnDemandItem::where('wa_return_demand_id', $demand->id)
                    ->whereHas('inventoryItem', function ($query) use ($lineItem) {
                        $query->where('stock_id_code', $lineItem['item_code']);
                    })
                    ->update([
                        'cost' => $cost,
                        'demand_cost' => $total_price
                    ]);
            }

            //vat entry start
            $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && count($vat_amount_arr) > 0) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series->type_number;
                $vat->transaction_type = $series->description;
                $vat->transaction_no = $demand->return_document_no;
                $vat->grn_last_used_number = $series->last_number_used;
                $vat->trans_date = $date;
                $vat->restaurant_id = $demand->user->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = -array_sum($vat_amount_arr);
                $vat->narrative = null;
                $vat->save();
            }
            // vat entry end 
            // cr entry start

            $suppTran = WaSuppTran::create([
                'grn_type_number' => $series->type_number,
                'supplier_no' => $demand->supplier->supplier_code,
                'suppreference' => $demand->demand_no,
                'trans_date' => $date,
                'total_amount_inc_vat' => -round(array_sum($cr_amount)),
                'vat_amount' => array_sum($vat_amount_arr),
                'document_no' => $demand->demand_no,
                'prepared_by' => $demand->created_by,
                'cu_invoice_number' => $request->cu_invoice_no
            ]);

            // Credit Creditors Control Account account
            $cr = new WaGlTran();
            $cr->grn_type_number = $series->type_number;
            $cr->transaction_type = $series->description;
            $cr->transaction_no = $demand->return_document_no;
            $cr->grn_last_used_number = $series->last_number_used;
            $cr->trans_date = $date;
            $cr->restaurant_id = $demand->user->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->wa_supp_tran_id = $suppTran->id;
            $cr->supplier_account_number = $demand->supplier->supplier_code;
            $cr->account = WaChartsOfAccount::where('account_name', 'CREDITORS CONTROL ACCOUNT')->first()->account_code;
            $cr->amount = round(array_sum($cr_amount));
            $cr->narrative = $demand->demand_no;
            $cr->save();

            $total_cost_with_vat = array_sum($cr_amount);
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round((1 - $roundOff), 2);
                    $crdrAmnt = '+' . $roundOff;
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                    $crdrAmnt = $roundOff;
                }
                $cr = new WaGlTran();
                $cr->grn_type_number = $series->type_number;
                $cr->transaction_type = $series->description;
                $cr->transaction_no = $demand->return_document_no;
                $cr->grn_last_used_number = $series->last_number_used;
                $cr->trans_date = $date;
                $cr->restaurant_id = $demand->user->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = null;
                $cr->account =  "202021";
                $cr->amount = $crdrAmnt;
                $cr->narrative = null;
                $cr->save();
                //cr enter end
            }

            $demand->update([
                'demand_amount' => round(array_sum($cr_amount)),
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'approved' => true,
                'approved_date' => now(),
                'approved_by' => $request->user_id
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to approve demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand approved successfully'
        ]);
    }

    public function processDemandFromGrn(Request $request)
    {
        $demand = WaReturnDemand::with('user')->find($request->demand_id);

        if ($demand->processed) {
            return response()->json([
                'message' => 'Demand already converted'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $demand_costs = [];
            foreach ($request->lineItems as $lineItem) {

                $cost = (float)str_replace(',', '', $lineItem['cost']);
                $demand_cost = (float)$cost * $lineItem['quantity'];

                WaReturnDemandItem::where('wa_return_demand_id', $demand->id)
                    ->whereHas('inventoryItem', function ($query) use ($lineItem) {
                        $query->where('stock_id_code', $lineItem['item_code']);
                    })
                    ->update([
                        'cost' => $cost,
                        'demand_cost' => $demand_cost
                    ]);

                array_push($demand_costs, $demand_cost);
            }

            $demand->update([
                'demand_amount' => round(array_sum($demand_costs)),
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'processed' => true
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand processed successfully'
        ]);
    }

    public function approveDemandFromGrn(Request $request)
    {
        $demand = WaReturnDemand::with('user')->find($request->demand_id);

        if (!$demand->processed) {
            return response()->json([
                'message' => 'Demand not processed'
            ], 422);
        }

        if ($demand->approved) {
            return response()->json([
                'message' => 'Demand already approved'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $series = WaNumerSeriesCode::where('module', 'RETURN')->first();

            $date = date('Y-m-d H:i:s');
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

            $cr_amount = [];
            $vat_amount_arr = [];
            $purchaseOrder = null;
            foreach ($request->lineItems as $lineItem) {
                $grn = ReturnedGrn::find($lineItem['id'])->grn;

                $purchaseOrder = $grn->purchaseOrder;
                $purchaseOrderItem = $grn->purchaseOrderItem;
                $accountno = $purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                $invoiceInfo = json_decode($grn->invoice_info);

                $dr = new WaGlTran();
                $dr->grn_type_number = $series->type_number;
                $dr->grn_last_used_number = $series->last_number_used;
                $dr->transaction_type = $series->description;
                $dr->transaction_no = $grn->grn_number;
                $dr->trans_date = $date;
                $dr->restaurant_id = $demand->user->restaurant_id;
                $dr->wa_purchase_order_id = $grn->wa_purchase_order_id;
                $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $dr->supplier_account_number = null;
                $dr->account = $accountno;

                $cost = (float)str_replace(',', '', $lineItem['cost']);

                //managae dr accounts end/
                $vat_amm = 0;
                $total_price = (float)$cost * $lineItem['quantity'];
                $cr_amount[] = $total_price;
                if ($invoiceInfo->vat_rate && (float)$invoiceInfo->vat_rate > 0) {
                    $vat_amm = $total_price - (($total_price * 100) / ((float)$invoiceInfo->vat_rate + 100));
                    $vat_amount_arr[] = round($vat_amm, 2);
                }
                $dr->amount = - ($total_price - $vat_amm);
                $dr->narrative = $purchaseOrder->purchase_no . '/' . ($purchaseOrder->getSupplier->supplier_code) . '/' . $purchaseOrderItem->item_no . '/' . $purchaseOrderItem->getInventoryItemDetail->title . '/' . $purchaseOrderItem->quantity . '@' . $total_price;
                $dr->save();

                WaReturnDemandItem::where('wa_return_demand_id', $demand->id)
                    ->whereHas('inventoryItem', function ($query) use ($lineItem) {
                        $query->where('stock_id_code', $lineItem['item_code']);
                    })
                    ->update([
                        'cost' => $cost,
                        'demand_cost' => $total_price
                    ]);
            }

            //vat entry start
            $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && count($vat_amount_arr) > 0) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series->type_number;
                $vat->transaction_type = $series->description;
                $vat->transaction_no = $grn->grn_number;
                $vat->grn_last_used_number = $series->last_number_used;
                $vat->trans_date = $date;
                $vat->restaurant_id = $demand->user->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->supplier_account_number = null;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = -array_sum($vat_amount_arr);
                $vat->narrative = null;
                $vat->wa_purchase_order_id = $purchaseOrder->id;
                $vat->save();
            }
            // vat entry end 
            // cr entry start
            $cr = new WaGlTran();
            $cr->grn_type_number = $series->type_number;
            $cr->transaction_type = $series->description;
            $cr->transaction_no = $grn->grn_number;
            $cr->grn_last_used_number = $series->last_number_used;
            $cr->trans_date = $date;
            $cr->restaurant_id = $demand->user->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->supplier_account_number = null;
            $cr->account =  $purchaseOrder->getBranch->getAssociateCompany->good_receive->account_code;
            $cr->amount = round(array_sum($cr_amount));
            $cr->narrative = null;
            $cr->wa_purchase_order_id = $purchaseOrder->id;
            $cr->save();

            $total_cost_with_vat = array_sum($cr_amount);
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round((1 - $roundOff), 2);
                    $crdrAmnt = '+' . $roundOff;
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                    $crdrAmnt = $roundOff;
                }
                $cr = new WaGlTran();
                $cr->grn_type_number = $series->type_number;
                $cr->transaction_type = $series->description;
                $cr->transaction_no = $grn->grn_number;
                $cr->grn_last_used_number = $series->last_number_used;
                $cr->trans_date = $date;
                $cr->restaurant_id = $demand->user->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = null;
                $cr->account =  "202021";
                $cr->amount = $crdrAmnt;
                $cr->narrative = null;
                $cr->wa_purchase_order_id = $purchaseOrder->id;
                $cr->save();
                //cr enter end
            }

            $demand->update([
                'demand_amount' => round(array_sum($cr_amount)),
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'approved' => true,
                'approved_date' => now(),
                'approved_by' => $request->user_id
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to approve demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand approved successfully'
        ]);
    }

    public function processPriceDemand(Request $request)
    {
        $demand = WaDemand::with('user', 'supplier')->find($request->demand_id);

        if ($demand->processed) {
            return response()->json([
                'message' => 'Demand already converted'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $demand_costs = [];
            foreach ($request->lineItems as $lineItem) {
                $demandItem = WaDemandItem::find($lineItem['id']);

                $demandQuantity = (float)str_replace(',', '', $lineItem['demand_quantity']);
                $newCost = (float)str_replace(',', '', $lineItem['new_cost']);
                $total_price = ((float)$demandItem->current_cost - $newCost) * $demandQuantity;

                $demandItem->update([
                    'new_cost' => $newCost,
                    'demand_quantity' => $demandQuantity,
                ]);

                array_push($demand_costs, $total_price);
            }

            $demand->update([
                'demand_amount' => round(array_sum($demand_costs)),
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'processed' => true
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand processed successfully'
        ]);
    }

    public function supplierPriceDemands($supplier_id)
    {
        $demands = WaDemand::with('user.userRestaurent')
            ->withCount('demandItems')
            ->where('wa_supplier_id', $supplier_id)
            ->where('merged', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($demands);
    }

    public function editReturnDemand(Request $request, WaReturnDemand $returnDemand)
    {
        try {
            $returnDemand->update([
                'vat_amount' => $request->vat_amount,
                'edited_demand_amount' => $request->edited_demand_amount,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand edited successfully"
        ]);
    }

    public function approveReturnDemand(Request $request, WaReturnDemand $returnDemand)
    {
        try {
            $returnDemand->update([
                'approved' => true,
                'approved_by' => $request->user_id,
                'approved_date' => now()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand approved successfully"
        ]);
    }

    public function editAndApproveReturnDemand(Request $request, WaReturnDemand $returnDemand)
    {
        try {
            $returnDemand->update([
                'vat_amount' => $request->vat_amount,
                'edited_demand_amount' => $request->edited_demand_amount,
                'approved' => true,
                'approved_by' => $request->user_id,
                'approved_date' => now()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand approved successfully"
        ]);
    }

    public function convertReturnDemand(Request $request, WaReturnDemand $returnDemand)
    {
        $returnDemand->load('user', 'supplier');

        if (!$returnDemand->approved) {
            return response()->json([
                'message' => 'Demand not approved'
            ], 422);
        }

        if ($returnDemand->processed) {
            return response()->json([
                'message' => 'Demand already converted'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $date = date('Y-m-d H:i:s');
            $purchasesAccount = WaChartsOfAccount::where('account_name', 'PURCHASES')->first();

            $financialNote = FinancialNote::create([
                'note_no' => getCodeWithNumberSeries('FINANCIAL_NOTES'),
                'type' => 'CREDIT',
                'supplier_id' => $returnDemand->wa_supplier_id,
                'note_date' => $date,
                'location_id' => $returnDemand->user->restaurant_id,
                'cu_invoice_number' => $request->cu_invoice_no,
                'supplier_invoice_number' => $request->supplier_reference,
                'memo' => $returnDemand->demand_no,
                'tax_amount' => $returnDemand->vat_amount,
                'withholding_amount' => $returnDemand->supplier->tax_withhold  ? ceil($returnDemand->vat_amount * (2 / 16)) : 0,
                'amount' => $returnDemand->edited_demand_amount,
                'created_by' => $request->user_id,
            ]);

            updateUniqueNumberSeries('FINANCIAL_NOTES', $financialNote->note_no);

            foreach ($returnDemand->returnDemandItems as $returnDemandItem) {
                $returnDemandItem->load('inventoryItem.taxManager');
                $taxRate = $returnDemandItem->inventoryItem->taxManager->tax_value;
                $taxAmount = $returnDemandItem->demand_cost - (($returnDemandItem->demand_cost * 100) / ((float)$taxRate + 100));

                FinancialNoteItem::create([
                    'financial_note_id' => $financialNote->id,
                    'account_id' => $purchasesAccount->id,
                    'memo' => $returnDemand->demand_no,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'withholding_amount' => $returnDemand->supplier->tax_withhold  ? ceil($taxAmount * (2 / 16)) : 0,
                    'amount' => $returnDemandItem->demand_cost,
                ]);
            }

            $series = WaNumerSeriesCode::where('module', 'DELTA')->first();

            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

            $dr = new WaGlTran();
            $dr->grn_type_number = $series->type_number;
            $dr->grn_last_used_number = $series->last_number_used;
            $dr->transaction_type = $series->description;
            $dr->transaction_no = $returnDemand->demand_no;
            $dr->trans_date = $date;
            $dr->restaurant_id = $returnDemand->user->restaurant_id;
            $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $dr->account = $purchasesAccount->account_code;
            $dr->amount = - ($returnDemand->edited_demand_amount - $returnDemand->vat_amount);
            $dr->narrative = "Return from store demand $returnDemand->demand_no - $returnDemand->return_document_no";
            $dr->supplier_account_number = $returnDemand->supplier->supplier_code;
            $dr->reference = $returnDemand->return_document_no;
            $dr->tb_reporting_branch = $returnDemand->user->restaurant_id;
            $dr->save();

            //vat entry start
            $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && $returnDemand->vat_amount) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series->type_number;
                $vat->transaction_type = $series->description;
                $vat->transaction_no = $returnDemand->demand_no;
                $vat->grn_last_used_number = $series->last_number_used;
                $vat->trans_date = $date;
                $vat->restaurant_id = $returnDemand->user->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = -$returnDemand->vat_amount;
                $vat->narrative = null;
                $vat->supplier_account_number = $returnDemand->supplier->supplier_code;
                $vat->reference = $returnDemand->return_document_no;
                $vat->tb_reporting_branch = $returnDemand->user->restaurant_id;
                $vat->save();
            }
            // vat entry end 
            // cr entry start

            $suppTran = WaSuppTran::create([
                'grn_type_number' => $series->type_number,
                'supplier_no' => $returnDemand->supplier->supplier_code,
                'suppreference' => $returnDemand->demand_no,
                'trans_date' => $date,
                'total_amount_inc_vat' => -$returnDemand->edited_demand_amount,
                'vat_amount' => $returnDemand->vat_amount,
                'document_no' => $returnDemand->demand_no,
                'prepared_by' => $returnDemand->created_by,
                'cu_invoice_number' => $request->cu_invoice_no
            ]);

            // Credit Creditors Control Account account
            $cr = new WaGlTran();
            $cr->grn_type_number = $series->type_number;
            $cr->transaction_type = $series->description;
            $cr->transaction_no = $returnDemand->demand_no;
            $cr->grn_last_used_number = $series->last_number_used;
            $cr->trans_date = $date;
            $cr->restaurant_id = $returnDemand->user->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->wa_supp_tran_id = $suppTran->id;
            $cr->supplier_account_number = $returnDemand->supplier->supplier_code;
            $cr->account = WaChartsOfAccount::where('account_name', 'CREDITORS CONTROL ACCOUNT')->first()->account_code;
            $cr->amount = $returnDemand->edited_demand_amount;
            $cr->narrative = $returnDemand->demand_no;
            $cr->supplier_account_number = $returnDemand->supplier->supplier_code;
            $cr->reference = $returnDemand->return_document_no;
            $cr->tb_reporting_branch = $returnDemand->user->restaurant_id;
            $cr->save();

            $total_cost_with_vat = $returnDemand->edited_demand_amount;
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round((1 - $roundOff), 2);
                    $crdrAmnt = '+' . $roundOff;
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                    $crdrAmnt = $roundOff;
                }
                $cr = new WaGlTran();
                $cr->grn_type_number = $series->type_number;
                $cr->transaction_type = $series->description;
                $cr->transaction_no = $returnDemand->demand_no;
                $cr->grn_last_used_number = $series->last_number_used;
                $cr->trans_date = $date;
                $cr->restaurant_id = $returnDemand->user->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = null;
                $cr->account =  "202021";
                $cr->amount = $crdrAmnt;
                $cr->narrative = null;
                $cr->supplier_account_number = $returnDemand->supplier->supplier_code;
                $cr->reference = $returnDemand->return_document_no;
                $cr->tb_reporting_branch = $returnDemand->user->restaurant_id;
                $cr->save();
                //cr enter end
            }

            $returnDemand->update([
                'supplier_reference' => $request->supplier_reference,
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'processed' => true,
                'credit_note_no' => $financialNote->note_no,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to convert demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand converted successfully'
        ]);
    }

    public function editPriceDemand(Request $request, WaDemand $priceDemand)
    {
        try {
            $priceDemand->update([
                'vat_amount' => $request->vat_amount,
                'edited_demand_amount' => $request->edited_demand_amount,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand edited successfully"
        ]);
    }

    public function approvePriceDemand(Request $request, WaDemand $priceDemand)
    {
        try {
            $priceDemand->update([
                'approved' => true,
                'approved_by' => $request->user_id,
                'approved_date' => now()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand approved successfully"
        ]);
    }

    public function editAndApprovePriceDemand(Request $request, WaDemand $priceDemand)
    {
        try {
            $priceDemand->update([
                'vat_amount' => $request->vat_amount,
                'edited_demand_amount' => $request->edited_demand_amount,
                'approved' => true,
                'approved_by' => $request->user_id,
                'approved_date' => now()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => "Demand approved successfully"
        ]);
    }

    public function convertPriceDemand(Request $request, WaDemand $priceDemand)
    {
        if (!$priceDemand->approved) {
            return response()->json([
                'message' => 'Demand not approved'
            ], 422);
        }

        if ($priceDemand->processed) {
            return response()->json([
                'message' => 'Demand already converted'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $date = date('Y-m-d H:i:s');
            $purchasesAccount = WaChartsOfAccount::where('account_name', 'PURCHASES')->first();

            $financialNote = FinancialNote::create([
                'note_no' => getCodeWithNumberSeries('FINANCIAL_NOTES'),
                'type' => 'CREDIT',
                'supplier_id' => $priceDemand->wa_supplier_id,
                'note_date' => $date,
                'location_id' => $priceDemand->user->restaurant_id,
                'cu_invoice_number' => $request->cu_invoice_no,
                'supplier_invoice_number' => $request->supplier_reference,
                'memo' => $priceDemand->demand_no,
                'tax_amount' => $priceDemand->vat_amount,
                'withholding_amount' => $priceDemand->supplier->tax_withhold  ? ceil($priceDemand->vat_amount * (2 / 16)) : 0,
                'amount' => $priceDemand->edited_demand_amount,
                'created_by' => $request->user_id,
            ]);

            updateUniqueNumberSeries('FINANCIAL_NOTES', $financialNote->note_no);

            foreach ($priceDemand->demandItems as $demandItem) {
                $demandItem->load('inventoryItem.taxManager');
                $demandCost = ($demandItem->current_cost - $demandItem->new_cost) * $demandItem->demand_quantity;
                $taxRate = $demandItem->inventoryItem->taxManager->tax_value;
                $taxAmount = $demandCost - (($demandCost * 100) / ((float)$taxRate + 100));

                FinancialNoteItem::create([
                    'financial_note_id' => $financialNote->id,
                    'account_id' => $purchasesAccount->id,
                    'memo' => $priceDemand->demand_no,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'withholding_amount' => $priceDemand->supplier->tax_withhold  ? ceil($taxAmount * (2 / 16)) : 0,
                    'amount' => $demandCost,
                ]);
            }

            $series = WaNumerSeriesCode::where('module', 'RETURN_FROM_STORE')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

            $dr = new WaGlTran();
            $dr->grn_type_number = $series->type_number;
            $dr->grn_last_used_number = $series->last_number_used;
            $dr->transaction_type = $series->description;
            $dr->transaction_no = $priceDemand->return_document_no;
            $dr->trans_date = $date;
            $dr->restaurant_id = $priceDemand->user->restaurant_id;
            $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $dr->account = WaChartsOfAccount::where('account_name', 'PURCHASES')->first()->account_code;
            $dr->amount = - ($priceDemand->edited_demand_amount - $priceDemand->vat_amount);
            $dr->narrative = "Return from store demand $priceDemand->demand_no - $priceDemand->return_document_no";
            $dr->save();

            //vat entry start
            $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && $priceDemand->vat_amount) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series->type_number;
                $vat->transaction_type = $series->description;
                $vat->transaction_no = $priceDemand->return_document_no;
                $vat->grn_last_used_number = $series->last_number_used;
                $vat->trans_date = $date;
                $vat->restaurant_id = $priceDemand->user->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = -$priceDemand->vat_amount;
                $vat->narrative = null;
                $vat->save();
            }
            // vat entry end 
            // cr entry start

            $suppTran = WaSuppTran::create([
                'grn_type_number' => $series->type_number,
                'supplier_no' => $priceDemand->supplier->supplier_code,
                'suppreference' => $priceDemand->demand_no,
                'trans_date' => $date,
                'total_amount_inc_vat' => -$priceDemand->edited_demand_amount,
                'vat_amount' => $priceDemand->vat_amount,
                'document_no' => $priceDemand->demand_no,
                'prepared_by' => $priceDemand->created_by,
                'cu_invoice_number' => $request->cu_invoice_no
            ]);

            // Credit Creditors Control Account account
            $cr = new WaGlTran();
            $cr->grn_type_number = $series->type_number;
            $cr->transaction_type = $series->description;
            $cr->transaction_no = $priceDemand->return_document_no;
            $cr->grn_last_used_number = $series->last_number_used;
            $cr->trans_date = $date;
            $cr->restaurant_id = $priceDemand->user->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->wa_supp_tran_id = $suppTran->id;
            $cr->supplier_account_number = $priceDemand->supplier->supplier_code;
            $cr->account = WaChartsOfAccount::where('account_name', 'CREDITORS CONTROL ACCOUNT')->first()->account_code;
            $cr->amount = $priceDemand->edited_demand_amount;
            $cr->narrative = $priceDemand->demand_no;
            $cr->save();

            $total_cost_with_vat = $priceDemand->edited_demand_amount;
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round((1 - $roundOff), 2);
                    $crdrAmnt = '+' . $roundOff;
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                    $crdrAmnt = $roundOff;
                }
                $cr = new WaGlTran();
                $cr->grn_type_number = $series->type_number;
                $cr->transaction_type = $series->description;
                $cr->transaction_no = $priceDemand->return_document_no;
                $cr->grn_last_used_number = $series->last_number_used;
                $cr->trans_date = $date;
                $cr->restaurant_id = $priceDemand->user->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = null;
                $cr->account =  "202021";
                $cr->amount = $crdrAmnt;
                $cr->narrative = null;
                $cr->save();
                //cr enter end
            }

            $priceDemand->update([
                'supplier_reference' => $request->supplier_reference,
                'cu_invoice_no' => $request->cu_invoice_no,
                'note' => $request->note,
                'processed' => true,
                'credit_note_no' => $financialNote->note_no,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to convert demand'
            ], 500);
        }

        return response()->json([
            'message' => 'Demand converted successfully'
        ]);
    }

    public function mergePriceDemands(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'demands' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $demands = WaDemand::with('demandItems')->whereIn('id', $request->demands)->get();

            $demandCode = getCodeWithNumberSeries('DELTA');
            $mergedDemand = WaDemand::create([
                'wa_supplier_id' => $demands->first()->wa_supplier_id,
                'demand_no' => $demandCode,
                'created_by' => $request->user_id,
                'demand_amount' => $demands->sum('demand_amount'),
                'edited_demand_amount' => $demands->sum('edited_demand_amount'),
                'vat_amount' => $demands->sum('vat_amount'),
            ]);
            updateUniqueNumberSeries('DELTA', $demandCode);


            foreach ($demands as $demand) {
                foreach ($demand->demandItems as $demandItem) {
                    $mergedDemand->demandItems()->create([
                        'wa_inventory_item_id' => $demandItem->wa_inventory_item_id,
                        'current_cost' => $demandItem->current_cost,
                        'new_cost' => $demandItem->new_cost,
                        'current_price' => $demandItem->current_price,
                        'new_price' => $demandItem->new_price,
                        'demand_quantity' => $demandItem->demand_quantity,
                    ]);
                }

                $demand->update(['merged' => true]);
            }


            $mergedDemand->update(['merged_from' => $demands->pluck('id')]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Demands merged successfully'
        ]);
    }
}
