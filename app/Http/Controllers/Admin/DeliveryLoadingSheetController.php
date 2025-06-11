<?php

namespace App\Http\Controllers\Admin;

use App\LoadingSheetDispatch;
use App\LoadingSheetDispatchItem;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\VehicleAssignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeliveryLoadingSheetController extends Controller
{
    public function generateForDispatch(Request $request)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = 'invoice-dispatch-report';
            $title = 'Invoice Dispatch Report';
            $model = 'dispatch-invoice-report';
            $breadcum = [$title => route('confirm-invoice.invoice_dispatch_report'), 'Listing' => ''];

            $invoiceIds = WaInternalRequisition::where('wa_shift_id', $request->shift_id)->pluck('id');
            $invoiceItems = WaInternalRequisitionItem::whereIn('wa_internal_requisition_id', $invoiceIds)
                ->where('store_location_id', $request->store_id)
                ->select('*', DB::raw('sum(quantity) as total_quantity'))
                ->with(['getInventoryItemDetail'])
                ->groupBy(['wa_inventory_item_id'])
                ->get();

            $shiftId = $request->shift_id;
            return view('admin.issuefullfillrequisition.dispatch_and_close_loading_sheet', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'invoiceItems', 'shiftId'));
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => 'An error was encountered. Please try again.']);
        }
    }

    public function showAssignForm(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'dispatch-and-close-loading-sheet';
        $title = 'Assign Loading Sheet';
        $model = 'dispatch-and-close-loading-sheet';

        if ((!isset($permission['assign-loading-sheet___view']) && $permission != 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $breadcum = [$title => ''];
        return view('admin.issuefullfillrequisition.assign-loading-sheet', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
    }

    public function assign(Request $request)
    {
        //
    }
}
