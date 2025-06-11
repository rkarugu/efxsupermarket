<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\NWaInventoryLocationTransfer;
use App\Model\NWaInventoryLocationTransferItem;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaStockMove;
use App\Model\WaAccountingPeriod;
use App\Model\WaNumerSeriesCode;
use App\Model\WaUnitOfMeasure;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaPurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\WaLocationStoreUom;
use Illuminate\Support\Facades\Auth;
use App\Models\WaPettyCashRequestItem;

class NInventoryLocationTransferController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'n-transfers';
        $this->title = 'Transfers';
        $this->pmodule = 'transfers';
    }

    public function getInventryItemDetails(Request $request)
    {

        $view = $this->getInventoryItemview($request->id, $request->location);

        return response()->json($view);
    }

    private function getInventoryItemview($id, $location, $item = null)
    {
        $data = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.*',
                'wa_inventory_location_stock_status.max_stock',
                'wa_inventory_location_stock_status.re_order_level',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($location ?? 'wa_inventory_items.store_location_id') . ') as quantity')
            ])
            ->with(['getTaxesOfItem', 'unitofmeasures', 'pack_size'])
            ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($location) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $location);
            })
            ->where('wa_inventory_items.id', $id)
            ->first();

        $qoo = WaPurchaseOrderItem::query()
            ->whereHas('getPurchaseOrder', function ($query) use ($location) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->doesntHave('grns');

                $query->where('wa_location_and_store_id',  $location);
            })->where('wa_inventory_item_id', $id)
            ->sum('quantity');

        $view = '';
        $uqid = uniqid();
        $binLocations = getUnitOfMeasureList();
        $store = $location;
        $exists = WaInventoryLocationUom::where('inventory_id', $id)->where('location_id', $store)->pluck('uom_id')->first();

        if ($data) {
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id[' . $uqid . ']" class="itemid" value="' . $data->id . '">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control"
                 value="' . $data->stock_id_code . '">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td><input style="padding: 3px 3px;" readonly type="text" 
                name="item_description[' . $uqid . ']" data-id="' . $data->id . '"  
                class="form-control" value="' . $data->description . '"></td>
               
        </td>
        <td>';
            if ($exists != null) {
                $binIdForItemInStoreName = WaUnitOfMeasure::find($exists);
                $name =  $binIdForItemInStoreName->title;
                $view .= '<input type="hidden" name="bin_location[' . $data->id . ']" value="' . $exists . '">';
                $view .= '' . $name;
            } else {
                $relevantBinLocationIds = WaLocationStoreUom::where('location_id', $location)
                    ->pluck('uom_id')
                    ->toArray();
                $view .= '<select required class="form-control select_bin_location mlselec6t" name="bin_location[' . $data->id . ']" id="bin_location[' . $data->id . ']">';
                $view .= '<option value="0">Select Bin Location</option>';
                foreach ($binLocations as $index => $option) {
                    if (in_array($index, $relevantBinLocationIds)) {
                        $selected = ($index == $data->wa_unit_of_measure_id) ? 'selected' : '';
                        $view .= '<option value="' . $index . '" ' . $selected . '>' . $option . '</option>';
                    }
                }
                $view .= '</select>';
            }

            $view .= '</td>           
            <td><input style="padding: 3px 3px;" readonly type="text" name="item_unit[' . $uqid . ']" data-id="' . $data->id . '"  class="form-control" value="' . ($data->pack_size->title ?? NULL) . '" readonly></td>
            <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)"  type="text" name="item_quantity[' . $uqid . ']" data-id="' . $data->id . '" data-weight="' . $data->net_weight . '" class="quantity form-control" value="' . ($item->quantity ?? 0) . '"></td>
            <td>' . number_format($qoo) . '</td>
            <td>' . number_format($data->quantity) . '</td>
            <td>' . number_format($data->max_stock) . '</td>
            <td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_standard_cost[' . $uqid . ']" data-id="' . $data->id . '" readonly class="standard_cost form-control" value="' . ($store->standard_cost ?? $data->standard_cost) . '"></td>
            <td><span class="exclusive">' . ($store->total_cost_with_vat ?? '') . '</span></td>
            <td><textarea class="form-control" cols="1" rows="1" name="comment[' .  $uqid . ']">' . ($store->note ?? '') . '</textarea></td>
            <td>
            <button type="button" class="btn btn-danger btn-sm deleteparent" style="background-color:transparent !important; border:none !important; color:red !important;">
                <i class="fas fa-trash" aria-hidden="true" style="color:red !important"></i></button>
            </td>
            </tr>';
        }

        return $view;
    }

    public function index(Request $request)
    {
        $pmodule = $this->pmodule;
        if (!can('view', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $lists  = NWaInventoryLocationTransfer::with('getrelatedEmployee');

        if ($request->has('start-date')) {
            $startDate = \Carbon\Carbon::parse($request->input('start-date'))->toDateString();
            $lists = $lists->whereDate('created_at', '>=', $startDate);
        }
        if ($request->has('end-date')) {
            $endDate = \Carbon\Carbon::parse($request->input('end-date'))->toDateString();
            $lists = $lists->whereDate('created_at', '<=', $endDate);
        }

        $lists = $lists->orderBy('id', 'desc')->get();

        $breadcum = [$this->title => route($this->model . '.index'), 'Listing' => ''];

        return view('admin.ninventorylocationtransfer..index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum,
            'lists' => $lists,
            'pmodule' => $pmodule
        ]);
    }

    public function indexReceive(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        // echo $permission[$pmodule.'___view']; die;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

            $lists  = DB::table('n_wa_inventory_location_transfers')->where('status', 'PENDING');

            if ($request->has('start-date')) {
                $startDate = \Carbon\Carbon::parse($request->input('start-date'))->toDateString();

                $lists = $lists->whereDate('created_at', '>=', $startDate);
            }
            if ($request->has('end-date')) {
                $endDate = \Carbon\Carbon::parse($request->input('end-date'))->toDateString();

                $lists = $lists->whereDate('created_at', '<=', $endDate);
            }

            $lists = $lists->orderBy('id', 'desc')->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.ninventorylocationtransfer.receive.list', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function indexProcessed(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        // echo $permission[$pmodule.'___view']; die;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

            $lists  = NWaInventoryLocationTransfer::with('getrelatedEmployee')->where('status', 'COMPLETED');

            if ($request->has('start-date')) {
                $startDate = \Carbon\Carbon::parse($request->input('start-date'))->toDateString();

                $lists = $lists->whereDate('created_at', '>=', $startDate);
            }
            if ($request->has('end-date')) {
                $endDate = \Carbon\Carbon::parse($request->input('end-date'))->toDateString();

                $lists = $lists->whereDate('created_at', '<=', $endDate);
            }

            $lists = $lists->orderBy('id', 'desc')->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.ninventorylocationtransfer.processed.list', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create()
    {
        if (!can('add', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $title = 'Initiate ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];

        $authuser = Auth::user();
        $user = User::where('id', $authuser->id)->with(['location_stores'])->first();
        $user_location = $user->location_stores;
        $role_id = $authuser->role_id;
        $isAdmin = $role_id == 1;
        $permission =  $this->mypermissionsforAModule();
        $can_view = isset($permission['maintain-items___view-per-branch']);

        $department = WaDepartment::pluck('department_name', 'id')->toArray();
        $all_sections = Restaurant::orderBy('name')->get();

        return view('admin.ninventorylocationtransfer.create', [
            // 'transfer_no' => getCodeWithNumberSeries('TRAN'),
            'transfer_date' => date('Y-m-d'),
            'user' => getLoggeduserProfile(),
            'department' => $department,
            'title' => $title,
            'model' => $model,
            'breadcum' => $breadcum,
            'user_location' => $user_location,
            'isAdmin' => $isAdmin,
            'can_view' => $can_view,
            'all_sections' => $all_sections
        ]);
    }

    public function print(Request $request)
    {
        $list =   NWaInventoryLocationTransfer::where('transfer_no', $request->transfer_no)->first();
        return view('admin.ninventorylocationtransfer..print', compact('title', 'list'));
    }

    public function refreshstockmoves()
    {

        $list =   NWaInventoryLocationTransfer::select('transfer_no', 'shift_id')->get();
        foreach ($list as $key => $val) {
            WaStockMove::where('document_no', $val->transfer_no)->update(['shift_id' => $val->shift_id]);
        }
        Session::flash('success', 'Refreshed successfully.');
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $user = Auth::user();


        if (!can('add', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        try{

        $validator = Validator::make($request->all(), [
            // 'transfer_no' => 'required|unique:n_wa_inventory_location_transfers',
            // 'wa_department_id' => 'required|exists:wa_departments,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'from_strore_location_id' => 'required|exists:wa_location_and_stores,id',
            'to_store_location_id' => 'required|exists:wa_location_and_stores,id|different:from_strore_location_id',
            'item_id' => 'required|array',
            'item_id.*' => 'exists:wa_inventory_items,id',
            'item_quantity.*' => 'required|numeric|min:1',
            'comment.*' => 'nullable|max:150'
        ], [], [
            // 'wa_department_id' => 'department',
            'restaurant_id' => 'branch',
            'from_strore_location_id' => 'from store',
            'to_store_location_id' => 'to store',
            'item_id' => 'item'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ], 422);
        }
        //allow only transfers from Nampark and exempt Mr. Njuguna and Edward
        // if($user->id != 388 && $user->role_id != 1 && $user->id != 381){
        //     if($request->from_strore_location_id != 46){
        //         return response()->json(['result' => 0, 'errors' => ['from_strore_location_id' => ['Only transfers from Nampark are allowed']]]);
        //     }

        // }
        

        $inventory = WaInventoryItem::with(['getUnitOfMeausureDetail', 'getTaxesOfItem'])->select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_location_and_store_id = ' . $request->from_strore_location_id . ' AND wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->whereIn('id', array_unique($request->item_id))->get();

        if (count($inventory) == 0) {
            return response()->json(['result' => 0, 'errors' => ['testIn' => ['Add items to proceed']]]);
        }
        $errors = [];
        if($request->from_strore_location_id != 38){
            if ($request->from_strore_location_id == 46 || $request->from_strore_location_id == 37) {
                foreach ($request->item_id as $key => $value) {
                    $item_detail = $inventory->where('id', $value)->first();
                    if ($request->item_quantity[$key] > $item_detail->quantity) {
                        $errors['item_quantity.' . $key] = ['The Quanity entered is greater than the remaining stock balance'];
                    }
                }
            }

        }
       

        if (count($errors) > 0) {
            return response()->json(['result' => 0, 'errors' => $errors], 500);
        }

        foreach ($request->bin_location as $key => $value) {
            if ($value != 0) {
                $inventoryItem =  WaInventoryItem::find($key);
                $inventoryItem->wa_unit_of_measure_id = $value;
                $inventoryItem->save();
            }
        }

        $check = DB::transaction(function () use ($inventory, $request) {
            $transfer_no = getCodeWithNumberSeries('TRAN');
            $row = new NWaInventoryLocationTransfer();
            $row->transfer_no = $transfer_no;
            $row->manual_doc_number = $request->manual_doc_number;
            $row->transfer_date = date('Y-m-d');
            $row->restaurant_id = $request->restaurant_id;
            $row->wa_department_id = $request->wa_department_id;
            $row->user_id = getLoggeduserProfile()->id;
            $row->from_store_location_id = $request->from_strore_location_id;
            $row->to_store_location_id = $request->to_store_location_id;
            $row->status = $request->action == 'save' ? 'DRAFT' : 'PENDING';
            $row->save();

            $date = date('Y-m-d H:i:s');
            $WaInventoryLocationTransferItem = [];

            foreach ($request->item_id as $key => $val) {
                $item_detail = $inventory->where('id', $val)->first();
                $total_cost = $item_detail->standard_cost * $request->item_quantity[$key];
                $vat_rate = 0;
                $vat_amount = 0;
                if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                    $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                    if ($total_cost > 0) {
                        $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $total_cost) / 100;
                    }
                }
                $WaInventoryLocationTransferItem[] = [
                    'wa_inventory_location_transfer_id' => $row->id,
                    'wa_inventory_item_id' => $val,
                    'quantity' => $request->item_quantity[$key],
                    'note' => $request->comment[$key],
                    'standard_cost' => $item_detail->standard_cost,
                    'total_cost' => $total_cost,
                    'vat_rate' => $vat_rate,
                    'vat_amount' => $vat_amount,
                    'total_cost_with_vat' => $total_cost + $vat_amount,
                    'created_at' => $date,
                    'updated_at' => $date
                ];
            }

            NWaInventoryLocationTransferItem::insert($WaInventoryLocationTransferItem);

            updateUniqueNumberSeries('TRAN', $transfer_no);

            $row =  NWaInventoryLocationTransfer::with([
                'getRelatedItem',
                'getRelatedItem.getInventoryItemDetail',
                'getRelatedItem.getInventoryItemDetail.getAllFromStockMoves',
                'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
                'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail'
            ])->where('transfer_no', $transfer_no)->first();

            $series_module = WaNumerSeriesCode::where('module', 'TRAN')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $user = getLoggeduserProfile();

            //TODO: undo this
            //prevent negative  stocks
            if (($request->from_strore_location_id == 46 || $request->from_strore_location_id == 37) && $request->action == 'process') {
                foreach ($row->getRelatedItem as $item) {
                    $delivery_quantity = 'delivered_quantity_' . $item;
                    $stock_qoh = $item->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', $row->from_store_location_id)->sum('qauntity') ?? 0;
                    $stock_qoh -= $item->quantity;
                    $fromStoreLocation = WaLocationAndStore::where('id', $request->from_strore_location_id)->value('location_name');
                    $toStoreLocation = WaLocationAndStore::where('id', $request->to_store_location_id)->value('location_name');
                

                    $from_entry = new WaStockMove();
                    $from_entry->user_id = $user->id;
                    $from_entry->wa_inventory_location_transfer_new_id = $row->id;
                    $from_entry->restaurant_id = $row->restaurant_id;
                    $from_entry->wa_location_and_store_id = $row->from_store_location_id;
                    $from_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                    $from_entry->wa_inventory_item_id = $item->getInventoryItemDetail->id;
                    // $from_entry->qauntity = '-'.$item->quantity;
                    $from_entry->qauntity = $item->quantity * -1;
                    $from_entry->new_qoh = $stock_qoh;
                    $from_entry->standard_cost = $item->standard_cost;
                    $from_entry->price = $item->standard_cost;
                    $from_entry->selling_price = $item->getInventoryItemDetail->selling_price;
                    $from_entry->refrence = 'Trans/' . $fromStoreLocation . '-to-' . $toStoreLocation;
                    $from_entry->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                    $from_entry->document_no = $transfer_no;
                    $from_entry->save();
                }
            }
            return true;
        });

        if ($check) {
            return response()->json(['result' => 1, 'message' => 'Transfer Created Successfully', 'location' => route($this->model . '.index')], 200);
        }

        // return response()->json(['result' => -1, 'message' => 'Something went wrong'], 500);
    }catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

    public function receiveInterBranchTransfer($id)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $interBranchTransfer = NWaInventoryLocationTransfer::find($id);
            $interBranchTransferItems =  NWaInventoryLocationTransferItem::with(['getInventoryItemDetail'])->where('wa_inventory_location_transfer_id', $id)->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.ninventorylocationtransfer.receive', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'interBranchTransfer', 'interBranchTransferItems'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function receiveInterBranchTransfer2($id)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $interBranchTransfer = NWaInventoryLocationTransfer::find($id);
            $interBranchTransferItems =  NWaInventoryLocationTransferItem::with(['getInventoryItemDetail'])->where('wa_inventory_location_transfer_id', $id)->get();
            $transferLocation = $interBranchTransfer->to_store_location_id;
            $transferItems = $interBranchTransferItems->pluck('wa_inventory_item_id')->toArray();
            $binLocations = [];
            foreach ($transferItems as $itemid) {
                $uom = WaInventoryLocationUom::where('inventory_id', $itemid)
                    ->where('location_id', $transferLocation)
                    ->pluck('uom_id');
                $binLocation = WaUnitOfMeasure::whereIn('id', $uom)->pluck('title')->first();
                $binLocations[$itemid] = $binLocation;
            }
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $relevantBinLocationIds = WaLocationStoreUom::where('location_id', $transferLocation)
                ->pluck('uom_id')
                ->toArray();
            $unitmeasures = WaUnitOfMeasure::whereIn('id', $relevantBinLocationIds)->get();
            return view('admin.ninventorylocationtransfer.receive.receive', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'interBranchTransfer', 'unitmeasures', 'interBranchTransferItems', 'binLocations'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function updateUnitOfMeasure(Request $request, $id)

    {
        $storeLocation = $request->store;
        $uomId = $request->wa_unit_of_measure_id;
        $inventoryId = NWaInventoryLocationTransferItem::where('id', $id)->pluck('wa_inventory_item_id')->first();
        $uomRecord = WaInventoryLocationUom::where('inventory_id', $inventoryId)
            ->where('location_id', $storeLocation)
            ->first();
        if ($uomRecord) {
            $uomRecord->uom_id = $uomId;
            $uomRecord->updated_at = now();
            $uomRecord->save();
        } else {
            $uomRecord = new WaInventoryLocationUom();
            $uomRecord->inventory_id = $inventoryId;
            $uomRecord->location_id = $storeLocation;
            $uomRecord->uom_id = $uomId;
            $uomRecord->created_at = now();
            $uomRecord->updated_at = now();
            $uomRecord->save();
        }

        return redirect()->back()->with('success', 'Bin location updated successfully.');
    }


    public function receiveInterBranchTransferProcessed($id)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $interBranchTransfer = NWaInventoryLocationTransfer::find($id);
            $interBranchTransferItems =  NWaInventoryLocationTransferItem::with(['getInventoryItemDetail'])->where('wa_inventory_location_transfer_id', $id)->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.ninventorylocationtransfer.processed.view', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'interBranchTransfer', 'interBranchTransferItems'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function processReceiveInterBranchTransfer($transferId)
    {
        try {
            $transfer_no = $transferId;

            $row =  NWaInventoryLocationTransfer::where('status', 'PENDING')->where('transfer_no', $transfer_no)->first();
            if ($row) {


                $internal_requisition_row =  NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)->first();
                $series_module = WaNumerSeriesCode::where('module', 'TRAN')->first();
                $intr_smodule = WaNumerSeriesCode::where('module', 'TRAN')->first();
                $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                $dateTime = date('Y-m-d H:i:s');

                if ($row->to_store_location_id == 37 || $row->to_store_location_id == 46 ) {
                    foreach ($row->getRelatedItem as $item) {
                        $delivery_quantity = 'delivered_quantity_' . $item;
                        $inventoryItem = WaInventoryItem::find($item->wa_inventory_item_id);
                        $stock_qoh = WaStockMove::where('stock_id_code', $inventoryItem->stock_id_code)->where('wa_location_and_store_id', $row->to_store_location_id)->sum('qauntity') ?? 0;
                        $stock_qoh += $item->quantity;
                        $fromStoreLocation = WaLocationAndStore::where('id', $row->from_store_location_id)->value('location_name');
                        $toStoreLocation = WaLocationAndStore::where('id', $row->to_store_location_id)->value('location_name');

                        //check for duplicates 
                        $duplicateEntry = WaStockMove::where('qauntity', $item->quantity)
                            ->where('wa_location_and_store_id', $row->to_store_location_id)
                            ->where('wa_inventory_location_transfer_id', $row->id)
                            ->where('document_no', $transfer_no)
                            ->where('stock_id_code', $item->getInventoryItemDetail->stock_id_code)
                            ->first();
                        if($duplicateEntry){
                            continue;
                        }

                        $to_entry = new WaStockMove();
                        $to_entry->user_id = getLoggeduserProfile()->id;
                        $to_entry->wa_inventory_location_transfer_id = $row->id;
                        $to_entry->restaurant_id = $row->restaurant_id;
                        $to_entry->wa_location_and_store_id = $row->to_store_location_id;
                        $to_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                        $to_entry->wa_inventory_item_id = $item->getInventoryItemDetail->id;
                        $to_entry->qauntity = $item->quantity;
                        $to_entry->new_qoh = $stock_qoh;
                        $to_entry->standard_cost = $item->standard_cost;
                        $to_entry->price = $item->standard_cost;
                        $to_entry->selling_price = $item->getInventoryItemDetail->selling_price;
                        $to_entry->refrence = 'Trans/'. $fromStoreLocation .'-to-' . $toStoreLocation;
                        $to_entry->document_no = $transfer_no;
                        $to_entry->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $to_entry->save();
                    }
                }

                $row->status = 'COMPLETED';
                $row->save();
                Session::flash('success', 'Transfer received successfully.');
                return redirect()->route($this->model . '.index');
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function checkQtyWithHandForAll($inventoryTransfer)
    {

        $item_withqty = [];
        foreach ($inventoryTransfer->getRelatedItem as $item_required) {

            if (isset($item_withqty[$item_required->wa_inventory_item_id])) {
                $item_withqty[$item_required->wa_inventory_item_id] = $item_withqty[$item_required->wa_inventory_item_id] + $item_required->quantity;
            } else {
                $item_withqty[$item_required->wa_inventory_item_id] = $item_required->quantity;
            }
        }





        $error = '';
        foreach ($item_withqty as $key => $value) {


            $item = WaInventoryItem::select('stock_id_code', 'id')->where('id', $key)->first();
            $qtyOnHand = WaStockMove::where('wa_location_and_store_id', $inventoryTransfer->from_store_location_id)
                ->where('stock_id_code', $item->stock_id_code)
                ->sum('qauntity');
            if ($value <= $qtyOnHand) {
            } else {
                if ($error == '') {
                    $error = $item->stock_id_code . ' have only ' . $qtyOnHand;
                } else {
                    $error .= ', ' . $item->stock_id_code . ' have only ' . $qtyOnHand;
                }
            }
        }
        if ($error == '') {
            return 'ok';
        } else {
            return 'ok';
            //            return $error;
        }
    }

    public function processTransfer($transfer_no)
    {
        try {
            $checkcount =  NWaInventoryLocationTransfer::where('status', 'PENDING')->where('transfer_no', $transfer_no)->count();

            if ($checkcount > 1) {
                Session::flash('warning', 'Already exist transfer no.');
                return redirect()->back();
            }

            $row =  NWaInventoryLocationTransfer::where('status', 'PENDING')->where('transfer_no', $transfer_no)->first();
            if ($row) {
                $qtyStatus =  $this->checkQtyWithHandForAll($row);
                if ($qtyStatus == 'ok') {
                    $row->status = 'COMPLETED';
                    $row->save();
                    $internal_requisition_row =  NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)->first();
                    $series_module = WaNumerSeriesCode::where('module', 'TRAN')->first();
                    $intr_smodule = WaNumerSeriesCode::where('module', 'TRAN')->first();
                    $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                    $dateTime = date('Y-m-d H:i:s');
                    foreach ($row->getRelatedItem as $item) {
                        $delivery_quantity = 'delivered_quantity_' . $item;
                        $from_entry = new WaStockMove();
                        $from_entry->user_id = getLoggeduserProfile()->id;
                        $from_entry->wa_inventory_location_transfer_id = $row->id;
                        $from_entry->restaurant_id = $row->restaurant_id;
                        $from_entry->wa_location_and_store_id = $row->from_store_location_id;
                        $from_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                        $from_entry->wa_inventory_item_id = $item->item;
                        // $from_entry->qauntity = '-'.$item->quantity;
                        $from_entry->qauntity = $item->quantity * -1;
                        $from_entry->standard_cost = $item->standard_cost;
                        $from_entry->price = $item->standard_cost;
                        $from_entry->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $from_entry->document_no = $transfer_no;
                        $from_entry->save();

                        $to_entry = new WaStockMove();
                        $to_entry->user_id = getLoggeduserProfile()->id;
                        $to_entry->wa_inventory_location_transfer_id = $row->id;
                        $to_entry->restaurant_id = $row->restaurant_id;
                        $to_entry->wa_location_and_store_id = $row->to_store_location_id;
                        $to_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                        $to_entry->wa_inventory_item_id = $item->getInventoryItemDetail->id;
                        $to_entry->qauntity = $item->quantity;
                        $to_entry->standard_cost = $item->standard_cost;
                        $to_entry->price = $item->standard_cost;
                        $to_entry->document_no = $transfer_no;
                        $to_entry->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $to_entry->save();
                    }
                    Session::flash('success', 'Transfered successfully.');
                    return redirect()->route($this->model . '.index');
                } else {
                    Session::flash('warning', $qtyStatus);
                    return redirect()->back();
                }
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function show($slug)
    {

        $row =  NWaInventoryLocationTransfer::whereSlug($slug)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            return view('admin.ninventorylocationtransfer..show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function edit($slug)
    {
        if (!can('edit', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $transfer =  NWaInventoryLocationTransfer::whereSlug($slug)->first();
        $items = [];
        foreach ($transfer->getRelatedItem as $item) {
            $items[] = $this->getInventoryItemview($item->wa_inventory_item_id, $transfer->from_store_location_id, $item);
        }

        $transfer->items = $items;

        $title = 'Edit ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];

        $department = WaDepartment::pluck('department_name', 'id')->toArray();

        return view('admin.ninventorylocationtransfer..edit', [
            'transfer_no' => getCodeWithNumberSeries('TRAN'),
            'transfer_date' => date('Y-m-d'),
            'user' => getLoggeduserProfile(),
            'department' => $department,
            'title' => $title,
            'model' => $model,
            'breadcum' => $breadcum,
            'transfer' => $transfer,
        ]);
    }


    public function update(Request $request, $slug)
    {
        if (!can('edit', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $validator = Validator::make($request->all(), [
            'wa_department_id' => 'required|exists:wa_departments,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'from_strore_location_id' => 'required|exists:wa_location_and_stores,id',
            'to_store_location_id' => 'required|exists:wa_location_and_stores,id|different:from_strore_location_id',
            'item_id' => 'required|array',
            'item_id.*' => 'exists:wa_inventory_items,id',
            'item_quantity.*' => 'required|numeric|min:1',
            'comment.*' => 'nullable|max:150'
        ], [], [
            'wa_department_id' => 'department',
            'restaurant_id' => 'branch',
            'from_strore_location_id' => 'from store',
            'to_store_location_id' => 'to store',
            'item_id' => 'item'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }

        $inventory = WaInventoryItem::with(['getUnitOfMeausureDetail', 'getTaxesOfItem'])->select([
            '*',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_location_and_store_id = ' . $request->from_strore_location_id . ' AND wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code) as quantity'),
        ])->whereIn('id', array_unique($request->item_id))->get();

        if (count($inventory) == 0) {
            return response()->json(['result' => 0, 'errors' => ['testIn' => ['Add items to proceed']]]);
        }

        $errors = [];
        //TODO: return check after  transfers.
        if($request->from_strore_location_id != 38){
            if ($request->from_strore_location_id == 46 || $request->from_strore_location_id == 37) {
                foreach ($request->item_id as $key => $value) {
                    $item_detail = $inventory->where('id', $value)->first();
                    if ($request->item_quantity[$key] > $item_detail->quantity) {
                        $errors['item_quantity.' . $key] = ['The Quanity entered is greater than the remaining stock balance'];
                    }
                }
            }
        }

       

        if (count($errors) > 0) {
            return response()->json(['result' => 0, 'errors' => $errors]);
        }

        foreach ($request->bin_location as $key => $value) {
            if ($value != 0) {
                $inventoryItem =  WaInventoryItem::find($key);
                $inventoryItem->wa_unit_of_measure_id = $value;
                $inventoryItem->save();
            }
        }

        $check = DB::transaction(function () use ($inventory, $request, $slug) {
            $row = NWaInventoryLocationTransfer::where('slug', $slug)->firstOrFail();
            $row->manual_doc_number = $request->manual_doc_number;
            $row->restaurant_id = $request->restaurant_id;
            $row->wa_department_id = $request->wa_department_id;
            $row->from_store_location_id = $request->from_strore_location_id;
            $row->to_store_location_id = $request->to_store_location_id;
            $row->status = $request->action == 'save' ? 'DRAFT' : 'PENDING';
            $row->save();

            $date = date('Y-m-d H:i:s');
            $WaInventoryLocationTransferItem = [];

            $row->getRelatedItem()->delete();

            foreach ($request->item_id as $key => $val) {
                $item_detail = $inventory->where('id', $val)->first();
                $total_cost = $item_detail->standard_cost * $request->item_quantity[$key];
                $vat_rate = 0;
                $vat_amount = 0;
                if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                    $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                    if ($total_cost > 0) {
                        $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $total_cost) / 100;
                    }
                }
                $WaInventoryLocationTransferItem[] = [
                    'wa_inventory_location_transfer_id' => $row->id,
                    'wa_inventory_item_id' => $val,
                    'quantity' => $request->item_quantity[$key],
                    'note' => $request->comment[$key],
                    'standard_cost' => $item_detail->standard_cost,
                    'total_cost' => $total_cost,
                    'vat_rate' => $vat_rate,
                    'vat_amount' => $vat_amount,
                    'total_cost_with_vat' => $total_cost + $vat_amount,
                    'created_at' => $date,
                    'updated_at' => $date
                ];
            }

            NWaInventoryLocationTransferItem::insert($WaInventoryLocationTransferItem);

            $row =  NWaInventoryLocationTransfer::with([
                'getRelatedItem',
                'getRelatedItem.getInventoryItemDetail',
                'getRelatedItem.getInventoryItemDetail.getAllFromStockMoves',
                'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
                'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail'
            ])->where('transfer_no', $request->transfer_no)->first();

            $transfer_no = $request->transfer_no;
            $series_module = WaNumerSeriesCode::where('module', 'TRAN')->first();
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $user = getLoggeduserProfile();

            //TODO: undo this
            //prevent negative  stocks
            if (($request->from_strore_location_id == 46 || $request->from_strore_location_id == 37) && $request->action == 'process') {
                foreach ($row->getRelatedItem as $item) {
                    $delivery_quantity = 'delivered_quantity_' . $item;
                    $stock_qoh = $item->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', $row->from_store_location_id)->sum('qauntity') ?? 0;
                    $stock_qoh -= $item->quantity;
                    $fromStoreLocation = WaLocationAndStore::where('id', $request->from_strore_location_id)->value('location_name');
                    $toStoreLocation = WaLocationAndStore::where('id', $request->to_store_location_id)->value('location_name');

                    $from_entry = new WaStockMove();
                    $from_entry->user_id = $user->id;
                    $from_entry->wa_inventory_location_transfer_new_id = $row->id;
                    $from_entry->restaurant_id = $row->restaurant_id;
                    $from_entry->wa_location_and_store_id = $row->from_store_location_id;
                    $from_entry->stock_id_code = $item->getInventoryItemDetail->stock_id_code;
                    $from_entry->wa_inventory_item_id = $item->getInventoryItemDetail->id;
                    // $from_entry->qauntity = '-'.$item->quantity;
                    $from_entry->qauntity = $item->quantity * -1;
                    $from_entry->new_qoh = $stock_qoh;
                    $from_entry->standard_cost = $item->standard_cost;
                    $from_entry->price = $item->standard_cost;
                    $from_entry->selling_price = $item->getInventoryItemDetail->selling_price;
                    $from_entry->refrence = 'Trans/'. $fromStoreLocation .'-to-' . $toStoreLocation;
                    $from_entry->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                    $from_entry->document_no = $transfer_no;
                    $from_entry->save();
                }
            }

            return true;
        });

        if ($check) {
            return response()->json(['result' => 1, 'message' => 'Transfer Processed Successfully', 'location' => route($this->model . '.index')]);
        }

        return response()->json(['result' => -1, 'message' => 'Something went wrong']);
    }


    public function destroy($slug)
    {
        if (!can('delete', $this->pmodule)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        NWaInventoryLocationTransfer::whereSlug($slug)->delete();
        Session::flash('success', 'Deleted successfully.');

        return redirect()->back();
    }

    public function getDapartments(Request $request)
    {
        $rows = WaDepartment::where('restaurant_id', $request->branch_id)->orderBy('department_name', 'asc')->get();
        $data = '<option  value="">Please select department</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->department_name . '</option>';
        }

        return $data;
    }

    public function getItems(Request $request)
    {
        $rows = WaInventoryItem::where('wa_inventory_category_id', $request->selected_inventory_category)->orderBy('title', 'asc')->get();
        $data = '<option  value="">Please select item</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->title . '</option>';
        }

        return $data;
    }

    public function getItemDetail(Request $request)
    {
        $rows = WaInventoryItem::where('id', $request->selected_item_id)->first();
        return json_encode(['stock_id_code' => $rows->stock_id_code, 'unit_of_measure' => $rows->wa_unit_of_measure_id ? $rows->wa_unit_of_measure_id : '', 'minimum_order_quantity' => $rows->minimum_order_quantity]);
    }
    public function deletingItemRelation($purchase_no, $id)
    {
        try {
            NWaInventoryLocationTransferItem::whereId($id)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }





    public function printToPdf($transfer_no)
    {
        $list =   NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)->with(['getRelatedItem.getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->first();


        $itemsdata =   NWaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id', $list->id)->with(['getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->get();


        $pdf = PDF::loadView('admin.ninventorylocationtransfer..print', compact('list', 'itemsdata'));
        return $pdf->download('transfer_' . date('Y_m_d_h_i_s') . '.pdf');
    }
    public function printPdf($transfer_no)
    {
        $list =   NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)->with(['getRelatedItem.getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->first();


        $itemsdata =   NWaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id', $list->id)->with(['getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->get();

        //echo "<pre>"; print_r($itemsdata); die;			
        //return view('admin.ninventorylocationtransfer..print',compact('title','list')); 

        $pdf = PDF::loadView('admin.ninventorylocationtransfer..print', compact('list', 'itemsdata'));
        return $pdf->setPaper('a4')
            ->setWarnings(false)
            ->download('transfer_' . date('Y_m_d_h_i_s') . '.pdf');
    }



    public function editPurchaseItem($transfer_no, $id)
    {
        try {

            $row =  NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)
                ->whereHas('getRelatedItem', function ($sql_query) use ($id) {
                    $sql_query->where('id', $id);
                })

                ->first();
            if ($row) {

                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), $row->purchase_no => '', 'Edit' => ''];
                $model = $this->model;


                $form_url = [$model . '.updatePurchaseItem', $row->getRelatedItem->find($id)->id];
                return view('admin.ninventorylocationtransfer..editItem', compact('title', 'model', 'breadcum', 'row', 'id', 'form_url'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function updatePurchaseItem(Request $request, $id)
    {
        try {


            $item =  NWaInventoryLocationTransferItem::where('id', $id)->first();

            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item_detail->standard_cost * $request->quantity;
            $vat_rate = 0;
            $vat_amount = 0;
            if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                if ($item->total_cost > 0) {
                    $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $item->total_cost) / 100;
                }
            }
            $item->vat_rate = $vat_rate;
            $item->vat_amount = $vat_amount;
            $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.edit', $item->getTransferLocation->slug);
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function checkQuantity(Request $request)
    {
        try {
            $qtyOnHand = WaStockMove::where('wa_location_and_store_id', $request->from_strore_location_id)->where('stock_id_code', $request->item_id)->sum('qauntity');
            // echo $qtyOnHand; die;
            $item = WaInventoryItem::select('stock_id_code', 'id')->where('stock_id_code', $request->item_id)->first();

            $item_id = $item->id;


            $myqty = $request->quantity;
            $qtyOnHand = $qtyOnHand;
            if ($myqty <= $qtyOnHand) {
                return '1';
            } else {

                return '1';
            }
        } catch (\Exception $e) {

            return '1';
        }
    }



    public function getManualItemsList(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->get('type');
        } else {
            $type = '';
        }
        $view_data = view('admin.ninventorylocationtransfer..manual_entry', compact('type'));
        return $view_data;
    }

    public function transfersByBranch($branchId)
    {
        $requestedTransfers = WaPettyCashRequestItem::whereNotNull('transfer_id')
            ->whereHas('pettyCashRequest', fn ($query) => $query->where('rejected', false))
            ->select('transfer_id')
            ->pluck('transfer_id')
            ->toArray();

        $transfers = NWaInventoryLocationTransfer::query()
            // ->where('restaurant_id', $branchId)
            ->whereNotIn('id', $requestedTransfers)
            ->select('id', 'transfer_no')
            ->latest()
            ->get();

        return response()->json($transfers);
    }
}
