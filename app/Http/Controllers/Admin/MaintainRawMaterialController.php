<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\StockAdjustment;
use App\Model\WaAccountingPeriod;
use App\Model\WaStockMove;
use App\Model\WaStockMove2;
use App\Model\WaNumerSeriesCode;
use App\Model\WaGlTran;
use App\Model\WaCategory;
use App\Model\WaSupplier;
use App\Model\WaCategoryItemPrice;
use App\Model\PackSize;
use App\Model\WaInventoryAssignedItems;
use Excel;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class MaintainRawMaterialController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $this->model = 'maintain-raw-material-items';
        $this->title = 'Maintain Raw Material Items';
        $this->pmodule = 'maintain-raw-material-items';

    }

    public function assignInventoryItems($itemid)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $data = WaInventoryItem::with(['destinated_items.destinated_item'])->where('id', $itemid)->first();
            if (!$data) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.maintainrawmaterial.assignInventoryItems', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'data'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function inventoryDropdown(Request $request)
    {
        $data = WaInventoryItem::select([
            'id',
            DB::RAW('CONCAT(title," - ",stock_id_code) as text')
        ])->where(function ($e) use ($request) {
            if ($request->q) {
                $e->orWhere('title', 'LIKE', '%' . $request->q . '%');
                $e->orWhere('stock_id_code', 'LIKE', '%' . $request->q . '%');
            }
        })->where('id', '!=', $request->id)->limit(20)->get();
        return response()->json($data);
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.maintainrawmaterial.index_server', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function datatable(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'stock_id_code', 'title', 'uom', 'standard_cost', 'qauntity', 'qty_on_order'
        ];
        $totalData = WaInventoryItem::where('item_type', '2')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        //$data_query = WaInventoryItem::select('items.*')->where([['type', $item_types['feed']]]);
        $data_query = WaInventoryItem::select('wa_inventory_categories.id as cat_id', 'wa_inventory_categories.category_description', 'wa_inventory_items.*',
            DB::RAW(' (select sum(qauntity) from wa_stock_moves where wa_stock_moves.stock_id_code=wa_inventory_items.stock_id_code) as item_total_qunatity')
        )->with('pack_size', 'getUnitOfMeausureDetail')
            ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'wa_inventory_items.wa_inventory_category_id')->where('item_type', '2');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function ($data_query) use ($search) {
                $data_query->where('stock_id_code', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('standard_cost', 'LIKE', "%{$search}%");
            });

        }

        //pre($data_query);

        // echo "<pre>"; print_r( $data_query); die;

        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = array();
        // dd($data_query);
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) {
                $user_link = '';

                $nestedData['stock_id_code'] = $row->stock_id_code;
                $nestedData['item_category'] = $row->category_description;
                $nestedData['title'] = $row->title;
                $nestedData['uom'] = @$row->pack_size->title;
                $nestedData['standard_cost'] = manageAmountFormat($row->standard_cost);
                $nestedData['qauntity'] = manageAmountFormat($row->item_total_qunatity);//manageAmountFormat(@$row->getAllFromStockMoves->sum('qauntity'));
                $nestedData['selling_price'] = manageAmountFormat($row->selling_price);
                $action_text = ($row->slug != 'mpesa' && (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')) ? buttonHtmlCustom('edit', route($model . '.edit', $row->slug)) : '';

                $link_popup = route('admin.table.adjust-item-stock-form', $row->slug);
                $link_popup2 = route('admin.table.adjust-category-price-form', $row->slug);
                $action_text .= buttonHtmlCustom('stock_movements', route($model . '.stock-movements', $row->slug));
                $action_text .= buttonHtmlCustom('stock_movements_2', route($model . '.stock-movements-2', $row->slug));
                $action_text .= buttonHtmlCustom('stock_status', route($model . '.stock-status', $row->slug));
                if (isset($permission[$pmodule . '___manage-item-stock']) || $permission == 'superadmin') {
                    $action_text .= view('admin.maintainrawmaterial.popup_link', [
                        'link_popup' => $link_popup,
                        'id' => $row->id,
                        'type' => '1'
                    ]);
                }

                if (count($row->getAllFromStockMoves) == 0) {
                    $action_text .= (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') ? buttonHtmlCustom('delete', route($model . '.destroy', $row->slug)) : '';
                }

                $action_text .= view('admin.maintainrawmaterial.popup_link', [
                    'link_popup2' => $link_popup2,
                    'id' => $row->id,
                    'data' => $row,
                    'type' => '2'
                ]);


                $nestedData['action'] = $action_text;
                $nestedData['action'] .= '<a href="' . route($model . '.assignInventoryItems', $row->id) . '" title="Assign Inventory Items"><i class="fa fa-share-alt" aria-hidden="true"></i></a>';


                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function postassignInventoryItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:wa_inventory_items,id|in:' . $id,
            // 'destination_item'=>'required|array',
            'destination_item.*' => 'required|exists:wa_inventory_items,id',
            'conversion_factor.*' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        WaInventoryAssignedItems::where('wa_inventory_item_id', $request->id)->delete();
        $destination_item = [];
        if ($request->destination_item && count($request->destination_item) > 0) {
            foreach ($request->destination_item as $key => $value) {
                $destination_item[] = [
                    'wa_inventory_item_id' => $request->id,
                    'destination_item_id' => $value,
                    'conversion_factor' => ($request->conversion_factor[$key] ?? NULL),
                ];
            }
            if (count($destination_item) > 0) {
                WaInventoryAssignedItems::insert($destination_item);
            }
        }
        return response()->json([
            'result' => 1,
            'message' => 'Items Assigned successfully',
            'location' => route('maintain-items.index')
        ]);
    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            $all_taxes = $this->getAllTaxFromTaxManagers();
            $suppliers = WaSupplier::pluck('name', 'id')->toArray();
            $locations = WaLocationAndStore::pluck('location_name', 'id')->toArray();
            $PackSize = PackSize::pluck('title', 'id')->toArray();
            return view('admin.maintainrawmaterial.create', compact('title', 'model', 'breadcum', 'all_taxes', 'locations', 'PackSize', 'suppliers'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        try {

            if ($request->tax_manager_id == 1 && $request->tax_manager_id != '') {
                $validator = Validator::make($request->all(), [
                    'stock_id_code' => 'required|unique:wa_inventory_items',
                    'suppliers*' => 'required|array',
                    'hs_code' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [

                    'stock_id_code' => 'required|unique:wa_inventory_items',
                    'description' => 'unique:wa_inventory_items',
                    'suppliers' => 'required|array'
                ]);
            }

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row = new WaInventoryItem();
                $row->stock_id_code = $request->stock_id_code;
                $row->title = $request->title;
                $row->description = $request->description;
                if ($request->file('image')) {
                    $file = $request->file('image');
                    $image = uploadwithresize($file, 'inventory_items', '341');
                    $row->image = $image;
                }
                $row->wa_inventory_category_id = $request->wa_inventory_category_id;
                $row->standard_cost = $request->standard_cost;
                $row->minimum_order_quantity = $request->minimum_order_quantity;
                $row->selling_price = $request->selling_price;
                $row->wa_unit_of_measure_id = $request->wa_unit_of_measure_id;
                $row->tax_manager_id = $request->tax_manager_id;
                $row->cost_update_time = date("Y-m-d H:i:s");
                $row->showroom_stock = isset($request->showroom_stock) ? '1' : '0';
                $row->new_stock = isset($request->wa_unit_of_measure_id) ? '1' : '0';
                $row->showroom_stock = 0;

                $row->item_type = '2';
                $row->wa_unit_of_measure_id = @$request->wa_unit_of_measure_id;
                $row->conversion_rate = @$request->conversion_rate;


                $row->pack_size_id = $request->pack_size_id ?? NULL;
                $row->store_location_id = $request->store_location_id ?? NULL;
                $row->alt_code = $request->alt_code ?? NULL;
                $row->packaged_volume = $request->packaged_volume ?? NULL;
                $row->gross_weight = $request->gross_weight ?? NULL;
                $row->net_weight = $request->net_weight ?? NULL;
                $row->hs_code = $request->hs_code ?? NULL;


                $row->save();


                if (isset($request->suppliers) && count($request->suppliers)) {
                    foreach ($request->suppliers as $key => $value) {
                        $suppliers[] = [
                            'wa_inventory_item_id' => $row->id,
                            'wa_supplier_id' => $value,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }
                    if (count($suppliers) > 0) {
                        DB::table('wa_inventory_item_suppliers')->insert($suppliers);
                    }
                }

                updateUniqueNumberSeries('INVENTORY ITEM', $request->stock_id_code);
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model . '.index');
            }


        } catch (\Exception $e) {


            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {

    }


    public function edit($slug)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = WaInventoryItem::whereSlug($slug)->first();

                $inventory_item_suppliers = $row->inventory_item_suppliers->pluck('id')->toArray();

                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    $suppliers = WaSupplier::pluck('name', 'id')->toArray();
                    $locations = WaLocationAndStore::pluck('location_name', 'id')->toArray();
                    $PackSize = PackSize::pluck('title', 'id')->toArray();
                    $all_taxes = $this->getAllTaxFromTaxManagers();
                    return view('admin.maintainrawmaterial.edit', compact('title', 'model', 'breadcum', 'row', 'all_taxes', 'locations', 'PackSize', 'suppliers'));
                } else {
                    Session::flash('warning', 'Invalid Request');
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


    public function update(Request $request, $slug)
    {
        try {
            $row = WaInventoryItem::whereSlug($slug)->first();
            if ($request->tax_manager_id == 1 && $request->tax_manager_id != '') {
                $validator = Validator::make($request->all(), [
                    'stock_id_code' => 'required|unique:wa_inventory_items,stock_id_code,' . $row->id,
                    'suppliers*' => 'required|array',
                    'hs_code*' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'stock_id_code' => 'required|unique:wa_inventory_items,stock_id_code,' . $row->id,
                    'suppliers*' => 'required|array',
                ]);
            }
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                DB::table('wa_inventory_item_suppliers')->where('wa_inventory_item_id', $row->id)->delete();

                if (isset($request->suppliers) && count($request->suppliers)) {
                    foreach ($request->suppliers as $key => $value) {
                        $suppliers[] = [
                            'wa_inventory_item_id' => $row->id,
                            'wa_supplier_id' => $value,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (count($suppliers) > 0) {
                        DB::table('wa_inventory_item_suppliers')->insert($suppliers);
                    }
                }

                $row->stock_id_code = strtoupper($request->stock_id_code);
                $row->title = $request->title;
                $row->description = $request->description;
                $row->prev_standard_cost = $row->standard_cost;
                if ($request->file('image')) {
                    $file = $request->file('image');
                    $image = uploadwithresize($file, 'inventory_items', '341');
                    $row->image = $image;
                }
                $row->wa_inventory_category_id = $request->wa_inventory_category_id;
                $row->standard_cost = $request->standard_cost;
                $row->minimum_order_quantity = $request->minimum_order_quantity;
                $row->selling_price = $request->selling_price;
                $row->wa_unit_of_measure_id = $request->wa_unit_of_measure_id;
                $row->tax_manager_id = $request->tax_manager_id;
                $row->hs_code = $request->hs_code;
                $row->showroom_stock = isset($request->showroom_stock) ? '1' : '0';
                $row->new_stock = isset($request->new_stock) ? '1' : '0';

                $row->item_type = '2';
                $row->wa_unit_of_measure_id = @$request->wa_unit_of_measure_id;
                $row->conversion_rate = @$request->conversion_rate;


                $row->pack_size_id = $request->pack_size_id ?? NULL;
                $row->store_location_id = $request->store_location_id ?? NULL;
                $row->alt_code = $request->alt_code ?? NULL;
                $row->packaged_volume = $request->packaged_volume ?? NULL;
                $row->gross_weight = $request->gross_weight ?? NULL;
                $row->net_weight = $request->net_weight ?? NULL;
                $row->hs_code = $request->hs_code ?? NULL;
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.index');
            }

        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {
            $row = WaInventoryItem::whereSlug($slug)->first();
            if (!$row || count($row->getAllFromStockMoves) > 0) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $row->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function standardCost(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Standard Cost';
        $model = $this->model;
        if (isset($permission[$pmodule . '___manage-standard-cost']) || $permission == 'superadmin') {
            if (isset($_GET['block_all'])) {
                DB::table('wa_inventory_items')->update(['block_this' => 1]);
                Session::flash('success', 'All Inventory items are blocked successfully.');
                return redirect()->route($this->model . '.standard.cost');
            }
            if (isset($_GET['un_block_all'])) {
                DB::table('wa_inventory_items')->update(['block_this' => 0]);
                Session::flash('success', 'All Inventory items are blocked successfully.');
                return redirect()->route($this->model . '.standard.cost');
            }
            $lists = WaInventoryItem::orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.standard.cost'), 'Listing' => ''];
            return view('admin.maintainrawmaterial.standardCost', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function editStandardCost($slug)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___manage-standard-cost']) || $permission == 'superadmin') {
                $row = WaInventoryItem::whereSlug($slug)->first();
                if ($row) {
                    $this->title = 'Standard Cost';
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.standard.cost'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.maintainrawmaterial.editStandardCost', compact('title', 'model', 'breadcum', 'row'));
                } else {
                    Session::flash('warning', 'Invalid Request');
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

    public function updateStandardCost(Request $request, $slug)
    {
        try {
            $row = WaInventoryItem::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|unique:wa_inventory_items,stock_id_code,' . $row->id,
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $row->standard_cost = $request->standard_cost;
                $row->selling_price = $request->selling_price ?? $row->selling_price;

                $row->prev_standard_cost = $request->old_standard_cost;
                $row->cost_update_time = date('Y-m-d H:i:s');
                $row->block_this = $request->block_this ? True : False;

                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.standard.cost');
            }

        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function stockMovements($StockIdCode, Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $formurl = "stock-movements";
        $location = WaLocationAndStore::where(['wa_branch_id' => getLoggeduserProfile()->restaurant_id])->get();
        $lists = WaStockMove::with(['getRelatedUser', 'getLocationOfStore'])->select([
            '*',
            \DB::RAW('
            (CASE WHEN grn_type_number = 4 THEN (SELECT wa_pos_cash_sales_items.selling_price FROM wa_pos_cash_sales_items where wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_stock_moves.wa_pos_cash_sales_id AND wa_stock_moves.wa_inventory_item_id = wa_pos_cash_sales_items.wa_inventory_item_id LIMIT 1)
            WHEN grn_type_number = 51 THEN (SELECT wa_internal_requisition_items.selling_price FROM wa_internal_requisition_items where wa_internal_requisition_items.wa_internal_requisition_id = wa_stock_moves.wa_internal_requisition_id AND wa_stock_moves.wa_inventory_item_id = wa_internal_requisition_items.wa_inventory_item_id LIMIT 1)
            ELSE selling_price END
            ) as selling_price
            ')
        ])->where(function ($w) use ($request) {
            if ($request->from && $request->to) {
                $w->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            }
            if ($request->location) {
                $w->where('wa_location_and_store_id', $request->location);
            }
        });
        if (($request->from && $request->to) || $request->location) {
            $lists = $lists->where('stock_id_code', $StockIdCode)->orderBy('id', 'asc')->get();
        } else {
            $lists = $lists->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->limit(20)->get();
            $lists = $lists->sort();
        }

        $row = WaInventoryItem::where('stock_id_code', $StockIdCode)->first();
        $breadcum = [$title => route($model . '.index'), 'Stock Movement' => '', $StockIdCode => ''];
        if ($request->type == 'pdf') {
            $firstQoh = WaStockMove::where(function ($w) use ($request) {
                $w->where('created_at', '<', $request->from . ' 00:00:00');
                if ($request->location) {
                    $w->where('wa_location_and_store_id', $request->location);
                }
            })
                ->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id' => $request->location])->first();
            $pdf = \PDF::loadView('admin.maintainrawmaterial.stockmovement_pdf', compact('firstQoh', 'currentLocation', 'request', 'row', 'location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode'));
            $report_name = 'Stock-Card-' . date('Y_m_d_H_i_A');
            // return $pdf->stream();
            return $pdf->download($report_name . '.pdf');
        }
        if ($request->type == 'print') {
            $firstQoh = WaStockMove::where(function ($w) use ($request) {
                $w->where('created_at', '<', $request->from . ' 00:00:00');
                if ($request->location) {
                    $w->where('wa_location_and_store_id', $request->location);
                }
            })
                ->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id' => $request->location])->first();
            return view('admin.maintainrawmaterial.stockmovement_pdf', compact('firstQoh', 'currentLocation', 'request', 'row', 'location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode'));
        }
        return view('admin.maintainrawmaterial.stockmovement', compact('location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode', 'formurl'));
    }


    public function stockMovements2($StockIdCode, Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $formurl = "stock-movements-2";
        $location = WaLocationAndStore::where(['wa_branch_id' => getLoggeduserProfile()->restaurant_id])->get();
        $lists = WaStockMove2::with(['getRelatedUser', 'getLocationOfStore'])->select([
            '*',
            \DB::RAW('
            (CASE WHEN grn_type_number = 4 THEN (SELECT wa_pos_cash_sales_items.selling_price FROM wa_pos_cash_sales_items where wa_pos_cash_sales_items.wa_pos_cash_sales_id = wa_stock_moves_2.wa_pos_cash_sales_id AND wa_stock_moves_2.wa_inventory_item_id = wa_pos_cash_sales_items.wa_inventory_item_id LIMIT 1)
            WHEN grn_type_number = 51 THEN (SELECT wa_internal_requisition_items.selling_price FROM wa_internal_requisition_items where wa_internal_requisition_items.wa_internal_requisition_id = wa_stock_moves_2.wa_internal_requisition_id AND wa_stock_moves_2.wa_inventory_item_id = wa_internal_requisition_items.wa_inventory_item_id LIMIT 1)
            ELSE selling_price END
            ) as selling_price
            ')
        ])->where(function ($w) use ($request) {
            if ($request->from && $request->to) {
                $w->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            }
            if ($request->location) {
                $w->where('wa_location_and_store_id', $request->location);
            }
        });
        if (($request->from && $request->to) || $request->location) {
            $lists = $lists->where('stock_id_code', $StockIdCode)->orderBy('id', 'asc')->get();
        } else {
            $lists = $lists->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->limit(20)->get();
            $lists = $lists->sort();
        }

        $row = WaInventoryItem::where('stock_id_code', $StockIdCode)->first();
        $breadcum = [$title => route($model . '.index'), 'Stock Movement' => '', $StockIdCode => ''];
        if ($request->type == 'pdf') {
            $firstQoh = WaStockMove2::where(function ($w) use ($request) {
                $w->where('created_at', '<', $request->from . ' 00:00:00');
                if ($request->location) {
                    $w->where('wa_location_and_store_id', $request->location);
                }
            })
                ->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id' => $request->location])->first();
            $pdf = \PDF::loadView('admin.maintainrawmaterial.stockmovement_pdf', compact('firstQoh', 'currentLocation', 'request', 'row', 'location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode'));
            $report_name = 'Stock-Card-' . date('Y_m_d_H_i_A');
            // return $pdf->stream();
            return $pdf->download($report_name . '.pdf');
        }
        if ($request->type == 'print') {
            $firstQoh = WaStockMove2::where(function ($w) use ($request) {
                $w->where('created_at', '<', $request->from . ' 00:00:00');
                if ($request->location) {
                    $w->where('wa_location_and_store_id', $request->location);
                }
            })
                ->where('stock_id_code', $StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id' => $request->location])->first();
            return view('admin.maintainrawmaterial.stockmovement_pdf', compact('firstQoh', 'currentLocation', 'request', 'row', 'location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode'));
        }
        return view('admin.maintainrawmaterial.stockmovement', compact('location', 'title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'StockIdCode', 'formurl'));
    }

    public function stockStatus($StockIdCode)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $lists = DB::select(DB::raw("SELECT SUM(`qauntity`) as total_quantity,wa_location_and_store_id from wa_stock_moves where stock_id_code = '" . $StockIdCode . "' group by `wa_location_and_store_id`"));


        $storeBiseQty = [];
        foreach ($lists as $list) {
            $storeBiseQty[$list->wa_location_and_store_id] = $list->total_quantity;
        }
        // dd($storeBiseQty);
        $lists = WaLocationAndStore::get();


        $breadcum = [$title => route($model . '.index'), 'Stock Status' => '', $StockIdCode => ''];
        return view('admin.maintainrawmaterial.stockstatus', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'storeBiseQty'));
    }


    public function adjustItemStockForm($slug = '')
    {
        $item_row = WaInventoryItem::with(['getUnitOfMeausureDetail'])->where('slug', $slug)->first();
        $locations = WaLocationAndStore::getLocationList();
        return view('admin.maintainrawmaterial.adjust_item_form', compact('item_row', 'locations'));
    }


    public function adjustCategoryPriceForm($slug = '')
    {
        $item_row = WaInventoryItem::with(['getTaxesOfItem'])->where('slug', $slug)->first();
        $categories = WaCategory::get();
        return view('admin.maintainrawmaterial.adjust_category_price_form', compact('item_row', 'categories'));
    }

    public function getAvailableQuantityAjax(Request $request)
    {
        if (isset($request->store_c)) {
            $available_quantity = getItemAvailableQuantity_C($request->stock_id_code, $request->location_id);
        } else {
            $available_quantity = getItemAvailableQuantity($request->stock_id_code, $request->location_id);
        }
        return json_encode(['available_quantity' => $available_quantity]);
    }

    public function manageCategoryPrice(Request $request)
    {
        //echo "<pre>"; print_r($request->all()); die;
        $itemid = $request->get('item_id');
        foreach ($request->get('category_id') as $key => $val) {
            $checkexisting = WaCategoryItemPrice::where('item_id', $itemid)->where('category_id', $val)->count();
            if ($checkexisting == 0) {
                $catprice = new WaCategoryItemPrice();
            } else {
                $catprice = WaCategoryItemPrice::where('item_id', $itemid)->where('category_id', $val)->first();
            }
            $catprice->item_id = $itemid;
            $catprice->price = $request->get('category_price')[$key];
            $catprice->category_id = $val;
            $catprice->save();
        }
        Session::flash('success', 'Category Item Price Saved Successfully');
        return redirect()->route($this->model . '.index');

    }

    public function stockManage(Request $request)
    {
        $item_row = WaInventoryItem::where('id', $request->item_id)->first();
        $adjustment_quantity = $request->adjustment_quantity;
        $current_available_quantity = getItemAvailableQuantity($request->stock_id_code, $request->wa_location_and_store_id);

        $new_quantity = $current_available_quantity + $adjustment_quantity;


        if ($new_quantity < 0) {
            $error = "Item No $item_row->stock_id_code does not have enough stock.";
            Session::flash('warning', $error);
            return redirect()->back();
        }


        $logged_user_profile = getLoggeduserProfile();
        $entity = new StockAdjustment();
        $entity->user_id = $logged_user_profile->id;
        $entity->item_id = $request->item_id;
        $entity->wa_location_and_store_id = $request->wa_location_and_store_id;
        $entity->adjustment_quantity = $request->adjustment_quantity;
        $entity->comments = $request->comments;
        $entity->item_adjustment_code = $request->item_adjustment_code;
        $entity->save();

        // $series_module = WaNumerSeriesCode::where('module','GRN')->first();
        $series_module = $item_adj = WaNumerSeriesCode::where('module', 'ITEM ADJUSTMENT')->first();
        $WaAccountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        // $grn_number = getCodeWithNumberSeries('GRN');
        $dateTime = date('Y-m-d H:i:s');

        $stockMove = new WaStockMove();
        $stockMove->user_id = $logged_user_profile->id;
        $stockMove->stock_adjustment_id = $entity->id;
        $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
        $stockMove->wa_location_and_store_id = $entity->wa_location_and_store_id;
        $stockMove->wa_inventory_item_id = $item_row->id;
        $stockMove->standard_cost = $item_row->standard_cost;
        $stockMove->qauntity = $adjustment_quantity;
        $stockMove->new_qoh = ($item_row->getAllFromStockMoves->where('wa_location_and_store_id', @$entity->wa_location_and_store_id)->sum('qauntity') ?? 0) + $stockMove->qauntity;
        $stockMove->stock_id_code = $item_row->stock_id_code;
        $stockMove->grn_type_number = $series_module->type_number;
        $stockMove->document_no = $request->item_adjustment_code;
        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
        $stockMove->price = $item_row->standard_cost;
        $stockMove->refrence = $entity->comments;
        $stockMove->save();

        $dr = new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $request->item_adjustment_code;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;

        $dr->account = $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code;
        $dr->amount = abs($item_row->standard_cost * $adjustment_quantity);
        if ($adjustment_quantity < '0') {
//             $dr->amount = '-'.abs($item_row->standard_cost * $adjustment_quantity);
            $dr->account = $item_row->getInventoryCategoryDetail->getusageGlDetail->account_code;
        }
        $dr->narrative = $item_row->stock_id_code . '/' . $item_row->title . '/' . $item_row->standard_cost . '@' . $adjustment_quantity;
        $dr->save();

        $dr = new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $request->item_adjustment_code;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;

        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
        $dr->account = $item_row->getInventoryCategoryDetail->getPricevarianceGlDetail->account_code;
        $tamount = $item_row->standard_cost * $adjustment_quantity;

        $dr->amount = '-' . abs($tamount);
        if ($adjustment_quantity < '0') {
            //           $dr->amount = abs($item_row->standard_cost * $adjustment_quantity);
            $dr->account = $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code;
        }
        $dr->narrative = $item_row->stock_id_code . '/' . $item_row->title . '/' . $item_row->standard_cost . '@' . $adjustment_quantity;
        $dr->save();


        updateUniqueNumberSeries('ITEM ADJUSTMENT', $request->item_adjustment_code);
        // updateUniqueNumberSeries('GRN',$grn_number);
        Session::flash('success', 'Processed Successfully');
        return redirect()->route($this->model . '.index');
    }

    public function stockMovementGlEntries($stock_move_id, $stock_id_code)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = WaGlTran::where('stock_adjustment_id', $stock_move_id)->orderBy('id', 'desc')->get();
        //echo "<pre>"; print_r($data); die;
        $negativeAMount = WaGlTran::where('stock_adjustment_id', $stock_move_id)->where('amount', '<', '0')->sum('amount');
        $positiveAMount = WaGlTran::where('stock_adjustment_id', $stock_move_id)->where('amount', '>', '0')->sum('amount');


        $breadcum = [$title => route($model . '.index'), 'Stock Movement' => route($model . '.stock-movements', $stock_id_code), 'GL Entries' => ''];
        return view('admin.maintainrawmaterial.gl_entries', compact('title', 'data', 'model', 'breadcum', 'stock_id_code', 'pmodule', 'permission', 'negativeAMount', 'positiveAMount'));
    }

    public function exportCategoryPrice(Request $request)
    {
        //   echo "dfs"; die;

        $data_query = WaInventoryItem::select('wa_inventory_categories.id as cat_id', 'wa_inventory_categories.category_description', 'wa_inventory_items.*')->with('getUnitOfMeausureDetail', 'getAllFromStockMoves', 'location')
            ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'wa_inventory_items.wa_inventory_category_id')
            ->where('wa_inventory_items.wa_inventory_category_id', $request->wa_inventory_category_id)
//        ->limit(50)
            ->get();
        $filetype = "xlsx";
        $mixed_array = $data_query;
        $export_array = [];
        $file_name = 'Item Category Price';
        $counter = 1;

        $pricecat = WaCategory::get();
        $headings = [];
        //	$prices = [];
        foreach ($pricecat as $key => $val) {
            $headings[$key] = $val->title;
//			$prices[$key] = "";
        }


        $export_arrays = array('Stock ID', 'Description', 'Category', 'Standard Cost', 'Selling Price', 'VAT', 'Gross weight', 'Bin Location', 'Store Location', 'HS Code');

        $export_array[] = array_merge($export_arrays, $headings);
        $final_amount = [];

        foreach ($mixed_array as $item) {

            $prices = [];
            foreach ($pricecat as $key => $val) {
                $prices[$key] = WaCategoryItemPrice::getitemcatprice($item->id, $val->id);
            }

            $final_amount[] = 0;

            $export_arrays = [
                $item->stock_id_code,
                $item->description,
                $item->wa_inventory_category_id,
                $item->standard_cost,
                $item->selling_price,
                $item->tax_manager_id,
                $item->gross_weight,
                $item->wa_unit_of_measure_id,
                $item->store_location_id,
                $item->hs_code,
            ];

            $export_array[] = array_merge($export_arrays, $prices);


            $counter++;
        }

//        echo "<pre>"; print_r($export_array); die;
        $this->downloadExcelFile($export_array, $filetype, $file_name);

    }

    public function downloadExcelFile($data, $type, $file_name)
    {
        return Excel::create($file_name, function ($excel) use ($data) {
            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                foreach ($data as $record) {
                    $i = 'A';
                    foreach ($record as $key => $records) {
                        if ($key > 4) {
                            $sheet->cell($i . '2', function ($cell) {
                                $cell->setBackground('#FFFF00');
                            });
                        }
                        $sheet->getStyle($i . '2')->getFont()
                            ->setBold(true);
                        $i++;
                    }
                }
            });
        })->download($type);
    }

    public function importexcelforitempriceupdate(Request $request)
    {
        if ($request->hasFile('excel_file')) {
            $path = $request->file('excel_file')->getRealPath();
            Excel::load($path, function ($reader) use (&$excel) {
                $objExcel = $reader->getExcel();
                $sheet = $objExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $alphabet = range('A', 'Z');
                $highestColumnInNum = array_search($highestColumn, $alphabet);
                $excel = [];
                $rown = $highestColumnInNum - 5;
                for ($row = 1; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                        NULL, TRUE, FALSE);

                    $excel[] = $rowData[0];
                }
            });


            $allCategories = \App\Model\WaInventoryCategory::get();
            $data = [];
            $heading = [];
            foreach ($excel as $key => $val) {
                if ($key == 1) {
                    $heading = $val;
                }
                if ($key > 1) {
                    $data[$key]['item_id'] = $val[0];
                    $data[$key]['category'] = (int)$val[2];
                    $data[$key]['standard_cost'] = (int)@$val[3];
                    $data[$key]['selling_price'] = (int)@$val[4];
                    $data[$key]['tax_manager_id'] = (int)@$val[5];
                    $data[$key]['gross_weight'] = (int)@$val[6];
                    $data[$key]['wa_unit_of_measure_id'] = (int)@$val[7];
                    $data[$key]['store_location_id'] = (int)@$val[8];
                    $data[$key]['hs_code'] = @$val[9];
                    foreach ($val as $k => $vals)
                        if ($k > 8 && $heading[$k] != "") {
                            $data[$key][$heading[$k]] = ($vals != "") ? $vals : "";
                        }
                }
            }
            //        echo "<pre>"; print_r($data); die;
            // dd($data);
            $final = [];
            foreach ($data as $key => $vals) {
                $i = 0;
                $itemid = WaInventoryItem::where('stock_id_code', $vals['item_id'])->first();
                foreach ($vals as $k => $val) {
                    if ($i > 0) {
                        if ($val != "") {
                            $categoryid = WaCategory::where('title', $k)->first();
                            if ($itemid && $categoryid) {
                                $checkexisting = WaCategoryItemPrice::where('item_id', $itemid->id)->where('category_id', $categoryid->id)->first();
                                if (!$checkexisting) {
                                    $catprice = new WaCategoryItemPrice();
                                } else {
                                    $catprice = $checkexisting;
                                }
                                $catprice->item_id = $itemid;
                                $catprice->price = $val;
                                $catprice->category_id = $categoryid->id;
                                $catprice->save();
                                // $final[] = [$categoryid , $itemid];
                            }
                        }
                    }
                    $i++;
                }
                if ($itemid) {
                    // $final[] = ['' , $itemid];
                    $itemid->standard_cost = $vals['standard_cost'] ?? $itemid->standard_cost;
                    $itemid->selling_price = $vals['selling_price'] ?? $itemid->selling_price;
                    $itemid->tax_manager_id = $vals['tax_manager_id'] ?? $itemid->tax_manager_id;
                    $itemid->wa_unit_of_measure_id = $vals['wa_unit_of_measure_id'] ?? $itemid->wa_unit_of_measure_id;
                    $itemid->wa_inventory_category_id = $vals['category'] ?? $itemid->wa_inventory_category_id;
                    $itemid->store_location_id = $vals['store_location_id'] ?? $itemid->store_location_id;
                    $itemid->gross_weight = $vals['gross_weight'] ?? $itemid->gross_weight;
                    $itemid->hs_code = $vals['hs_code'] ?? $itemid->hs_code;
                    $itemid->save();
                }
            }
            // dd($final);
            Session::flash('success', 'Category Item Price Imported Successfully');
            return redirect()->route($this->model . '.index');

        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->route($this->model . '.index');

        }


    }

    public function downloadInvetoryitems(Request $request)
    {
        try {
            $data_query = WaInventoryItem::select('wa_inventory_categories.id as cat_id', 'wa_inventory_categories.category_description',
                'wa_inventory_items.*', DB::RAW(' (select sum(qauntity) from wa_stock_moves where wa_stock_moves.stock_id_code=wa_inventory_items.stock_id_code) as item_total_qunatity'))->with('pack_size', 'getAllFromStockMoves', 'getTaxesOfItem', 'location', 'unitofmeasures')
                ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'wa_inventory_items.wa_inventory_category_id')->get();
            $arrays = [];
            $sumH = 0;
            if (!empty($data_query)) {
                foreach ($data_query as $key => $row) {
                    $arrays[] = ['Stock Id Code' => (string)($row->stock_id_code),
                        'Title' => $row->title,
                        'Item Category' => $row->category_description,
                        'Pack Size' => (string)($row->pack_size ? $row->pack_size->title : ''),
                        'Standard Cost' => (string)$row->standard_cost,
                        'Selling Price' => (string)$row->selling_price,
                        'Quantity' => (string)(@$row->item_total_qunatity ?? 0),
                        //'Quantity' => (string)($row->getAllFromStockMoves ? $row->getAllFromStockMoves->sum('qauntity') : 0),
                        'Tax Category' => (string)@$row->getTaxesOfItem->title,
                        'Default Store' => (string)@$row->location->location_name,
                        'Gross Weight' => (string)@$row->gross_weight,
                        'Bin Location(UOM)' => (string)@$row->unitofmeasures->title,
                    ];
                    $sumH += ($row->getAllFromStockMoves ? $row->getAllFromStockMoves->sum('qauntity') : 0);
                }
            }
            $arrays[] = ['Stock Id Code' => '',
                'Title' => '',
                'Item Category' => '',
                'Pack Size' => '',
                'Standard Cost' => '',
                'Selling Price' => 'Total',
                'Quantity' => $sumH,
                'Tax Category' => '',
                'Default Store' => '',
                'Gross Weight' => '',
                'Bin Location(UOM)' => '',
            ];

            return \Excel::create('inventory-items-' . date('Y-m-d-H-i-s'), function ($excel) use ($arrays) {
                $excel->sheet('mySheet', function ($sheet) use ($arrays) {
                    $sheet->fromArray($arrays);
                });
            })->export('xls');
        } catch (\Exception $th) {
            $request->session()->flash('danger', 'Something went wrong');
            return redirect()->back();
        }
    }
}
