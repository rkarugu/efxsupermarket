<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaNumerSeriesCode;
use App\Model\WaStockMove;
use App\Model\WaUnitOfMeasure;
use App\Models\OpeningBalancesWaStockCheckFreeze;
use App\Models\OpeningBalancesWaStockCheckFreezeItem;
use App\Models\OpeningBalancesWaStockCount;
use App\Models\OpeningBalancesWaStockCountDeviation;
use App\Models\OpeningBalancesWaStockCountProcess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ExcelDownloadService;


class OpeningBalancesStockTakeController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'opening-balances-stock-takes';
        $this->title = 'Opening Balances Stock Takes';
        $this->pmodule = 'opening-balances-stock-take';
    }

    public function index() {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = OpeningBalancesWaStockCheckFreeze::with(['getAssociateLocationDetail','getAssociateUserDetail','unit_of_measure'])->withCount('getAssociateItems')->orderBy('id', 'desc')->get();
            $breadcum = [$title => route('admin.stock-takes.create-stock-take-sheet'), 'Listing' => ''];
            return view('admin.stock_takes.opening_balances_index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function freezeTable(){
        DB::table('opening_balances_wa_stock_check_freezes')->delete();
        DB::table('opening_balances_wa_stock_counts')->delete();
        DB::table('opening_balances_wa_stock_check_freeze_items')->delete();
        Session::flash('success', 'Opening Balances Stock Take Tables Truncated Successfully.');
        return redirect()->back();
    }
    public function addStockCheckFile(Request $request)
    {
        $wa_inventory_category_ids = $request->wa_inventory_category_id ?? WaInventoryCategory::pluck('id')->toArray();
        $logged_user_profile = getLoggeduserProfile();
        $stock_check_row_created = 0;
        $units = WaUnitOfMeasure::with('get_uom_location')->whereIn('id', $request->wa_unit_of_measure_id)->get();
        foreach($units as $unit){
            foreach ($wa_inventory_category_ids as $category_id) {
                $items = WaInventoryItem::where('wa_inventory_location_uom.uom_id', $unit->id)
                    ->where('wa_location_and_stores.id', $request->wa_location_and_store_id)
                    ->where('wa_inventory_items.wa_inventory_category_id', $category_id)
                    ->join('wa_inventory_location_uom', function ($e) {
                        $e->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id');
                    })
                    ->leftJoin('wa_location_and_stores', function ($e) {
                        $e->on('wa_inventory_location_uom.location_id', '=', 'wa_location_and_stores.id');
                    })
                    ->groupBy('wa_inventory_items.id')
                    ->select('wa_inventory_items.*')
                    ->get();
                $exits_row = OpeningBalancesWaStockCheckFreezeItem::where([['wa_unit_of_measure', @$unit->title], ['wa_location_and_store_id', $request->wa_location_and_store_id], ['item_category_id', $category_id]])->first();
                if ($exits_row) {
                    foreach ($items as $key => $item_row) {
                        $available_quantity = getItemAvailableQuantity($item_row->stock_id_code, $request->wa_location_and_store_id);
                        if (!empty($request->quantities_zero) && empty($available_quantity)) {
                            continue;
                        }
                        $stock_check_id = $exits_row->opening_balances_wa_stock_check_freeze_id;
                        $entity = OpeningBalancesWaStockCheckFreezeItem::firstOrNew(
                            [
                                'wa_inventory_item_id' => $item_row->id,
                                'opening_balances_wa_stock_check_freeze_id' => $stock_check_id,
                                'wa_location_and_store_id' => $request->wa_location_and_store_id,
                                'item_category_id' => $category_id,
                                'wa_unit_of_measure' => @$unit->id,
                            ]
                        );
    
                        $entity->quantity_on_hand = $available_quantity;
                        $entity->save();
                    }
                } else {
                    if ($stock_check_row_created == 0) {
                        $entity_stock_check = new OpeningBalancesWaStockCheckFreeze();
                        $entity_stock_check->wa_location_and_store_id = $request->wa_location_and_store_id;
                        $entity_stock_check->user_id = $logged_user_profile->id;
                        $entity_stock_check->wa_unit_of_measure_id = $unit->id;
                        $entity_stock_check->wa_inventory_category_ids = serialize($wa_inventory_category_ids);
                        $entity_stock_check->save();
                        $stock_check_row_created = 1;
                    }
                    foreach ($items as $key => $item_row) {
                        $available_quantity = getItemAvailableQuantity($item_row->stock_id_code, $request->wa_location_and_store_id);
                        if (!empty($request->quantities_zero) && empty($available_quantity)) {
                            continue;
                        }
                        $entity = new OpeningBalancesWaStockCheckFreezeItem();
    
                        $entity->opening_balances_wa_stock_check_freeze_id = $entity_stock_check->id;
                        $entity->wa_inventory_item_id = $item_row->id;
                        $entity->wa_location_and_store_id = $request->wa_location_and_store_id;
                        $entity->item_category_id = $category_id;
                        $entity->quantity_on_hand = $available_quantity;
                        $entity->wa_unit_of_measure = @$unit->id;
                        $entity->save();
                    }
                }
            }
        }
        Session::flash('success', 'Processed successfully.');
        return redirect()->back();
    }
    public function printToPdf($id)
    {
        $categories = WaInventoryCategory::all();
        $freeze = OpeningBalancesWaStockCheckFreeze::find($id);
        $frozenBins = OpeningBalancesWaStockCheckFreezeItem::where('opening_balances_wa_stock_check_freeze_id', $id)
            ->pluck('wa_unit_of_measure')
            ->toArray();
       
        $user = Auth::user();
        if(isset($user->role_id) && $user->role_id == 152){
            $bins = WaUnitOfMeasure::where('wa_unit_of_measures.id', $user->wa_unit_of_measures_id)
                ->where('wa_location_store_uom.location_id', $freeze->wa_location_and_store_id)
                ->whereIn('wa_unit_of_measures.id', $frozenBins)
                ->leftJoin('wa_location_store_uom', function($e){
                    $e->on('wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id');
            })->get();
        }else{
            $bins = WaUnitOfMeasure::where('wa_location_store_uom.location_id', $freeze->wa_location_and_store_id)
                ->whereIn('wa_unit_of_measures.id', $frozenBins)
                ->leftJoin('wa_location_store_uom', function($e){
                    $e->on('wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id');
                })->get();

        }
        $freezeItems = DB::table('opening_balances_wa_stock_check_freeze_items')
        ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'opening_balances_wa_stock_check_freeze_items.wa_inventory_item_id')
        ->select('opening_balances_wa_stock_check_freeze_items.*', 'wa_inventory_items.stock_id_code', 'wa_inventory_items.title' )
        ->where('opening_balances_wa_stock_check_freeze_items.wa_location_and_store_id', '=', $freeze->wa_location_and_store_id)
        ->where('opening_balances_wa_stock_check_freeze_items.opening_balances_wa_stock_check_freeze_id', $id)
        ->orderBy('wa_inventory_items.title', 'asc')
        ->get();
        
        $data = [];
        foreach ($freezeItems as $key => $item) {
            $payload = [];
            $payload['stock_id_code'] = $item->stock_id_code;
            $payload['title'] = $item->title;
            $payload['quantity at hand'] = $item->quantity_on_hand;
            $data [] = $payload;
        }
                set_time_limit(0);
                ini_set("memory_limit",-1);
                ini_set('max_execution_time', 0);

        $pdf = Pdf::loadView('admin.stock_takes.print', compact('categories', 'freeze', 'freezeItems','bins'));
        return $pdf->download('stock_check' . date('Y_m_d_h_i_s') . '.pdf');
    }
    public function stockCountsIndex(){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'opening-balances-stock-counts';
        $title = 'Opening Balances Stock Counts';
        $model = 'opening-balances-stock-counts';
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists =OpeningBalancesWaStockCount::with('getAssociateItemDetail', 'getAssociateLocationDetail','category','getUomDetail')->get();
            $breadcum = [$title => route('admin.stock-counts'), 'Listing' => ''];
            return view('admin.stock_counts.opening_balances_index', compact('lists','title','model', 'breadcum', 'pmodule', 'permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    } 
    public function enterStockCounts() {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'opening-balances-stock-counts';
        $title = 'Opening Balances Stock Counts';
        $model = 'opening-balances-stock-counts';

        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $user = Auth::user();
            if(isset($user->role_id) && $user->role_id == 152){
                $location_ids = OpeningBalancesWaStockCheckFreeze::distinct()
                    ->where('wa_location_and_store_id', $user->wa_location_and_store_id)->pluck('wa_location_and_store_id');
            }else{
                $location_ids = OpeningBalancesWaStockCheckFreeze::distinct()->pluck('wa_location_and_store_id');
            }
            $location_list =\App\Model\WaLocationAndStore::getLocationListByIds($location_ids);      
            $category_list = getInventoryCategoryList();
            $breadcum = [$title => route('admin.stock-counts'), 'Enter Stock Counts' => ''];
            return view('admin.stock_counts.opening_balances_enter_stock_counts', compact('title','category_list', 'location_list', 'model', 'breadcum', 'pmodule', 'permission', 'user'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function updateStockCounts(Request $request){
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'wa_location_and_store_id' => 'required',
                'wa_inventory_category_id' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $logged_user_profile = getLoggeduserProfile();
            $location_id = $request->wa_location_and_store_id;
            $category_id = $request->wa_inventory_category_id;
            $wa_unit_of_measure_id = $request->wa_unit_of_measure_id;
            if($category_id == 0){
                $items = WaInventoryItem::with('getUnitOfMeausureDetail')
                ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', '=', 'wa_inventory_items.id')
                ->where('wa_inventory_location_uom.uom_id', $request->wa_unit_of_measure_id)
                ->get();
            }else{
                $items = WaInventoryItem::with('getUnitOfMeausureDetail')
                ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', '=', 'wa_inventory_items.id')
                ->where('wa_inventory_location_uom.uom_id', $request->wa_unit_of_measure_id)
                ->where('wa_inventory_category_id', $category_id)->get();

            }
            //TODO: use items coming from the request
            foreach($items as $key => $row){
                $frozenQuantity = OpeningBalancesWaStockCheckFreezeItem::latest()->where('wa_location_and_store_id', $location_id)
                ->where('wa_inventory_item_id', $row->inventory_id)->first();
                
               
                if($frozenQuantity){
                    $entity = OpeningBalancesWaStockCount::firstOrNew(
                        [
                            'wa_location_and_store_id' => $location_id,
                            'wa_inventory_item_id' => $row->inventory_id,
                        ]
                    );
                    $entity->user_id = $logged_user_profile->id;
                    $entity->item_name = $row->title;
                    $entity->category_id = $row->wa_inventory_category_id;
                    $entity->uom = $wa_unit_of_measure_id;
                    $entity->quantity = isset($request['quantity_'.$row->inventory_id]) ? $request['quantity_'.$row->inventory_id] ?? 0 : 0;
                    $entity->reference = isset($request['reference_'.$row->inventory_id]) ? $request['reference_'.$row->inventory_id] : '';
                    $entity->save();
                    //? not in original implementation. should it be saved? 
                    // $variance = new WaStockCountVariation();
                    // $variance->user_id = $logged_user_profile->id;
                    // $variance->wa_location_and_store_id = $location_id;
                    // $variance->wa_inventory_item_id = $row->inventory_id;
                    // $variance->category_id = $row->wa_inventory_category_id;
                    // $variance->quantity_recorded = !empty($request['quantity_'.$row->inventory_id]) ? $request['quantity_'.$row->inventory_id] : null;
                    // $variance->current_qoh = $frozenQuantity->quantity_on_hand;
                    // if($frozenQuantity->quantity_on_hand < 0){
                    //     $variance->variation = isset($request['quantity_'.$row->inventory_id]) ? ($variance->quantity_recorded + $variance->current_qoh) : null;
                    // }else{
                    //     $variance->variation = isset($request['quantity_'.$row->inventory_id]) ? ($variance->quantity_recorded - $variance->current_qoh) : null;
                    // }
                    // $variance->uom_id =  $wa_unit_of_measure_id;
                    // $variance->reference =  isset($request['reference_'.$row->inventory_id]) ? $request['reference_'.$row->inventory_id] : '';
                    // $variance->save();

                }      
    
            }
            DB::commit();
    
            Session::flash('success', 'Opening Balances Stock Counts has been saved successfully.');
            return redirect()->route('admin.opening-balances-stock-counts');
        } catch (\Throwable $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();

        }
       
    }
    public function stockCountFormListAjax(Request $request) {
        $location_id = $request->location_id;
        $category_id = $request->category_id;
        $wa_unit_of_measure_id = $request->wa_unit_of_measure_id;
        $items = [];
        if(!empty($location_id) && !empty($wa_unit_of_measure_id)){
            if($category_id == 0){
                $counts = OpeningBalancesWaStockCount::where('wa_location_and_store_id',$location_id)->pluck('wa_inventory_item_id')->toArray();
                $dd = array_unique($counts);
                $items = OpeningBalancesWaStockCheckFreezeItem::select(
                    'wa_inventory_items.id as inventory_id',
                    'wa_inventory_items.stock_id_code as stock_id_code',
                    'wa_inventory_items.title as title',
                    'pack_sizes.title as pack_size',
                )
                
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'opening_balances_wa_stock_check_freeze_items.wa_inventory_item_id')
                ->leftJoin('pack_sizes', 'pack_sizes.id', '=','wa_inventory_items.pack_size_id')
                ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', '=', 'wa_inventory_items.id')
                ->where('wa_inventory_location_uom.uom_id', $wa_unit_of_measure_id)
                ->whereNotIn('wa_inventory_items.id',$dd)->get();

            }else{
                $counts = OpeningBalancesWaStockCount::where('wa_location_and_store_id',$location_id)->where('category_id',$category_id)->pluck('wa_inventory_item_id')->toArray();
                $dd = array_unique($counts);
                $items = OpeningBalancesWaStockCheckFreezeItem::select(
                    'wa_inventory_items.id as inventory_id',
                    'wa_inventory_items.stock_id_code as stock_id_code',
                    'wa_inventory_items.title as title',
                    'pack_sizes.title as pack_size',
                )
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'opening_balances_wa_stock_check_freeze_items.wa_inventory_item_id')
                ->leftJoin('pack_sizes', 'pack_sizes.id', '=','wa_inventory_items.pack_size_id')
                ->leftJoin('wa_inventory_location_uom', 'wa_inventory_location_uom.inventory_id', '=', 'wa_inventory_items.id')
                ->where('wa_inventory_location_uom.uom_id', $wa_unit_of_measure_id)
                ->whereNotIn('wa_inventory_items.id',$dd)->where('wa_inventory_category_id', $category_id)->get();

            }
           
            
        }
        return view('admin.stock_counts.stock_count_form',compact('items'));
    }
    public function destroy($id){
        OpeningBalancesWaStockCount::destroy($id);
        Session::flash('success', 'Opening Balances Stock  Count Record has been deleted successfully.');
        return redirect()->back();
    }
    public function updateStockRow(Request $request){
        if($request->quantity < 0){
            Session::flash('warning', 'Quantity should be greater than 0.');
            return redirect()->back();
        }
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission['stock-counts___edit']) && $permission != 'superadmin') {
            Session::flash('warning', 'Restriced: You dont have permission to perform this operation');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
        $entity = new OpeningBalancesWaStockCount();
        $entity->exists = true;
        $entity->id = $request->row_id;
        $entity->quantity = $request->quantity;
        $entity->save();
        $newEntity = OpeningBalancesWaStockCount::find($entity->id);
        // ? Not in original implementation
        
        // $stockCountVariationEntity = WaStockCountVariation::latest()
        //     ->where('wa_inventory_item_id', $newEntity->wa_inventory_item_id)
        //         ->where('uom_id', $newEntity->uom)
        //         ->where('category_id', $newEntity->category_id)
        //     ->first();
        // if($stockCountVariationEntity->is_processed != 0){
        //     DB::rollback();
        //     Session::flash('warning', 'Cannot update quantity for processed items');
        //     return redirect()->back();

        // }
        // $stockCountVariationEntity->quantity_recorded = $request->quantity;
        // $stockCountVariationEntity->variation = $request->quantity - $stockCountVariationEntity->current_qoh;
        // $stockCountVariationEntity->save();
        DB::commit();

        Session::flash('success', 'Quantity has been updated successfully.');
        return redirect()->back();
        } catch (\Throwable $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
       
        
    }
    public function compareCountsVsStockCheck(){
        $locationAndStores  = getStoreLocationDropdown();
         $permission = $this->mypermissionsforAModule();
         $pmodule = $this->pmodule;
         $title = 'Compare Counts Vs Stock Check Data';
         $model = 'opening-balances-stock-counts-compare';
         if (isset($permission['compare-counts-vs-stock-check___view']) || $permission == 'superadmin') {
             $breadcum = [$title => route('admin.stock-counts'), $title => ''];
             return view('admin.stock_counts.opening_balances_compare_counts_vs_stock_check', compact('title','model', 'breadcum', 'pmodule', 'permission','locationAndStores'));
         }
         else {
             Session::flash('warning', 'Invalid Request');
             return redirect()->back();
         }
    }
    public function compareCountsVsStockCheckUpdate(Request $request){
        $action_type = $request->action_type;
        $data = OpeningBalancesWaStockCount::with('getAssociateItemDetail.getInventoryCategoryDetail', 'getAssociateLocationDetail', 'getUomDetail')
        ->where('wa_location_and_store_id',$request->wa_location_and_store_id)
        ->get();
        $items_by_location_category = $category_list = [];
        $logged_user_profile = getLoggeduserProfile();
        $batch_date =  date('Y-m-d H:i:s');
        $deviation_data = [];

        $StockAdjustmentdata = [];
        $stockMovedata = [];
        $drgltrans = [];
        $crgltrans = [];

        foreach($data as $key => $row){
            $stock_id_code =  $row->getAssociateItemDetail->stock_id_code;
            $wa_location_and_store_id = $row->wa_location_and_store_id;
            $item_available_quantity = getItemAvailableQuantity($stock_id_code, $wa_location_and_store_id);

            $adjustment = $row->quantity - $item_available_quantity;
            if($action_type == 2){
                $item_available_quantity = $row->quantity;
            }
            
            $category_id = $row->getAssociateItemDetail->getInventoryCategoryDetail->id;
            $category_list[$category_id] = [
                'category_description'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_description,
                'category_code'=>$row->getAssociateItemDetail->getInventoryCategoryDetail->category_code
            ];

            $row->quantity_on_hand = $item_available_quantity;
            $row->adjustment = $adjustment;
            $items_by_location_category[$wa_location_and_store_id][$category_id][] = $row;
        }
        if($action_type == 2){
	        $process = OpeningBalancesWaStockCountProcess::where('wa_location_and_store_id', $request->wa_location_and_store_id)->count();
	        if($process>0){
		        $process = OpeningBalancesWaStockCountProcess::where('wa_location_and_store_id', $request->wa_location_and_store_id)->first();			        
	        }else{
		        $process = new OpeningBalancesWaStockCountProcess();
	        }
		    $process->wa_location_and_store_id = $request->wa_location_and_store_id;
		    $process->selected_type = 2;
	        $process->save(); 
        }
        $location_list = getStoreLocationDropdown();
        $pdf = Pdf::loadView('admin.stock_counts.compare_counts_print', compact('items_by_location_category', 'location_list', 'category_list', 'data'));
        return $pdf->download('compare_counts_vs_stock_check_data_' . date('Y_m_d_h_i_s') . '.pdf');
            
    }
    public function stockcountprocess(){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'stock-count-process';
        $title = $this->title;
        $model = 'opening-balances-stock-count-process';
             $data = OpeningBalancesWaStockCountProcess::get();
            $breadcum = [$title => route('admin.stock-counts'), 'Listing' => ''];
            return view('admin.stock_counts.opening_balances_stock_count_process', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
  
    }
    public function compareCountsVsStockCheckProcess(Request $request,$wa_location_and_store_id){

        $action_type = 2;
        $data = OpeningBalancesWaStockCount::with('getAssociateItemDetail','getAssociateItemDetail.getAllFromStockMoves','getAssociateItemDetail.getInventoryCategoryDetail','getAssociateItemDetail.getInventoryCategoryDetail.getusageGlDetail', 'getAssociateLocationDetail')
        ->where('wa_location_and_store_id',$wa_location_and_store_id)
        ->get();
        $items_by_location_category = $category_list = [];
        $logged_user_profile = getLoggeduserProfile();
        $batch_date =  date('Y-m-d H:i:s');
        $deviation_data = [];

        $StockAdjustmentdata = [];
        $stockMovedata = [];
        $drgltrans = [];
        $crgltrans = [];

        $series_module = WaNumerSeriesCode::where('module','GRN')->first();
        $adj_series_module = WaNumerSeriesCode::where('module','ITEM ADJUSTMENT')->first();
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
        $grn_number = getCodeWithNumberSeriesBillClose('GRN');
        $dateTime = date('Y-m-d H:i:s');
        foreach($data as $key => $row){
	        
            $stock_id_code =  $row->getAssociateItemDetail->stock_id_code;
            $wa_location_and_store_id = $row->wa_location_and_store_id;
            $item_available_quantity = getItemAvailableQuantity($stock_id_code, $wa_location_and_store_id);

            $adjustment = $row->quantity - $item_available_quantity;
            if($action_type == 2){
                
                if($adjustment != 0){

                    // $entity = new StockAdjustment();
                    // $entity->user_id = $logged_user_profile->id;
                    // $entity->item_id = $row->wa_inventory_item_id;
                    // $entity->wa_location_and_store_id = $row->wa_location_and_store_id;
                    // $entity->adjustment_quantity = $adjustment;
                    // $entity->comments = '';
                    // $entity->item_adjustment_code = getCodeWithNumberSeries('ITEM ADJUSTMENT');
                    // $entity->save();	                              
	                $stockMovedata[] = [
	                    'user_id'=> $logged_user_profile->id,
	                    'stock_adjustment_id'=> "2",
	                    'restaurant_id'=> $logged_user_profile->restaurant_id,
	                    'standard_cost'=> $row->getAssociateItemDetail->standard_cost,
	                    'qauntity'=> $adjustment,
	                    'stock_id_code'=> $row->getAssociateItemDetail->stock_id_code,
	                    'wa_location_and_store_id'=> $row->wa_location_and_store_id,
	                    'grn_type_number'=> $series_module->type_number,
	                    'grn_last_nuber_used'=> $series_module->last_number_used,
	                    'price'=> $row->getAssociateItemDetail->selling_price,
	                    'created_at'=> $batch_date,
	                    'updated_at'=> $batch_date,
	                    'refrence'=> '',
                        'new_qoh' => ($row->getAssociateItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',@$row->wa_location_and_store_id)->sum('qauntity') ?? 0) + $adjustment,
                        'wa_inventory_item_id'=>$row->getAssociateItemDetail->id
	                ];
                    if($adjustment < '0'){
                         $draccount = $row->getAssociateItemDetail->getInventoryCategoryDetail->getusageGlDetail->account_code; 
                    }else{
	                     $draccount = $row->getAssociateItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code; 	                
	                }             
                $drgltrans[] = [
                    'grn_type_number'=> $series_module->type_number,
                    'stock_adjustment_id'=> "2",
                    'transaction_type'=> $adj_series_module->description,
                    'grn_last_used_number'=> $series_module->last_number_used,
                    'trans_date'=> $dateTime,
                    'restaurant_id'=> getLoggeduserProfile()->restaurant_id,
                    'period_number'=> $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
                    'amount'=> abs($row->getAssociateItemDetail->standard_cost * $adjustment),
                    'account'=> $draccount,
                    'created_at'=> $batch_date,
                    'updated_at'=> $batch_date,
                    'narrative'=> $row->getAssociateItemDetail->stock_id_code.'/'.$row->getAssociateItemDetail->title.'/'.$row->getAssociateItemDetail->standard_cost.'@'.$adjustment,
                ];
                if($adjustment < '0'){
                     $craccount = $row->getAssociateItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code; 
                }else{
                     $craccount = $row->getAssociateItemDetail->getInventoryCategoryDetail->getPricevarianceGlDetail->account_code; 
                }
                $crgltrans[] = [
                    'grn_type_number'=> $series_module->type_number,
                    'stock_adjustment_id'=> "2",
                    'transaction_type'=> $adj_series_module->description,
                    'grn_last_used_number'=> $series_module->last_number_used,
                    'trans_date'=> $dateTime,
                    'restaurant_id'=> getLoggeduserProfile()->restaurant_id,
                    'period_number'=> $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
                    'amount'=> abs($row->getAssociateItemDetail->standard_cost * $adjustment),
                    'account'=> $craccount,
                    'created_at'=> $batch_date,
                    'updated_at'=> $batch_date,
                    'narrative'=> $row->getAssociateItemDetail->stock_id_code.'/'.$row->getAssociateItemDetail->title.'/'.$row->getAssociateItemDetail->standard_cost.'@'.$adjustment,
                ];
                  //  updateUniqueNumberSeries('GRN',$grn_number);
                }
                
                $deviation_data[] = [
                    'batch_date'=> $batch_date,
                    'wa_inventory_item_id'=> $row->wa_inventory_item_id,
                    'wa_location_and_store_id'=> $row->wa_location_and_store_id,
                    'uom'=> $row->uom,
                    'quantity'=> $row->quantity,
                    'quantity_on_hand'=> $item_available_quantity,
                    'created_at'=> $batch_date,
                    'updated_at'=> $batch_date,
                ];
                $item_available_quantity = $row->quantity;
            }
        }
        if($action_type == 2){
            WaStockMove::insert($stockMovedata); 
            WaGlTran::insert($drgltrans); 
            WaGlTran::insert($crgltrans); 
            OpeningBalancesWaStockCountDeviation::insert($deviation_data); 
            DB::table('wa_stock_counts')->where('wa_location_and_store_id',$wa_location_and_store_id)->delete();
             DB::table('wa_stock_count_process')->where('wa_location_and_store_id',$wa_location_and_store_id)->delete();

        }
            Session::flash('success', 'Processed Successfully.');
		return redirect()->back();            
    }  
    public function deviationReport(){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'opening-balances-deviation-report';
        if (isset($permission['deviation-report___view']) || $permission == 'superadmin') {
            $data = OpeningBalancesWaStockCountDeviation::select('batch_date',DB::raw('COUNT(batch_date) as batch_date_count'))->orderBy('batch_date')->groupBy('batch_date')->get();
            $breadcum = [$title => route('admin.stock-counts'), 'Listing' => ''];
            return view('admin.stock_counts.opening_balances_deviation_report', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }
    public function deviationReportPdf($date){
        $category_list = $items_by_location_category = [];
        $data = OpeningBalancesWaStockCountDeviation::where('batch_date', $date)->with('getAssociateItemDetail', 'getAssociateItemDetail.getInventoryCategoryDetail', 'getUomDetail')->get();
        foreach($data as $key => $row){
            $wa_location_and_store_id = $row->wa_location_and_store_id;
            $item_available_quantity = $row->quantity_on_hand;
            $adjustment = $row->quantity - $item_available_quantity;
            
            $category_id = @$row->getAssociateItemDetail->getInventoryCategoryDetail->id;
            $category_list[$category_id] = [
                'category_description'=>@$row->getAssociateItemDetail->getInventoryCategoryDetail->category_description,
                'category_code'=>@$row->getAssociateItemDetail->getInventoryCategoryDetail->category_code
            ];

            $row->quantity_on_hand = $item_available_quantity;
            $row->adjustment = $adjustment;
            $items_by_location_category[$wa_location_and_store_id][$category_id][] = $row;
        }
        $location_list = getStoreLocationDropdown();
        return view('admin.stock_counts.opening_balances_compare_counts_print', compact('items_by_location_category', 'location_list', 'category_list', 'data'));
        // return $pdf->download('compare_counts_vs_stock_check_data_' . date('Y_m_d_h_i_s') . '.pdf');
    }
    
    public function deviationReportExcel($date){
        
        $data = OpeningBalancesWaStockCountDeviation::where('batch_date', $date)->with('getAssociateItemDetail','getAssociateItemDetail.getInventoryCategoryDetail', 'getAssociateLocationDetail', 'getUomDetail')->get();
        $data_excel = [];
        foreach($data as $key => $row) {
            $item_available_quantity = $row->quantity_on_hand;
            $adjustment = $row->quantity - $item_available_quantity;
            $deviationvalue = $adjustment * @$row->getAssociateItemDetail->standard_cost;
            $data_excel[] = [
                'loccode'=>@$row->getAssociateLocationDetail->location_code,
                'stockid'=>@$row->getAssociateItemDetail->stock_id_code,
                'description'=>@$row->getAssociateItemDetail->title,
                'categoryid'=>@$row->getAssociateItemDetail->getInventoryCategoryDetail->category_code,
                'categorydescription'=>@$row->getAssociateItemDetail->getInventoryCategoryDetail->category_description,
                'uom'=>$row->getUomDetail?->title,
                'counted'=>$row->quantity,
                'system'=>$row->quantity_on_hand,
                'cost'=>@$row->getAssociateItemDetail->standard_cost,
                'deviation'=>$adjustment,
                'deviationvalue'=>$deviationvalue,
            ];
        }
        // return Excel::create('deviation_reports', function($excel) use ($data_excel) {
        //     $excel->sheet('mySheet', function($sheet) use ($data_excel)
        //     {
        //         $sheet->fromArray($data_excel);
        //     });
        // })->download('xls');
        //downloadd using general excel download service
        return ExcelDownloadService::download('opening_balances_stock_deviation_report', collect($data_excel),  ['LOCATION', 'STOCK ID CODE', 'DESCRIPTION', 'CATEGORY ID', 'CATEGORY', 'BIN', 'COUNTED', 'SYSTEM', 'COST', 'DEVIATION', 'DEVIATION VALUE']);       
    }
    
    
}
