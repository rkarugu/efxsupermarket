<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Model\WaUnitOfMeasure;
use App\Models\ReverseSplit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReverseSplitsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'reverse-splits';
        $this->title = 'Reverse Splits';
        $this->pmodule = 'reverse-splits';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = Auth::user();
        $from = $request->date ? Carbon::parse($request->date)->startOfDay() :  Carbon::now()->startOfDay();
        $to = $request->todate ? Carbon::parse($request->todate)->endOfDay() :  Carbon::now()->endOfDay();
        $branches = WaLocationAndStore::all();
        $reverseSplits = ReverseSplit::query();

        if($request->date  && $request->todate){
            $reverseSplits = $reverseSplits->whereBetween('created_at', [$from, $to]);
        }
        $is_display = true;
        $is_mother_bin = false;
        if($user->role_id != 1){
            $reverseSplites = $reverseSplits->where('wa_location_and_store_id', $user->wa_location_and_store_id);

        }
        if($user->role_id == 152){
            $is_display = WaUnitOfMeasure::find($user->wa_unit_of_measures_id)->is_display;
            if(!$is_display)
            {
                $is_mother_bin = true;
            }
            $reverseSplits = $reverseSplits->where('mother_item_bin', $user->wa_unit_of_measures_id)
                ->orwhere('child_item_bin', $user->wa_unit_of_measures_id);
        }
        // if($request->branch){
        //     $reverseSplits = $reverseSplits->where('wa_location_and_store_id', $request->branch);
        // }
        $reverseSplits = $reverseSplits->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

           
            $breadcum = [$title => route('reverse-splitting.index'), 'Listing' => ''];
            return view('admin.reverse_splits.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission','branches', 'reverseSplits', 'is_display', 'is_mother_bin'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function create(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route('reverse-splitting.index'), 'Listing' => ''];
            return view('admin.reverse_splits.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $validations = Validator::make($request->all(),[
                'time' => 'required',
                'break_no' => 'required',
                'item_id' => 'required|array',
                'item_id.*' => 'required|exists:wa_inventory_items,id',
                'alternateid.*' => 'required|exists:wa_inventory_items,id',
                'source_qty.*' => 'required|numeric|min:1',
                'conversion_factor.*' => 'required|numeric|min:1',
            ],[],[
                'item_id.*'=>'Source Item',
                'alternateid.*'=>'Destination Item',
                'source_qty.*'=>'Source Qty',
                'conversion_factor.*'=>'Conversion Factor',
            ]);
            if($validations->fails()){
                return response()->json([
                    'result' => 0,
                    'errors' => $validations->errors()
                ]);
            }

            $childItemsArray = array_values($request->alternateid);
            $item_id = WaInventoryItem::select([
                '*',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.stock_id_code = wa_inventory_items.stock_id_code and wa_stock_moves.wa_location_and_store_id ='.$user->wa_location_and_store_id.' ) as quantity'),
            ])->whereIn('id', $childItemsArray);
            $error = [];
            foreach ($request->alternateid as $key => $value) {
                $split = DB::table('wa_stock_breaking')->where('wa_stock_breaking.breaking_code', $request->break_no)->first();
                if(!$split){
                    return response()->json([
                        'result' => -1,
                        'message' => 'Provided stock break number does not exist',
                     'errors' => ['Error' => 'Provided stock break number does not exist'],
                     ]);
                }
                $childItem = $item_id->where('wa_inventory_items.id', $value)->first();
                $stockBreaking = DB::table('wa_stock_breaking_items')
                    ->select(
                        'wa_stock_breaking_items.id',
                        'wa_stock_breaking_items.destination_qty'
                    )
                    ->leftJoin('wa_stock_breaking', 'wa_stock_breaking.id', 'wa_stock_breaking_items.wa_stock_breaking_id')
                    ->where('wa_stock_breaking.breaking_code', $request->break_no)
                    ->where('wa_stock_breaking_items.destination_item_id', $childItem->id)
                    ->first();
                if(!$stockBreaking){
                    return response()->json([
                        'result' => -1,
                        'message' => 'Items not in provided split code',
                     'errors' => ['Error' => 'Items not in provided split code'],
                     ]);
                }
                if($stockBreaking->destination_qty < $request->source_qty[$key]){
                    return response()->json([
                        'result' => -1,
                        'message' => 'Child Qty is more than split qty',
                     'errors' => ['Error' => 'Child Qty is more than split qty'],
                     ]);

                }
 
                if (!in_array($value, $childItemsArray)) {
                    $error['alternateid.'.$value] = ['Destination Item is required'];
                }
                if(!isset($request->source_qty[$key]) || $request->source_qty[$key] == '' || $childItem->quantity < $request->source_qty[$key]){
                    $error['source_qty.'.$childItem->stock_id_code] = ['Source Item don\'t have enough quantity'];
                }
                if(!isset($request->conversion_factor[$key]) || $request->conversion_factor[$key] == ''){
                    $error['conversion_factor.'.$childItem->stock_id_code] = ['Conversion factor is required'];
                }
            }
            if(count($error) > 0){
                return response()->json([
                    'result' => 0,
                    'errors' => $error
                ]);
            }
            DB::beginTransaction();
            foreach ($request->alternateid as $key => $value) {        
                $childItem = $item_id->where('wa_inventory_items.id', $value)->first();            
                $totalQty = ($request->conversion_factor[$key] * $request->source_qty[$key]);
                $childItem = WaInventoryItem::find($request->alternateid[$key]);
                $availableChildQty = WaStockMove::where('stock_id_code', $childItem->stock_id_code)->where('wa_location_and_store_id', $user->wa_location_and_store_id)->sum('qauntity');
                $existingPendingSplit = ReverseSplit::where('mother_item_id', $key)
                    ->where('child_item_id', $request->alternateid[$key])
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)
                    ->where('status', 'pending')
                    ->first();
                if($existingPendingSplit){
                    DB::rollBack();
                    return response()->json([
                       'result' => -1,
                       'message' => 'A similar pending split exists',
                    'errors' => ['Error' => 'A similar pending split exists'],
                    ]);
                }
                if($availableChildQty < $totalQty){
                    DB::rollBack();
                    return response()->json([
                       'result' => -1,
                       'message' => 'Child Item does not have enough quantity',
                    'errors' => ['Error' => 'Not enough available child quantity'],
                    ]);
                }
                $mother_item_id = $key;
                $mother_qty = $request->source_qty[$key];
                $child_item_id = $request->alternateid[$key];
                $conversion_factor = $request->conversion_factor[$key];
                $reverseSplit = new ReverseSplit();
                $reverseSplit->mother_item_id = $mother_item_id;
                $reverseSplit->mother_item_bin = WaInventoryLocationUom::where('inventory_id', $mother_item_id)->where('location_id', $user->wa_location_and_store_id)->first()->uom_id;
                $reverseSplit->child_item_id = $child_item_id;
                $reverseSplit->child_item_bin = WaInventoryLocationUom::where('inventory_id', $child_item_id)->where('location_id', $user->wa_location_and_store_id)->first()->uom_id;
                $reverseSplit->requested_child_quantity = $totalQty;
                $reverseSplit->expected_mother_quantity = $mother_qty;
                $reverseSplit->requested_by = $user->id;
                $reverseSplit->wa_location_and_store_id = $user->wa_location_and_store_id;
                $reverseSplit->save();
            }
            DB::commit();
            return response()->json([
                'result' => 1,
                'message' => 'Reverse Split Initiated Successfully',
                'location' => route('reverse-splitting.index'),
            ]);        
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
               'result' => -1,
               'message' => $th->getMessage(),
            'errors' => ['Error' => $th->getMessage()],
            ]);
            // return redirect()->back()->with('warning', $th->getMessage());
        }

        
    }

    public function approve($id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = Auth::user();
        if (isset($permission[$pmodule . '___approve']) || $permission == 'superadmin') {
            DB::beginTransaction();
            try {
                $reverseSplit = ReverseSplit::find($id);
                $childItem = WaInventoryItem::find($reverseSplit->child_item_id);
                $childQoh = WaStockMove::where('stock_id_code', $childItem->stock_id_code)
                    ->where('wa_location_and_store_id', $reverseSplit->wa_location_and_store_id)
                    ->sum('qauntity');
                $motherItem = WaInventoryItem::find($reverseSplit->mother_item_id);
                $motherQoh =   WaStockMove::where('stock_id_code', $motherItem->stock_id_code)
                    ->where('wa_location_and_store_id', $reverseSplit->wa_location_and_store_id)
                    ->sum('qauntity');
                if ($childQoh < $reverseSplit->requested_child_quantity) {
                    $split_no = getCodeWithNumberSeries('REVERSE_STOCKBREAKING');
                    updateUniqueNumberSeries('REVERSE_STOCKBREAKING',$split_no);
                    $reverseSplit->document_no = $split_no;
                    $reverseSplit->status = 'rejected';
                    $reverseSplit->approved_by = $user->id;
                    $reverseSplit->approved_at = Carbon::now()->toDateTimeString();
                    $reverseSplit->save();
                    DB::commit();
                    return redirect()->route('reverse-splitting.index')->with('warning', 'No enough child quantity at hand. Split rejected automatically.');
                }
                $split_no = getCodeWithNumberSeries('REVERSE_STOCKBREAKING');
                $reverseSplit->document_no = $split_no;
                $reverseSplit->status = 'approved';
                $reverseSplit->approved_by = $user->id;
                $reverseSplit->approved_at = Carbon::now()->toDateTimeString();
                $reverseSplit->save();


                $parentStockMove = new WaStockMove();
                $parentStockMove->user_id = $user->id;
                $parentStockMove->restaurant_id = $user->restaurant_id;
                $parentStockMove->wa_location_and_store_id = $reverseSplit->wa_location_and_store_id;
                $parentStockMove->wa_inventory_item_id = $motherItem->id;
                $parentStockMove->standard_cost = $motherItem->standard_cost;
                $parentStockMove->qauntity = $reverseSplit->expected_mother_quantity;
                $parentStockMove->new_qoh = $motherQoh - $reverseSplit->expected_mother_quantity;
                $parentStockMove->stock_id_code = $motherItem->stock_id_code;
                $parentStockMove->price = $motherItem->selling_price * $reverseSplit->expected_mother_quantity;
                $parentStockMove->document_no = $split_no;
                $parentStockMove->refrence = "$split_no - REVERSE SPLIT";
                $parentStockMove->total_cost = $motherItem->selling_price * $reverseSplit->expected_mother_quantity;
                $parentStockMove->selling_price = $motherItem->selling_price;
                $parentStockMove->save();
    
                $childStockMove = new WaStockMove();
                $childStockMove->user_id = $user->id;
                $childStockMove->restaurant_id = $user->restaurant_id;
                $childStockMove->wa_location_and_store_id = $reverseSplit->wa_location_and_store_id;
                $childStockMove->wa_inventory_item_id = $reverseSplit->child_item_id;
                $childStockMove->standard_cost = $childItem->standard_cost;
                $childStockMove->qauntity = $reverseSplit->requested_child_quantity * -1;
                $childStockMove->new_qoh = $childQoh - $reverseSplit->requested_child_quantity;
                $childStockMove->stock_id_code = $childItem->stock_id_code;
                $childStockMove->price = $childItem->selling_price * $reverseSplit->requested_child_quantity;
                $childStockMove->total_cost = $childItem->selling_price * $reverseSplit->requested_child_quantity;
                $childStockMove->selling_price = $childItem->selling_price;
                $childStockMove->document_no = $split_no;
                $childStockMove->refrence = "$split_no - Reverse Split";
                $childStockMove->save();
                //child Moves
                //Mother Moves


                updateUniqueNumberSeries('REVERSE_STOCKBREAKING',$split_no);
                DB::commit();
                return redirect()->route('reverse-splitting.index')->with('success', 'Reverse Split successfull');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with('warning', $th->getMessage());
            }
        
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
      
    }
    public function reject($id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = Auth::user();
        if (isset($permission[$pmodule . '___reject']) || $permission == 'superadmin') {
            DB::beginTransaction();
            try {
                $reverseSplit = ReverseSplit::find($id);
                $split_no = getCodeWithNumberSeries('REVERSE_STOCKBREAKING');
                $reverseSplit->document_no = $split_no;
                $reverseSplit->status = 'rejected';
                $reverseSplit->approved_by = $user->id;
                $reverseSplit->approved_at = Carbon::now()->toDateTimeString();
                $reverseSplit->save();
                updateUniqueNumberSeries('REVERSE_STOCKBREAKING',$split_no);
                DB::commit();
                return redirect()->route('reverse-splitting.index')->with('success', 'Reverse Split Rejected successfully');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with('warning', $th->getMessage());
            }
        
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
      
    }
}
