<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Enums\PromotionMatrix;
use App\Http\Controllers\Controller;
use App\ItemPromotion;
use App\Model\Branch;
use App\Model\PackSize;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Models\HamperItem;
use App\Models\PromotionGroup;
use App\Models\PromotionType;
use App\Models\WaInventoryItemApprovalStatus;
use App\View\Components\ItemCentre\InventoryItems;
use App\WaDemand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HamperPromotionsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'hampers';
        $this->title = 'Hampers';
        $this->pmodule = 'utility';
    }
    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $hampers = WaInventoryItem::where('is_hamper', true)
            ->leftJoin('item_promotions', function ($join) {
                $join->on('wa_inventory_items.id', '=', 'item_promotions.inventory_item_id');
            })
            ->select('wa_inventory_items.title', 'wa_inventory_items.standard_cost', 'wa_inventory_items.selling_price', 'wa_inventory_items.id',
                'item_promotions.from_date', 'item_promotions.to_date','item_promotions.status')
            ->latest('wa_inventory_items.created_at')
            ->get();


        return view('admin.inventory.item.hamper.index', compact('permission','pmodule','title','model','hampers'));
    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Create Hamper';
        $model = $this->model;


        $all_taxes = $this->getAllTaxFromTaxManagers();
        $suppliers = WaSupplier::pluck('name', 'id')->toArray();
        $locations = WaLocationAndStore::pluck('location_name', 'id')->toArray();
        $PackSize = PackSize::pluck('title', 'id')->toArray();
        $branches = WaLocationAndStore::pluck('location_name', 'id')->toArray();


        return view('admin.inventory.item.hamper.create', compact('permission','pmodule','title','model', 'all_taxes', 'locations', 'PackSize', 'suppliers'));
    }

    public function inventoryDropdown(Request $request)
    {

//        $data = WaInventoryItem::select([
//            'id',
//            'wa_unit_of_measure_id',
//            DB::RAW('CONCAT(title," - ",stock_id_code) as text'),
//            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . (getLoggeduserProfile()->wa_location_and_store_id) . ') as quantity')
//
//        ])->where(function ($e) use ($request) {
//            if ($request->q) {
//                $e->where('title', 'LIKE', '%' . $request->q . '%');
//                $e->orWhere('stock_id_code', 'LIKE', '%' . $request->q . '%');
//            }
//        })->get();

        $data = WaInventoryItem::select([
            'id',
            'wa_unit_of_measure_id',
//            DB::raw('CONCAT(title, " - ", stock_id_code) as title'),
//            DB::raw('(SELECT SUM(wa_stock_moves.qauntity)
//              FROM wa_stock_moves
//              WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id
//              AND wa_stock_moves.wa_location_and_store_id = ' . (getLoggeduserProfile()->wa_location_and_store_id) . ') as quantity'),
            DB::raw('CONCAT(title, " - ", stock_id_code, " (", 
              (SELECT IFNULL(SUM(wa_stock_moves.qauntity), 0) 
               FROM wa_stock_moves 
               WHERE wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id 
               AND wa_stock_moves.wa_location_and_store_id = ' . (getLoggeduserProfile()->wa_location_and_store_id) . '), 
              " units)") as text')
        ])
            ->where(function ($query) use ($request) {
                if ($request->q) {
                    $query->where('title', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('stock_id_code', 'LIKE', '%' . $request->q . '%');
                }
            })
            ->get();
        return response()->json($data);
    }
    public function suppliers(Request $request)
    {
        $data  = WaSupplier::whereHas('products', function ($query) use ($request) {
            $query->where('wa_inventory_item_suppliers.wa_inventory_item_id', $request->item_id);
        })->select([
            'id',
            DB::RAW('name as text')
        ])->get();
        return response()->json($data);
    }

    public function validate_first_step(Request $request, $id = "")
    {
        $validator = Validator::make($request->all(), [
            'stock_id_code' => ($id == "" ? "required" : "nullable") . '|unique:wa_inventory_items,stock_id_code' . $id,
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'wa_inventory_category_id' => 'required',
//            'item_sub_category_id' => 'required',
//            'suppliers.*' => 'required|exists:wa_suppliers,id',
            // 'suppliers.*' => 'required|array',
            'standard_cost' => 'required|numeric',
            // 'minimum_order_quantity'=>'required|numeric',
            'selling_price' => 'required',
            // 'profit_margin' => 'required|numeric'
            'percentage_margin' => 'required|numeric',
        ], [], [
            'suppliers.*' => 'Supplier' // Custom attribute name for error messages
        ]);
        return !$validator->fails() ? false : $validator->errors();
    }

    public function validate_second_step(Request $request, $image = "nullable")
    {
        $validator = Validator::make($request->all(), [
            'tax_manager_id' => 'required',
            'pack_size_id' => 'required',
            // 'store_location_id'=>'nullable',
            'alt_code' => 'nullable',
            'packaged_volume' => 'nullable',
            'gross_weight' => 'nullable',
            'net_weight' => 'nullable',
            'hs_code' => 'nullable',
            'restocking_method' => 'nullable',
//            'image' => $image . '|image'
        ]);
        return !$validator->fails() ? false : $validator->errors();
    }

    public function validate_third_step(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination_item.*' => 'required|exists:wa_inventory_items,id',
            'conversion_factor.*' => 'required|numeric',
            'location_id' => 'required',
            'quantity' => 'required',
            'start_time' => 'required|date_format:Y-m-d',
            'end_time' => 'required|date_format:Y-m-d|after_or_equal:start_time',
            'block_this' => 'nullable'
        ]);
        return !$validator->fails() ? false : $validator->errors();
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['current_step' => "required|in:1,2,3"]);
            if ($validator->fails()) {
                return response()->json(['result' => 0, 'errors' => $validator->errors()]);
            }
            if ($request->current_step == 1 && $st_first = $this->validate_first_step($request)) {
                return response()->json(['result' => 0, 'errors' => $st_first]);
            }
            if ($request->current_step == 2 && $st_sec = $this->validate_second_step($request, 'required')) {
                return response()->json(['result' => 0, 'errors' => $st_sec]);
            }
            if ($request->current_step == 3 && $st_third = $this->validate_third_step($request)) {
                return response()->json(['result' => 0, 'errors' => $st_third]);
            }
            if ($request->current_step != 3) {
                return response()->json(['result' => 1, 'next_step' => $request->current_step + 1]);
            }
            //save status before item
            $data = $request->all();

            try {
                $imagePath = $request->file('image')->store('images');
                $data['image'] = [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'path' => $imagePath
                ];
            } catch (\Throwable $e) {
                // pass
            }



            try {
                DB::beginTransaction();

                $location = WaLocationAndStore::find($request->location_id);


                $inventory_item = new WaInventoryItem();
                $inventory_item->stock_id_code = $request->stock_id_code;
                $inventory_item->title = $request->title;
                $inventory_item->description = $request->description;
                $inventory_item->wa_inventory_category_id = $request->wa_inventory_category_id;
                $inventory_item->item_sub_category_id = $request->item_sub_category_id;
                $inventory_item->selling_price = $request->selling_price;
                $inventory_item->standard_cost = $request->standard_cost;
                $inventory_item->margin_type = $request->margin_type;
                $inventory_item->percentage_margin = $request->percentage_margin;
                $inventory_item->max_order_quantity = $request->max_order_quantity;
                $inventory_item->tax_manager_id = $request->tax_manager_id;
                $inventory_item->pack_size_id = $request->pack_size_id;
                $inventory_item->alt_code = $request->alt_code;
                $inventory_item->packaged_volume = $request->packaged_volume;
                $inventory_item->gross_weight = $request->gross_weight;
                $inventory_item->net_weight = $request->net_weight;
                $inventory_item->hs_code = $request->hs_code;
                $inventory_item->is_hamper = true;
                $inventory_item -> save();

                $item =  $inventory_item;
                $reference = 'New Hamper '.$request->title;
                $location_id = $location->id;
                $branch_id = $location->wa_branch_id;
                $qty = $request->quantity;

                /*do stock moves for hamper*/
                $this->stockMove($reference, $item, $location_id, $branch_id, $qty);

                /*Create and Link this to a Promotion*/
                $validated = [
                    'name' => $request->title,
                    'active' => true,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ];
                $validated['end_time'] = $validated['end_time'] . ' 23:59:59';
                $group = PromotionGroup::create($validated);
                /*create promotion*/
                $type = PromotionType::where('description', PromotionMatrix::HAMPER->value)->first();
                $itemPromotion = new ItemPromotion();
                $itemPromotion->inventory_item_id = $inventory_item->id;
                $itemPromotion->promotion_type_id = $type->id;
                $itemPromotion->promotion_group_id = $group ->id;
                $itemPromotion->supplier_id = $request->supplier_id;
                $itemPromotion->initiated_by = getLoggeduserProfile()->id;
                $itemPromotion->from_date = \Carbon\Carbon::parse($group->start_time)->toDateString();
                if($request->to_date){
                    $itemPromotion->to_date = \Carbon\Carbon::parse($group->end_time)->toDateString();
                }
                $itemPromotion->current_price = $inventory_item->selling_price;
                $itemPromotion->promotion_price = $inventory_item->selling_price;

                $itemPromotion->save();


                $destination_items = $request->destination_item;
                $cost_items = $request->cost_item;
                $selling_price_items = $request->selling_price_item;
                $supplier_items = $request->supplier_item;
                foreach ($destination_items as $index => $item_id) {

                    $item_qoh = DB::table('wa_stock_moves')->where('wa_inventory_item_id', $item_id)->sum('qauntity');

                    if ($item_qoh < $request->quantity)
                    {
                        $item_name = WaInventoryItem::find($item_id)->title;
                        return response()->json(['result' => -1, 'message' => "Hamper Item $item_name Quantity can not be less that current QOH"]);

                    }
                    /*create_demand*/
                    $demand = $this->demand($supplier_items[$index] );
                    $hamper_item = HamperItem::create([
                        'hamper_id' => $inventory_item->id,
                        'demand_id' => $demand->id,
                        'wa_inventory_item_id' => $item_id,
                        'standard_cost' => $cost_items[$index],
                        'selling_price' => $selling_price_items[$index],
                        'supplier_id' => $supplier_items[$index],
                        'quantity' => $request->quantity,
                    ]);



                    $reference = $request->title.' - '.$inventory_item->id;
                    $item = WaInventoryItem::find($item_id);
                    $qty = - $request->quantity;

                    /*do stock moves for Items*/
                    $this->stockMove($reference, $item, $location_id, $branch_id, $qty);


                }
                DB::commit();
                return response()->json(['result' => 1, 'message' => 'Record added.', 'location' => route('hampers.index')]);

            }catch (\Throwable $e)
            {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['result' => -1, 'message' => $msg]);
        }
    }

    public function demand($supplier_id)
    {
        /*create a demand*/
        $demandCode = getCodeWithNumberSeries('DELTA');
        $delta = new WaDemand();
        $delta->demand_no = $demandCode;
        $delta->created_by = Auth::id();
        $delta->wa_supplier_id = $supplier_id;
        $delta->demand_amount = 0;
        $delta->edited_demand_amount = 0;
        $delta->save();
        updateUniqueNumberSeries('DELTA',$demandCode);
        /*create promotion*/
        return $delta;

    }

    public function stockMove($reference,$item, $location_id, $branch_id, $qty)
    {

        $promotionItemQoh = WaStockMove::where('wa_inventory_item_id', $item->id)->where('wa_location_and_store_id', $location_id)->sum('qauntity');
        $stockMove = new WaStockMove();
        $stockMove->user_id = Auth::id();
        $stockMove->restaurant_id = $branch_id;
        $stockMove->wa_location_and_store_id = $location_id;
        $stockMove->stock_id_code = $item->stock_id_code;
        $stockMove->wa_inventory_item_id =$item->id;
        $stockMove->price = $item->selling_price;
        $stockMove->refrence = $reference;
        $stockMove->qauntity = $qty;
        $stockMove->new_qoh = ($promotionItemQoh + $qty);
        $stockMove->standard_cost = $item->standard_cost;
        $stockMove->save();
    }

    public function show(Request $request, $id)
    {
        dd('ksk');
    }
    public function edit(Request $request, $id)
    {
        $row = WaInventoryItem::with('hamperItem')->find($id);

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Edit Hamper';
        $model = $this->model;


        $all_taxes = $this->getAllTaxFromTaxManagers();
        $suppliers = WaSupplier::pluck('name', 'id')->toArray();
        $locations = WaLocationAndStore::pluck('location_name', 'id')->toArray();
        $PackSize = PackSize::pluck('title', 'id')->toArray();
        $branches = WaLocationAndStore::pluck('location_name', 'id')->toArray();


        return view('admin.inventory.item.hamper.edit', compact('permission','pmodule','title','model', 'all_taxes', 'locations', 'PackSize', 'suppliers', 'row'));

    }

    public function update(Request $request, $id)
    {


        if ($st_first = $this->validate_third_step($request)) {
            return response()->json(['result' => 0, 'errors' => $st_first]);
        }


        $row = WaInventoryItem::find($id);
        $location = WaLocationAndStore::find($request->location_id);
//        dd($location);



        try {
            DB::beginTransaction();
            /*get current qoh for Item*/
            $qoh = DB::table('wa_stock_moves')->where('wa_inventory_item_id', $row->id)->sum('qauntity');

            if ($qoh > $request->quantity)
            {
                return response()->json(['result' => -1, 'message' => 'Quantity can not be less that current QOH']);

            }
            $diff_qty = $request->quantity-$qoh;


            /*do stock moves for hamper*/
            $item =  $row;
            $reference = 'Edit Hamper '.$request->title;
            $location_id = $location->id;
            $branch_id = $location->wa_branch_id;
            $qty = $diff_qty;
            $this->stockMove($reference, $item, $location_id, $branch_id, $qty);


            /*return  hamper item qty*/
            $items = HamperItem::where('hamper_id', $id)->get();
            foreach ($items as $item)
            {
                $reference = $request->title.' - Update';
                $qty =  $qoh;
                /*do stock moves for Items*/
                $this->stockMove($reference, $item, $location_id, $branch_id, $qty);
            }


            /*move new qoh*/

            $destination_items = $request->destination_item;
            $cost_items = $request->cost_item;
            $selling_price_items = $request->selling_price_item;
            $supplier_items = $request->supplier_item;

            $std_cost = 0;
            $selling_price = 0;
            foreach ($destination_items as $index => $item_id) {

                /*check QOH*/
                $item_qoh = DB::table('wa_stock_moves')->where('wa_inventory_item_id', $item_id)->sum('qauntity');

                if ($item_qoh < $request->quantity)
                {
                    $item_name = WaInventoryItem::find($item_id)->title;
                    return response()->json(['result' => -1, 'message' => "Hamper Item $item_name Quantity can not be less that current QOH"]);

                }
                // Check if the hamper item exists
                $hamper_item = HamperItem::where([
                    'hamper_id' => $id,
                    'wa_inventory_item_id' => $item_id,
                ])->first();

                if ($hamper_item) {
                    // Update existing hamper item
                    $hamper_item->update([
                        'standard_cost' => $cost_items[$index],
                        'selling_price' => $selling_price_items[$index],
                        'quantity' => $request->quantity,
                    ]);
                } else {
                    // Create new demand only if hamper item does not exist
                    $demand = $this->demand($supplier_items[$index]);

                    // Create new hamper item
                    HamperItem::create([
                        'hamper_id' => $id,
                        'demand_id' => $demand->id,
                        'wa_inventory_item_id' => $item_id,
                        'standard_cost' => $cost_items[$index],
                        'selling_price' => $selling_price_items[$index],
                        'supplier_id' => $supplier_items[$index],
                        'quantity' => $request->quantity,
                    ]);
                }


                $reference = $request->title.' - '.$id;
                $item = WaInventoryItem::find($item_id);
                $qty = - $request->quantity;

                $std_cost += $cost_items[$index];
                $selling_price += $selling_price_items[$index];

                /*do stock moves for Items*/
                $this->stockMove($reference, $item, $location_id, $branch_id, $qty);

            }
            $row->update([
                'standard_cost' => $std_cost,
                'selling_price' => $selling_price,
            ]);
            DB::commit();
            return response()->json(['result' => 1, 'message' => 'Record added.', 'location' => route('hampers.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            return response()->json(['result' => -1, 'message' => $msg]);
        }
    }
}
