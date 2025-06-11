<?php

namespace App\Http\Controllers\Admin;

use App\Model\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;

use App\Model\OrderDeliverySlots;
use App\Model\WaLocationAndStore;
use Log;
use Session;
use Illuminate\Support\Facades\Validator;

class LpoPortalSlotsController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    protected $days = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday",
    ];

    public function __construct()
    {
        $this->model = 'order-delivery-slots';
        $this->title = 'LPO Delivery Slots';
        $this->pmodule = 'order-delivery-slots';
    }
    public function index($branch_id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = OrderDeliverySlots::where('branch_id', $branch_id)->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index', $branch_id), 'Listing' => ''];
            $branch = Restaurant::where('id', $branch_id)->first();
            return view('admin.order_delivery_slots.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'branch'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function delivery_branches()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.delivery_branches'), 'Listing' => ''];
            $branchs = Restaurant::get();
            return view('admin.order_delivery_slots.branch_list', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branchs'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create($branch_id, Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $days = $this->days;
            $breadcum = [$title => route($model . '.index', $branch_id), 'Listing' => ''];
            return view('admin.order_delivery_slots.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'days', 'branch_id'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store($branch_id, Request $request)
    {
        $validations = Validator::make($request->all(), [
            'day' => 'required|unique:order_delivery_slots,day,null,id,branch_id,' . $branch_id . '|in:' . implode(',', $this->days),
            'slots' => 'required|max:24|min:0|numeric',
            'no_of_delivery_points' => 'required|max:50|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ], [
            'start_time.required' => 'Start time is required',
            'end_time.required'   => 'Latest by time is required',
            'end_time.after'      => 'Latest by time must be after the start time',
        ]);
        if ($validations->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $start_time = strtotime($request->start_time);
        $end_time = strtotime($request->end_time);

        if (($end_time - $start_time) < 3600) { // 3600 seconds = 1 hour
            return response()->json([
                'result' => 0,
                'errors' => ['end_time' => ['The difference between start time and end time must be at least 1 hour.']]
            ]);
        }
        $new = new OrderDeliverySlots;
        $new->day = $request->day;
        $new->branch_id = $branch_id;
        $new->no_of_delivery_points = $request->no_of_delivery_points;
        $new->slots = $request->slots;
        $new->end_time = $request->end_time;
        $new->start_time = date('H:i:s', strtotime($request->start_time));

        $new->save();
        return response()->json([
            'result' => 1,
            'message' => 'LPO Delivery Slots Added successfully',
            'location' => route($this->model . '.index', $branch_id),
        ]);
    }
    public function show_booked_slots(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___show']) || $permission == 'superadmin') {
            $breadcum = [$title => '', 'Listing' => ''];
            $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
            $user = getLoggeduserProfile();
            if ($user->role_id == 152) {
                $userSuppliers = \App\Model\WaUserSupplier::all()->pluck('wa_supplier_id')->toArray();
                $userItems = \App\Model\WaInventoryLocationUom::where('uom_id', $user->wa_unit_of_measures_id)->pluck('inventory_id')->toArray();
            } else {
                $userSuppliers = \App\Model\WaUserSupplier::where('user_id', $user->id)->pluck('wa_supplier_id')->toArray();
            }
            $lpos = WaPurchaseOrder::where('status', 'APPROVED')
                ->where('advance_payment', false)
                ->with([
                    'getSupplier'
                ])
                ->where('advance_payment', 0)
                ->where('supplier_accepted', 1)
                ->where('slot_booked', 0)
                ->where('goods_released', 0)
                ->where(function ($e) use ($user, $userSuppliers, $permission) {
                    if ($user->role_id != 1 && !isset($permission['purchase-orders___view-all'])) {
                        $e->whereIn('wa_supplier_id', $userSuppliers);
                    }
                })
                ->doesntHave('invoices')
                ->doesntHave('grns');
                
            if ($user->role_id == 152) {
                $lpos = $lpos->whereHas('getRelatedItem', function ($query) use ($userItems) {
                    $query->whereIn('wa_inventory_item_id', $userItems);
                });
            }
            $storeID = $request->filled('store') ? $request->store : 46;
            $store = WaLocationAndStore::find($storeID)->location_name;

            $lpos = $lpos->get();
            $bookedSlots = $api->postRequest(env('SUPPLIER_PORTAL_BOOKED_SLOTS', '/api/lpo/get-booked-slots'), [
                'date' => $request->date ?? date('Y-m-d'),
                'store' => $store,
                'from' => env('SUPPLIER_SOURCE')
            ]);

            $slots = [];
            if (isset($bookedSlots['result']) == 1) {
                $slots = $bookedSlots['data'];
            }
            return view('admin.order_delivery_slots.show_booked_slots', compact('title', 'model', 'slots', 'breadcum', 'pmodule', 'permission', 'lpos'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function edit($id, $order_delivery_slot)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
            $slot = OrderDeliverySlots::where('branch_id', $id)->findOrFail($order_delivery_slot);
            $breadcum = [$title => route($model . '.index', $id), 'Listing' => ''];
            $days = $this->days;
            return view('admin.order_delivery_slots.edit', compact('title', 'model', 'slot', 'breadcum', 'pmodule', 'permission', 'days'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function update($branch_id, $order_delivery_slot, Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (!isset($permission[$pmodule . '___edit']) && !$permission == 'superadmin') {
            return response()->json([
                'result' => -1,
                'message' => 'Restricted: You dont have enough permissions',
            ]);
        }
        $validations = Validator::make($request->all(), [
            'day' => 'required|unique:order_delivery_slots,day,' . $order_delivery_slot . ',id,branch_id,' . $branch_id . '|in:' . implode(',', $this->days),
            'slots' => 'required|max:24|min:0',
            'no_of_delivery_points' => 'required|max:50|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ], [
            'start_time.required' => 'Start time is required',
            'end_time.required'   => 'Latest by time is required',
            'end_time.after'      => 'Latest by time must be after the start time',
        ]);
        if ($validations->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $start_time = strtotime($request->start_time);
        $end_time = strtotime($request->end_time);
        if (($end_time - $start_time) < 3600) { // 3600 seconds = 1 hour
            return response()->json([
                'result' => 0,
                'errors' => ['end_time' => ['The difference between start time and end time must be at least 1 hour.']]
            ]);
        }
        $new = OrderDeliverySlots::findOrFail($order_delivery_slot);
        $new->day = $request->day;
        $new->slots = $request->slots;
        $new->no_of_delivery_points = $request->no_of_delivery_points;
        $new->end_time = $request->end_time;
        $new->start_time = date('H:i:s', strtotime($request->start_time));
        $new->save();
        return response()->json([
            'result' => 1,
            'message' => 'LPO Delivery Slots Updated successfully',
            'location' => route($this->model . '.index', $branch_id),
        ]);
    }

    public function destroy($branch_id, $id, Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (!isset($permission[$pmodule . '___delete']) && !$permission == 'superadmin') {
            return response()->json([
                'result' => -1,
                'message' => 'Restricted: You dont have enough permissions',
            ]);
        }
        $new = OrderDeliverySlots::findOrFail($id);
        $new->delete();
        return response()->json([
            'result' => 1,
            'message' => 'LPO Delivery Slots deleted successfully',
            'location' => route($this->model . '.index', $branch_id),
        ]);
    }

    public function book_lpo_slot(Request $request)
    {
        try {
            $validations = Validator::make($request->all(), [
                'date' => 'required|date',
                'time_slot' => 'required|max:23|min:0',
                'lpo' => 'required'
            ]);
            if ($validations->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validations->errors(),
                ]);
            }
            $order = WaPurchaseOrder::with('getSupplier')->where('id', $request->lpo)->firstOrFail();
            $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
            $post_data = [
                'date' => $request->date,
                'time_slot' => $request->time_slot,
                'lpo' => $order->purchase_no,
                'supplier_code' => $order->getSupplier->supplier_code,
                'supplier_email' => $order->getSupplier->email,
                'from' => env('SUPPLIER_SOURCE')
            ];

            $a = $api->postRequest('/api/lpo/book-delivery-slot', $post_data);
            $order->update([
                'slot_booked' => 1
            ]);

            return response()->json([
                'result' => 1,
                'message' => 'LPO Delivery Slot Booked Successfully',
                'location' => route($this->model . '.show_booked_slots'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }
}
