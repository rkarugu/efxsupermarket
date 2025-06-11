<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\TyreInventory;
use App\Model\WaLocationAndStore;
use App\Model\StockAdjustment;
use App\Model\WaAccountingPeriod;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaGlTran;
use App\User;
use App\Model\AdjustmentsSummary;
use Auth;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use Response;
use PDF;

class TyreInventoriesController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $this->model = 'tyre_inventories';
        $this->title = 'Tyre Inventories';
        $this->pmodule = 'tyre_inventories';
    } 

    /*
    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            
            $lists = TyreInventory::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyre_inventories.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }
    */
    
    
    public function index(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin'){
            
            if($request->ajax()){


                $sortable_columns = ['id','code','tyre_size','tyre_make','type','pattern','cost','status'];


                $limit          = $request->input('length');
                $start          = $request->input('start');
                $search         = $request['search']['value'];
                $orderby        = $request['order']['0']['column'];
                $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
                $draw           = $request['draw'];  

                
                $response       = TyreInventory::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
                $totalCms       = $response['count'];
                $data = $response['response'];
                $total = 0;

                $total_new_tyre_in_stock_count=0;
                $total_in_motor_vehicle_count=0;
                $total_retread_tyre_in_stock_count=0;
                $total_tyres_in_retread_count=0;
                $total_damaged_tyre_count=0;

                foreach($data as $key => $re){
                    
                    $total_new_tyre_in_stock_count+=$data[$key]['new_tyre_in_stock_count'];
                    $total_in_motor_vehicle_count+=$data[$key]['in_motor_vehicle_count'];
                    $total_retread_tyre_in_stock_count+=$data[$key]['retread_tyre_in_stock_count'];
                    $total_tyres_in_retread_count+=$data[$key]['tyres_in_retread_count'];
                    $total_damaged_tyre_count+=$data[$key]['damaged_tyre_count'];

                    $data[$key]['uom'] = (isset($re['getUnitOfMeausureDetail']))?@$re['getUnitOfMeausureDetail']['title']:'';
                    $data[$key]['qauntity'] = (isset($re['getAllFromStockMoves']))?$re['getAllFromStockMoves']->sum('qauntity') : 0;
                    $data[$key]['qty_on_order'] = getQtyOnOrder($re['id']);
                    //dd($re['getAllFromStockMoves']->sum('qauntity'));
                    $data[$key]['links'] = '<div style="display:flex">';
                    if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin'){
                         $data[$key]['links'] .= '<a href="'.route($this->pmodule.'.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin'){
                         $data[$key]['links'] .= '<form action="'.route($this->pmodule.'.destroy',$re['id']).'" method="POST"  class="deleteMe"> <button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                         <input type="hidden" value="DELETE" name="_method">
                         '.csrf_field().'
                         </form> &nbsp;';
                    }

                    if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin'){
                         $data[$key]['links'] .= '&nbsp; <a href="'.route($this->pmodule.'.show',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }

                    if(isset($permission[$this->pmodule.'___manage-stock']) || $permission == 'superadmin'){
                        
                        $link_popup = route('admin.tyre.adjust-item-stock-form', $re['id']);     
                        $data[$key]['links'] .= '&nbsp; 
                         <a href="javascript:void(0);" data-id="'.$re['id'].'" onclick="manageStockPopup('.$re['id'].')" class="btn btn-danger btn-sm"><i class="fa fa-bolt" aria-hidden="true"></i></a>';
                    }

                    if($re['serialised'] == 'Yes'){
                    
                        $data[$key]['links'] .=  "<span class='span-action'><a class='btn btn-danger btn-sm' href='".route('tyre.stockMovesSerials', $re['id'])."' title='Serials' style=><i class='fa fa-tasks' aria-hidden='true'></i></a></span>";
                    }



                    $data[$key]['links'] .= '</div>';


                   /* $data[$key]['links'] = '<div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Dropdown Example
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                          <li><a href="#">HTML</a></li>
                          <li><a href="#">CSS</a></li>
                          <li><a href="#">JavaScript</a></li>
                        </ul>
                      </div>';*/

                   // $data[$key]['finalamount'] = manageAmountFormat($re['totalexpense']+$re['totalfuel']);

                    //$total += ($re['totalexpense']+$re['totalfuel']);
                }


                $response['response'] = $data;
                $return = [
                    "draw"              =>  intval($draw),
                    "recordsFiltered"   =>  intval( $totalCms),
                    "recordsTotal"      =>  intval( $totalCms),
                    "data"              =>  $response['response'],
                    "total"              =>  manageAmountFormat($total),
                    "total_new_tyre_in_stock_count" =>  manageAmountFormat($total_new_tyre_in_stock_count),
                    "total_in_motor_vehicle_count" =>  manageAmountFormat($total_in_motor_vehicle_count),
                    "total_retread_tyre_in_stock_count" =>  manageAmountFormat($total_retread_tyre_in_stock_count),
                    "total_tyres_in_retread_count" =>  manageAmountFormat($total_tyres_in_retread_count),
                    "total_damaged_tyre_count" =>  manageAmountFormat($total_damaged_tyre_count)

                ];
                return $return;
            }


            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.tyre_inventories.index',compact('title','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function adjustItemStockForm($id=''){
        $item_row = TyreInventory::findOrFail();
        $locations = WaLocationAndStore::getLocationList();
        return view('admin.tyre_inventories.adjust_item_form',compact('item_row','locations'));
    }
    
    public function exportPdf(Request $request){
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            $response       = TyreInventory::getData(9999999999,1,NULL,'tyre_inventories.id', 'DESC',$request);
            $data = $response['response'];
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            $pdf = PDF::loadView('admin.tyre_inventories.print',compact('user','title','data','model','breadcum','pmodule','permission','request'));
            return $pdf->download('delivery_note_'.date('Y_m_d_h_i_s').'.pdf');
        
    }


    public function tyres_list(Request $request){
        # Payment Accounts
        $data = TyreInventory::select(['id as id','title as text']);
        if($request->q){
            $data = $data->orWhere('title','LIKE',"%$request->q%");
            $data = $data->orWhere('stock_id_code','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }
    
    public function stockMovesSerials($id)
    {
        
        $data['id'] = $id;
        $data['pmodule'] = $this->pmodule;
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        // $data['serials'] = \App\Model\WaPoiStockSerialMoves::where('wa_inventory_item_id',$id)->paginate(\Config::get('params.list_limit_admin'));
        $data['serials'] = \App\Model\WaPoiStockSerialMoves::where('wa_inventory_item_id',$id)->orderBy('created_at','DESC')->get();

        return view('admin.tyre_inventories.serialItems')->with($data);
    }

    public function stockMovesSerialsHistory($wa_poi_stock_serial_moves_id,$inventory_item_id){
        $data['id'] = $wa_poi_stock_serial_moves_id;
        $data['inventory_item_id'] = $inventory_item_id;
        $data['pmodule'] = $this->pmodule;
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        // $data['serials'] = \App\Model\WaPoiStockSerialMoves::where('wa_inventory_item_id',$id)->paginate(\Config::get('params.list_limit_admin'));
        $data['history'] = \App\Model\WaPoiStockSerialMovesHistory::where('wa_poi_stock_serial_moves_id',$wa_poi_stock_serial_moves_id)->orderBy('created_at','DESC')->get();

        return view('admin.tyre_inventories.serialHistory')->with($data);
    }
   

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            $all_taxes = $this->getAllTaxFromTaxManagers();
            return view('admin.tyre_inventories.create',compact('title','model','breadcum','all_taxes'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [  
                'stock_id_code' => 'required|unique:tyre_inventories',
                'inventory_item_type'=>'required',

            ]);
            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{

                $row = new TyreInventory();
                $row->stock_id_code= $request->stock_id_code;
                $row->title=$request->title;
                $row->wa_inventory_category_id= $request->wa_inventory_category_id;
                $row->description= $request->description;
                $row->tyre_size= $request->tyre_size;
                $row->tyre_make= $request->tyre_make;
                $row->wa_unit_of_measure_id= $request->wa_unit_of_measure_id;
                $row->tax_manager_id= $request->tax_manager_id;
                $row->inventory_item_type= $request->inventory_item_type;
                $row->pattern= $request->pattern;
                $row->standard_cost= $request->standard_cost;
                //$row->status= $request->status;
                //$row->serialised= $request->batch_type != 'Controlled' ? $request->serialised : 'Yes'; 
                $row->serialised= $request->serialised ? $request->serialised : 'No'; 
                $row->save();

                updateUniqueNumberSeries('TYRE INVENTORY ITEM',$request->stock_id_code);
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }
            
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function show($id){   
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin')
            {
                $row =  TyreInventory::findOrFail($id);
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.tyre_inventories.show',compact('title','model','breadcum','row')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function edit($id)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  TyreInventory::findOrFail($id);
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    $all_taxes = $this->getAllTaxFromTaxManagers();
                    return view('admin.tyre_inventories.edit',compact('title','model','breadcum','row','all_taxes')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $id){
        try{
             $row =  TyreInventory::findOrFail($id);
             $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|unique:tyre_inventories,stock_id_code,' . $row->id,
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
            
                $row->stock_id_code= $request->stock_id_code;
                $row->title= $request->title;
                $row->description= $request->description;
                $row->wa_inventory_category_id= $request->wa_inventory_category_id;
                $row->tyre_size= $request->tyre_size;
                $row->tyre_make= $request->tyre_make;
                $row->wa_unit_of_measure_id= $request->wa_unit_of_measure_id;
                $row->tax_manager_id= $request->tax_manager_id;
                $row->inventory_item_type= $request->inventory_item_type;
                $row->pattern= $request->pattern;
                $row->standard_cost= $request->standard_cost;
                //$row->status= $request->status;   
                $row->serialised= $request->serialised ? $request->serialised : 'No'; 
              
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($id){
        try{
            
            TyreInventory::where('id',$id)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function standardCost(Request $request)
    {

       $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Standard Cost';
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
             $lists = TyreInventory::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.standard.cost'),'Listing'=>''];
            return view('admin.tyre_inventories.standardCost',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }
    
    public function editStandardCost($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  TyreInventory::whereSlug($slug)->first();
                if($row)
                {
                    $this->title = 'Standard Cost';
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.standard.cost'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.tyre_inventories.editStandardCost',compact('title','model','breadcum','row')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }

    public function updateStandardCost(Request $request,$slug)
    {
        try
        {
            $row =  TyreInventory::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|unique:tyre_inventories,stock_id_code,' . $row->id,
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
            
                
                
                $row->standard_cost= $request->standard_cost;
                
                $row->prev_standard_cost= $request->old_standard_cost; 
                $row->cost_update_time= date('Y-m-d H:i:s');         
              
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.standard.cost');
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function stockMovements($StockIdCode,Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $location = WaLocationAndStore::where(['wa_branch_id'=>getLoggeduserProfile()->restaurant_id])->get();
        $lists = WaStockMove::with(['getRelatedUser','getLocationOfStore'])->where(function($w) use ($request){
            if($request->from && $request->to){
                $w->whereBetween('created_at',[$request->from.' 00:00:00',$request->to.' 23:59:59']);
            }
            if($request->location){
                $w->where('wa_location_and_store_id',$request->location);
            }
        });
        if(($request->from && $request->to) || $request->location){
            $lists = $lists->where('stock_id_code',$StockIdCode)->orderBy('id', 'asc')->get();
        }
        else {
            $lists = $lists->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->limit(20)->get();
            $lists = $lists->sort();
        }
        
        $row =  TyreInventory::where('stock_id_code',$StockIdCode)->first();
        $breadcum = [$title=>route($model.'.index'),'Stock Movement'=>'',$StockIdCode=>''];
        if($request->type == 'pdf'){
            $firstQoh = WaStockMove::where(function($w) use ($request){
                $w->where('created_at','<',$request->from.' 00:00:00');                
                if($request->location){
                    $w->where('wa_location_and_store_id',$request->location);
                }
            })
            ->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id'=>$request->location])->first();
            $pdf = \PDF::loadView('admin.tyre_inventories.stockmovement_pdf',compact('firstQoh','currentLocation','request','row','location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
            $report_name = 'Stock-Card-'.date('Y_m_d_H_i_A');
            // return $pdf->stream();
            return $pdf->download($report_name.'.pdf');
        }
        if($request->type == 'print'){
            $firstQoh = WaStockMove::where(function($w) use ($request){
                $w->where('created_at','<',$request->from.' 00:00:00');                
                if($request->location){
                    $w->where('wa_location_and_store_id',$request->location);
                }
            })
            ->where('stock_id_code',$StockIdCode)->orderBy('id', 'DESC')->first();
            $currentLocation = WaLocationAndStore::where(['id'=>$request->location])->first();
            return view('admin.tyre_inventories.stockmovement_pdf',compact('firstQoh','currentLocation','request','row','location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
        }
        return view('admin.tyre_inventories.stockmovement',compact('location','title','lists','model','breadcum','pmodule','permission', 'StockIdCode'));
    }

    public function stockStatus($StockIdCode)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $lists =  DB::select( DB::raw("SELECT SUM(`qauntity`) as total_quantity,wa_location_and_store_id from wa_stock_moves where stock_id_code = '".$StockIdCode."' group by `wa_location_and_store_id`") );


          $storeBiseQty = [];
        foreach($lists as $list)
        {
            $storeBiseQty[$list->wa_location_and_store_id] = $list->total_quantity;
        }

        $lists = WaLocationAndStore::get();
      
       

        $breadcum = [$title=>route($model.'.index'),'Stock Status'=>'',$StockIdCode=>''];
        return view('admin.tyre_inventories.stockstatus',compact('title','lists','model','breadcum','pmodule','permission','storeBiseQty'));
    }

    
   
    public function getAvailableQuantityAjax(Request $request){
        $available_quantity = getItemAvailableQuantity($request->stock_id_code, $request->location_id);
        return json_encode(['available_quantity'=>$available_quantity]);
    }
    
    public function stockManage(Request $request){
        $item_row = TyreInventory::where('id', $request->item_id)->first();
        $adjustment_quantity = $request->adjustment_quantity;
        $current_available_quantity = getItemAvailableQuantity($request->stock_id_code, $request->wa_location_and_store_id);
        
        $new_quantity = $current_available_quantity + $adjustment_quantity;
        if($new_quantity < 0){
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
        
        $series_module = WaNumerSeriesCode::where('module','GRN')->first();
          $item_adj = WaNumerSeriesCode::where('module','ITEM ADJUSTMENT')->first();
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period','1')->first();
        $grn_number = getCodeWithNumberSeries('GRN');
        $dateTime = date('Y-m-d H:i:s');
        
        $stockMove = new WaStockMove();
        $stockMove->user_id = $logged_user_profile->id;
        $stockMove->stock_adjustment_id = $entity->id;
        $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
        $stockMove->wa_location_and_store_id = $entity->wa_location_and_store_id;
        $stockMove->wa_inventory_item_id = $item_row->id;
        $stockMove->standard_cost = $item_row->standard_cost;
        $stockMove->qauntity = $adjustment_quantity;
        $stockMove->new_qoh = (@$item_row->getAllFromStockMoves->where('wa_location_and_store_id',@$entity->wa_location_and_store_id)->sum('qauntity') ?? 0) + $stockMove->qauntity;

        $stockMove->stock_id_code = $item_row->stock_id_code;
        $stockMove->grn_type_number = $series_module->type_number;
        $stockMove->document_no = $request->item_adjustment_code;
        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
        $stockMove->price = $item_row->standard_cost;
        $stockMove->refrence = $entity->comments;
        $stockMove->save();
        
        $dr =  new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $request->item_adjustment_code;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;

        $dr->account = $item_row->getInventoryCategoryDetail ? $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code : '';
        $dr->amount = abs($item_row->standard_cost * $adjustment_quantity);
        if($adjustment_quantity < '0')
        {
//             $dr->amount = '-'.abs($item_row->standard_cost * $adjustment_quantity);
      $dr->account = $item_row->getInventoryCategoryDetail ? $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code : '';
  }
        $dr->narrative = $item_row->code.'/'.$item_row->title.'/'.$item_row->standard_cost.'@'.$adjustment_quantity;
        $dr->save();
        
        $dr =  new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $request->item_adjustment_code;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;

        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
 $dr->account = $item_row->getInventoryCategoryDetail ? $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code : '';        $tamount = $item_row->standard_cost * $adjustment_quantity;

        $dr->amount = '-'.abs($tamount);
         if($adjustment_quantity < '0')
        {
  //           $dr->amount = abs($item_row->standard_cost * $adjustment_quantity);
 $dr->account = $item_row->getInventoryCategoryDetail ? $item_row->getInventoryCategoryDetail->getStockGlDetail->account_code : '';
         }
        $dr->narrative = $item_row->code.'/'.$item_row->title.'/'.$item_row->standard_cost.'@'.$adjustment_quantity;
            if($dr->save()){
             $data2 = Session::get('AdminLoggedIn');
            $alldjustmentsSummary =  new AdjustmentsSummary;
            $alldjustmentsSummary->code = $request->code;
            $alldjustmentsSummary->item_adjustment_code = $request->item_adjustment_code;
            $alldjustmentsSummary->description = $request->description;
            $alldjustmentsSummary->item_title = $request->item_slug;
            $alldjustmentsSummary->UOM = $request->uom;
            $alldjustmentsSummary->standard_cost = $request->standard_cost;
            $alldjustmentsSummary->wa_location_and_store_id = $request->wa_location_and_store_id;
            $alldjustmentsSummary->current_qty_available = $request->current_qty_available;
            $alldjustmentsSummary->comments = $request->comments;
            $alldjustmentsSummary->adjustment_quantity = $request->adjustment_quantity;
            $alldjustmentsSummary->user_id = $data2['user_id'];
            $alldjustmentsSummary->save();

        }

        updateUniqueNumberSeries('ITEM ADJUSTMENT',$request->item_adjustment_code);
        updateUniqueNumberSeries('GRN',$grn_number);
        Session::flash('success', 'Processed Successfully');
        return redirect()->route($this->model . '.index');
    }
    
     public function stockMovementGlEntries($stock_move_id, $code){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = WaGlTran::where('stock_adjustment_id',$stock_move_id)->orderBy('id', 'desc')->get();
        //echo "<pre>"; print_r($data); die;
         $negativeAMount = WaGlTran::where('stock_adjustment_id',$stock_move_id)->where('amount', '<','0')->sum('amount');
            $positiveAMount = WaGlTran::where('stock_adjustment_id',$stock_move_id)->where('amount', '>','0')->sum('amount');


        $breadcum = [$title=>route($model.'.index'),'Stock Movement'=>route($model.'.stock-movements', $code),'GL Entries'=>''];
        return view('admin.tyre_inventories.gl_entries',compact('title','data','model','breadcum','code','pmodule','permission','negativeAMount','positiveAMount'));
    }


    public function Stockadjustments(){
    $data_query =DB::table('adjustments_summary')
            ->leftJoin('users', 'adjustments_summary.user_id', '=', 'users.id')->
            select('adjustments_summary.*','users.name as Unmae');
            $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        //echo "ssd"; die;
        // $data_query = TyreInventory::select('wa_inventory_categories.id as cat_id', 'wa_inventory_categories.category_description', 'tyre_inventories.*')->with('getUnitOfMeausureDetail', 'getAllFromStockMoves')
        //     ->join('wa_inventory_categories', 'wa_inventory_categories.id', '=', 'tyre_inventories.wa_inventory_category_id')->paginate(10);
        //echo "<pre>"; print_r($data_query); die;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.StockAdjustments.index',compact('title','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }


    public function StockAdjustmentsDatatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'code', 'item_title', 'comments', 'standard_cost',
            'item_adjustment_code','adjustment_quantity','current_qty_available'
        ];
        $totalData = TyreInventory::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =DB::table('adjustments_summary')
            ->leftJoin('users', 'adjustments_summary.user_id', '=', 'users.id')->
            select('adjustments_summary.*','users.name as Unmae');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('stock_id_code', 'LIKE', "%{$search}%")
                    ->orWhere('item_title', 'LIKE', "%{$search}%")
                    ->orWhere('standard_cost', 'LIKE', "%{$search}%")
                    ->orWhere('item_adjustment_code', 'LIKE', "%{$search}%");
                    
            });
            
        }
        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) {
         
                  $test = WaLocationAndStore::where('id',$row->wa_location_and_store_id)->first();
                  $udata = User ::where('id',$row->user_id)->first();
                  
                $user_link = '';
                $nestedData['date'] = date('d-m-Y', strtotime($row->created_at));
                $nestedData['item_adjustment_code'] = $row->item_adjustment_code;
                $nestedData['stock_id_code'] = $row->stock_id_code;
                $nestedData['uname'] = $row->Unmae;
                $nestedData['item_category'] = $test ? $test->location_name  : 'NA';
                $nestedData['item_title'] = $row->item_title;
                $nestedData['comments'] = $row->comments;
                $nestedData['standard_cost'] = $row->standard_cost;
                $nestedData['current_qty_available'] = $row->current_qty_available;
                $nestedData['adjustment_quantity'] = $row->adjustment_quantity;

                // $action_text =  ($row->slug != 'mpesa' && (isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')) ? buttonHtmlCustom('edit', route($model.'.edit', $row->slug)) : '';
                
                // $link_popup = route('admin.table.adjust-item-stock-form', $row->slug);
                $action_text = "<span class='span-action'> <a href='". route('tyre_inventories.StockadPrint',['id' => $row->id]) ."' ><i class='fa fa-file-pdf' aria-hidden='true' style='font-size: 16px;'></i></a></span>";
                // $action_text .=  buttonHtmlCustom('stock_status', route($model.'.stock-status', $row->code));

                // if(isset($permission[$pmodule.'___manage-item-stock']) || $permission == 'superadmin'){
                //    $action_text .= view('admin.tyre_inventories.popup_link',['link_popup'=> $link_popup,
                //         'id'=> $row->id]);
                // }
                // $action_text .=  (isset($permission[$pmodule.'___delete']) || $permission == 'superadmin') ? buttonHtmlCustom('delete', route($model.'.destroy', $row->slug)) : '';
                $nestedData['action'] = $action_text;
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

    public function StockadPrint(Request $request,$pData){
        $dataPdf = AdjustmentsSummary::where('id',$pData)->first();
        $userData = User::where('id',$dataPdf->user_id)->first();
        $pdf = PDF::loadView('admin.pdfviewStock', ['dataPdf' => $dataPdf,'userData' => $userData]);
      return $pdf->download('Stock Adjustments Reports.pdf');
  }

  public function downloadInvetoryitems(Request $request)
    {
        try {
            $data_query = TyreInventory::select('wa_inventory_categories.id as cat_id','wa_inventory_categories.category_description',
            'tyre_inventories.*')->with('getUnitOfMeausureDetail', 'getAllFromStockMoves')
            ->join('wa_inventory_categories','wa_inventory_categories.id','=','tyre_inventories.wa_inventory_category_id')->get();
            $arrays = [];
            $sumH = 0;
            if (!empty($data_query)) {
                foreach ($data_query as $key => $row) {
                    $arrays[] = ['Stock Id Code' => (string)($row->stock_id_code),
                                    'Title' => $row->title,
                                    'Item Category' => $row->category_description,
                                    'UOM' => (string)($row->getUnitOfMeausureDetail?$row->getUnitOfMeausureDetail->title:''),
                                    'Standard Cost' => (string)$row->standard_cost,
                                    'Quantity' => (string)($row->getAllFromStockMoves ? $row->getAllFromStockMoves->sum('qauntity') : 0),
                                    'Qty on order' => (string)getQtyOnOrder($row->id)          
                                ];
                    $sumH += ($row->getAllFromStockMoves ? $row->getAllFromStockMoves->sum('qauntity') : 0);
                }
            }
            $arrays[] = ['Stock Id Code' => '',
                            'Title' =>  '',
                            'Item Category' =>  '',
                            'UOM' =>  '',
                            'Standard Cost' =>  'Total',
                            'Quantity' => $sumH,
                            'Qty on Order' => '',          
                        ];
                       
            return \Excel::create('inventory-items-'.date('Y-m-d-H-i-s'), function($excel) use ($arrays) {
                $excel->sheet('mySheet', function($sheet) use ($arrays)
                {                    
                    $sheet->fromArray($arrays);
                });
            })->export('xls');            
        } catch (\Exception $th) {
            $request->session()->flash('danger','Something went wrong');
            return redirect()->back();
        }
    }


    //Maintain Purchase Data
    public function purchaseData($stockid,Request $request)
    {
        try {   
            $data['pmodule'] = $this->pmodule;
            $data['title'] = $this->title;
            $data['model'] = $this->model;
            $data['item_suppliers'] = \App\Model\TyreInventorySupplier::where('wa_inventory_item_id',$stockid)->orderBy('id','DESC')->get();
            $data['inventoryItem'] = \App\Model\TyreInventory::findOrFail($stockid);
            if(isset($_GET['supplier_name']) || isset($_GET['supplier_code'])){
                $sup = new \App\Model\WaSupplier();
                if($request->supplier_name)
                {
                    $sup = $sup->orWhere('name',$request->supplier_name);
                }
                if($request->supplier_code)
                {
                    $sup = $sup->orWhere('supplier_code',$request->supplier_code);
                }
                $data['suppliers'] = $sup->get();            
            }
            return view('admin.tyre_inventories.purchaseData.purchaseData')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->route('tyre_inventories.index');
        }
    }
    public function purchaseDataAdd($stockid,Request $request)
    {
        try {   
            $data['pmodule'] = $this->pmodule;
            $data['title'] = $this->title;
            $data['model'] = $this->model;
            $data['inventoryItem'] = \App\Model\TyreInventory::findOrFail($stockid);
            $data['supplier'] = \App\Model\WaSupplier::where('supplier_code',$request->supplier_code)->firstOrFail();
            $data['currencys'] = \App\Model\WaCurrencyManager::get();
            $data['units'] = \App\Model\WaUnitOfMeasure::get();
            return view('admin.tyre_inventories.purchaseData.purchaseDataAdd')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->route('tyre_inventories.index');
        }
    }

    public function purchaseDataEdit($stockid,$itemid,Request $request)
    {
        try {   
            $data['pmodule'] = $this->pmodule;
            $data['title'] = $this->title;
            $data['model'] = $this->model;
            $itemid = decrypt($itemid);
            $stockid = decrypt($stockid);
            $data['inventoryItem'] = \App\Model\TyreInventory::findOrFail($stockid);
            $data['supplier_item'] = \App\Model\TyreInventorySupplier::findOrFail($itemid);
            $data['supplier'] = \App\Model\WaSupplier::where('id',$data['supplier_item']->wa_supplier_id)->firstOrFail();
            // dd($data);
            $data['currencys'] = \App\Model\WaCurrencyManager::get();
            $data['units'] = \App\Model\WaUnitOfMeasure::get();
            return view('admin.tyre_inventories.purchaseData.purchaseDataEdit')->with($data);
        } catch (\Throwable $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->route('tyre_inventories.index');
        }
    }
    public function purchaseDataStore($stockid,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|exists:wa_suppliers,id',
            'stockid' => 'required|exists:tyre_inventories,id',
            'currency' => 'required|exists:wa_currency_managers,ISO4217',
            'price' => 'required|numeric|max:2550000000|min:1',
            'price_effective_from' => 'required|date_format:Y-m-d|after:today',
            'supplier_unit' => 'required|exists:wa_unit_of_measures,title',
            'conversion_factor' => 'nullable|string|min:1|max:255',
            'supplier_stock_code' => 'nullable|string|min:1|max:255',
            'min_order_qty' => 'required|numeric|digits_between:1,10|min:1',
            'supplier_stock' => 'required|string|max:255|min:1',
            'lead_time' => 'required|numeric|digits_between:1,10|min:1',
            'preferred_supplier' => 'required|in:No,Yes'
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        $suplier = DB::transaction(function () use ($request) {
            $suplier = new \App\Model\TyreInventorySupplier;
            $suplier->wa_supplier_id = $request->supplier;
            $suplier->wa_inventory_item_id = $request->stockid;
            $suplier->currency = $request->currency;
            $suplier->price = $request->price;
            $suplier->price_effective_from = $request->price_effective_from;
            $suplier->our_unit_of_measure = $request->our_unit_of_measure;
            $suplier->supplier_unit_of_measure = $request->supplier_unit;
            $suplier->conversion_factor = $request->conversion_factor;
            $suplier->supplier_stock_code = $request->supplier_stock_code;
            $suplier->minimum_order_quantity = $request->min_order_qty;
            $suplier->supplier_stock_description = $request->supplier_stock;
            $suplier->lead_time_days = $request->lead_time;
            $suplier->preferred_supplier = $request->preferred_supplier;
            $suplier->save();
            $price = new \App\Model\TyreInventorySupplierPrices;
            $price->wa_inventory_item_supplier_id = $suplier->id;
            $price->price = $suplier->price;
            $price->status = 'Current';
            $price->save();
            return  $suplier;
        });
        if($suplier)
        {   
            return response()->json([
                'result' => 1,
                'message' => 'Supplier Item added successfully',
                'location'=>route('tyre_inventories.purchaseData',['stockid'=>$request->stockid]),
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function purchaseDataUpdate($stockid,$itemid,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|exists:wa_currency_managers,ISO4217',
            'price' => 'required|numeric|min:1|max:2550000000',
            'price_effective_from' => 'required|date_format:Y-m-d|after:today',
            'supplier_unit' => 'required|exists:wa_unit_of_measures,title',
            'conversion_factor' => 'nullable|string|min:1|max:255',
            'supplier_stock_code' => 'nullable|string|min:1|max:255',
            'min_order_qty' => 'required|numeric|digits_between:1,10|min:1',
            'supplier_stock' => 'required|string|max:255|min:1',
            'lead_time' => 'required|numeric|digits_between:1,10|min:1',
            'preferred_supplier' => 'required|in:No,Yes'
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        $suplier = DB::transaction(function () use ($request,$itemid) {

            $itemid = decrypt($itemid);
            $suplier = \App\Model\TyreInventorySupplier::findOrFail($itemid);
            // $suplier->wa_supplier_id = $request->supplier;
            // $suplier->wa_inventory_item_id = $request->stockid;
            $suplier->currency = $request->currency;
            $suplier->price = $request->price;
            $suplier->price_effective_from = $request->price_effective_from;
            $suplier->our_unit_of_measure = $request->our_unit_of_measure;
            $suplier->supplier_unit_of_measure = $request->supplier_unit;
            $suplier->conversion_factor = $request->conversion_factor;
            $suplier->supplier_stock_code = $request->supplier_stock_code;
            $suplier->minimum_order_quantity = $request->min_order_qty;
            $suplier->supplier_stock_description = $request->supplier_stock;
            $suplier->lead_time_days = $request->lead_time;
            $suplier->preferred_supplier = $request->preferred_supplier;
            $suplier->save();
            \App\Model\TyreInventorySupplierPrices::where('wa_inventory_item_supplier_id',$suplier->id)->update(['status'=>'Old']);
            $price = new \App\Model\TyreInventorySupplierPrices;
            $price->wa_inventory_item_supplier_id = $suplier->id;
            $price->price = $suplier->price;
            $price->status = 'Current';
            $price->save();
            return  $suplier;
        });
        if($suplier)
        {   
            return response()->json([
                'result' => 1,
                'message' => 'Supplier Item updated successfully',
                'location'=>route('tyre_inventories.purchaseData',['stockid'=>$request->stockid]),
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function purchaseDataDelete($stockid,$itemid,Request $request)
    {
        $itemid = decrypt($itemid);
        $stockid = decrypt($stockid);
        $suplier = \App\Model\TyreInventorySupplier::findOrFail($itemid);
        $suplier->delete();
        Session::flash('success', 'Supplier Item deleted successfully.');
        return redirect()->route($this->model.'.purchaseData',['stockid'=>$stockid]); 
    }

}
    