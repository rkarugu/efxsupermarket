<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Services\ApiService;
use Illuminate\Http\RedirectResponse;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use App\Models\TradeAgreement;
use App\Model\Setting;
use App\Model\WaUserSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Log;

class SupplierPortalController extends Controller
{
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->base_title = 'Supplier Portal';
        $this->base_route = 'supplier-portal';
        $this->resource_folder = 'admin.supplier_portal';
    }

    public function getPendingSuppliers(): View|RedirectResponse
    {
        if (!can('view', 'pending-suppliers')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $title = "Pending Suppliers";
        $model = "pending-suppliers";
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;

        $pendingSuppliers = DB::table('wa_suppliers')->where('portal_status', 'pending')->get();

        return view("$this->resource_folder.pending-suppliers", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'pendingSuppliers'
        ));
    }

    public function get_supplier_portal_logs(Request $request)
    {
        if (!can('logs', 'supplier-portal')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        if ($request->ajax()) {
            $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $data = $apiService->get_supplier_portal_logs([
                'limit' => 100,
                'offset' => $request->offset ?? 0,
                'from_date' => $request->from_date ?? date('Y-m-d'),
                'to_date' => $request->to_date ?? date('Y-m-d')
            ])['data'];
            return response()->json($data);
        }

        $title = "Suppliers";
        $model = "supplier-portal";
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;
        $supplier_portal_logs = [];


        return view("$this->resource_folder.supplier-portal-logs", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'supplier_portal_logs'
        ));
    }

    public function get_all_supplier_from_portal()
    {
        if (!can('view', 'supplier-maintain-suppliers')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $title = "Suppliers";
        $model = "supplier-portal";
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;

        $trades = TradeAgreement::with('supplier.users')
            ->where('linked_to_portal', true)
            ->when(!can('can-view-all-suppliers', 'maintain-suppliers'), function ($query) {
                $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                    ->pluck('wa_supplier_id')->toArray();
                $query->whereIn('wa_supplier_id', $supplierIds);
            })
            ->orderBy('linked_at', 'desc')
            ->get();

        $portal_suppliers = $this->get_portal_suppliers();

        return view("$this->resource_folder.get_all_supplier_from_portal", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'trades',
            'portal_suppliers'
        ));
    }

    public function get_portal_suppliers()
    {
        try {
            $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $data = $apiService->get_portal_suppliers(env('SUPPLIER_SOURCE'))['data'];
            return collect($data);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return collect([]);
        }
    }

    public function suspend_supplier(Request $request)
    {
        if (!can('suspend', 'supplier-portal')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        try {
            $supplier = WaSupplier::find($request->id);
            $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $a = $apiService->suspend_supplier([
                'supplier_code' => $supplier->supplier_code,
                'email' => $supplier->email,
                'is_suspended' => !$supplier->is_suspended
            ]);
            if (isset($a['result'])) {
                if ($a['result'] == 1) {
                    $supplier->is_suspended = !$supplier->is_suspended;
                    $supplier->save();
                }
            }
            Session::flash('success', 'Operation Successfully');
            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function get_supplier_staff($id, Request $request)
    {
        if (!can('staff', 'supplier-maintain-suppliers')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $supplier = WaSupplier::find($id);

        if ($request->ajax()) {
            $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $data = $apiService->get_supplier_staff([
                'supplier_code' => $supplier->supplier_code,
                'email' => $supplier->email,
                'limit' => 100,
                'offset' => $request->offset ?? 0
            ])['data'];
            return response()->json($data);
        }

        $title = "Suppliers";
        $model = "supplier-portal";
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;

        return view("$this->resource_folder.get_supplier_staff", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'supplier'
        ));
    }

    public function update_supplier_staff($id, Request $request)
    {
        if (!can('staff', 'supplier-maintain-suppliers')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $supplier = WaSupplier::find($id);
        $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
        $inputs = $request->all();
        $inputs['supplier_code'] = $supplier->supplier_code;
        $inputs['supplier_email'] = $supplier->email;
        $data = $apiService->update_supplier_staff_details(
            $inputs
        );
        $data['location'] = route('supplier-portal.get_supplier_staff', $id);
        return response()->json($data);
    }

    public function billing_description()
    {
        $description = Setting::where('name', 'SUPPLIER_PORTAL_BILLING_DESCRIPTION')->first();
        if (!can('view', 'billing-description')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $title = "Suppliers Portal Billing Desciption";
        $model = "supplier-portal";

        return view("$this->resource_folder.billing_description", compact(
            'title',
            'model',
            'description'
        ));
    }

    public function update_billing_description(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }
            $description = Setting::where('name', 'SUPPLIER_PORTAL_BILLING_DESCRIPTION')->first();
            if (!$description) {
                $description = new Setting();
                $description->name = 'SUPPLIER_PORTAL_BILLING_DESCRIPTION';
                $description->parameter_type = 'string';
                $description->status = 1;
            }
            $description->description = $request->description;
            $description->save();
            return response()->json(['result' => 1, 'message' => 'Description stored successfully.', 'location' => route($this->base_route . '.billing-description')], 200);
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
            return response()->json(['result' => -1, 'error' => $msg], 500);
        }
    }

    public function get_supplier_details($supplier_id)
    {

        if (!can('logs', 'supplier-details')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $title = "Suppliers";
        $model = "supplier-portal";
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;

        $supplier = WaSupplier::where('id', $supplier_id)->first();
        $portal_supplier = $this->get_portal_supplier_details($supplier);

        return view("$this->resource_folder.supplier_details", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'supplier',
            'portal_supplier'
        ));
    }

    public function get_portal_supplier_details($suplier)
    {
        try {
            $apiService = new ApiService(env('SUPPLIER_PORTAL_URI'));
            return $apiService->get_portal_supplier_details(env('SUPPLIER_SOURCE'), ['code' => $suplier->supplier_code, 'email' => $suplier->email])['data'];
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return [];
        }
    }
}
