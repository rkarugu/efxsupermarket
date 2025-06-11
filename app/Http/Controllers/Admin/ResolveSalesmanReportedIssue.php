<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaRouteCustomer;
use App\Model\WaSupplier;
use App\Models\ResolvedSalesmanReportedIssue;
use App\SalesmanShiftIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


class ResolveSalesmanReportedIssue extends Controller
{

    protected $permissions_module;

    public function __construct()
    {
        $this->permissions_module = 'reported-shift-issues';
    }

    public function resolveSalesmanReportedIssue(Request $request)
    {
        if (!can('resolve-salesman-reported-issues', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $sendMessage = $request->submitType == 'with-message';
        
        try {

            $validator = Validator::make(request()->all(), [
                'resolvedDescription' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $this->validationHandle($validator->messages());
                return $this->jsonify(['message' => $errors], 422);
            }


            $salesmanshiftissue = SalesmanShiftIssue::where('id', request()->issueId)
                ->with('wainventoryitem', 'user')
                ->first();

            $route_manager = DB::table('route_user')
                ->where('route_id', $salesmanshiftissue->route_id)
                ->first();

            $route_manager_data = User::where('id', $route_manager->user_id)->first();

            $salesmanphonenumber = $salesmanshiftissue?->user?->phone_number ?? null;
            $inventoryitem = $salesmanshiftissue?->wainventoryitem?->id ?? null;
            if ($inventoryitem) {
                $itemsupplier = WaInventoryItemSupplier::where('wa_inventory_item_id', $inventoryitem)->first();
                $itemsupplierdata = WaSupplier::find($itemsupplier->wa_supplier_id);
            }
            $routecustomer = WaRouteCustomer::where('id', $salesmanshiftissue->customer_id)->first();
            $customername = $routecustomer->name;
            $customerbusinessname = $routecustomer->bussiness_name;

            if (!$salesmanshiftissue) {
                return $this->jsonify(['message' => 'Salesman shift issue not found.'], 404);
            }

            $resolvedsalesmanreportedissue = ResolvedSalesmanReportedIssue::create([
                'salesman_shift_issues_id' => $salesmanshiftissue->id,
                'description' => request()->resolvedDescription,
                'invoice_sent_to_supplier' => 0,
                'price_changed' => 1,
                'resolved' => 1
            ]);

            $strscenario = ucwords(str_replace('_', ' ', $salesmanshiftissue->scenario));

            if ($request->hqResolvedDescription != '') {

                $resolvedsalesmanreportedissue->hq_description = $request->hqResolvedDescription;
                $resolvedsalesmanreportedissue->save();

                $message = 'Reported issue: ' . $strscenario . "\n" .
                    'Business Name: ' . $customerbusinessname . "\n" .
                    'Comment: ' . $resolvedsalesmanreportedissue->hq_description;

                if ($salesmanphonenumber && $sendMessage) {
                    sendMessage($message, $salesmanphonenumber);
                }

                if ($route_manager_data['phone_number'] && $sendMessage) {
                    sendMessage($message, $route_manager_data['phone_number']);
                }
            }

            $message = 'Reported issue: ' . $strscenario . "\n" .
                'Business Name: ' . $customerbusinessname . "\n" .
                'Comment: ' . $resolvedsalesmanreportedissue->description;

            if ($salesmanphonenumber && $sendMessage) {
                sendMessage($message, $salesmanphonenumber);
            }

            return $this->jsonify(['message' => 'Issue resolved successfully'], 200);
        } catch (\Exception $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
