<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\BeerDeliveryItem;
use App\Model\BeerItemsAndCategoryRelation;
use App\Model\BeerItemTaxManager;


use DB;
use Session;
use Excel;

class DeliveryItemController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    
    public function __construct()
    {
        $this->model = 'delivery-items';
        $this->title = 'Delivery Item';
        $this->pmodule = 'delivery-items';
        
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = BeerDeliveryItem::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.deliveryitems.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }



    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            
            $familyGroups = getDeliveryFamilyAndSubfamilyGroup();
          
            $all_taxes = $this->getAllTaxFromTaxManagers();
            $pluNumberList = $this->getPluList();
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.deliveryitems.create',compact('title','model','breadcum','familyGroups','printclasses','getCondimentGroupsList','all_taxes','pluNumberList'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

   


    public function store(Request $request)
    {
        try
        {
            $row = new BeerDeliveryItem();
            $row->name= $request->name;
            $row->description= $request->description;
            $row->price= $request->price;
            
            
           $row->is_available_in_stock = '0';
            if($request->is_available_in_stock)
            {
            $row->is_available_in_stock = '1';
            }
           

            $row->plu_number = $request->plu_number?$request->plu_number:null;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'beerandkeg','100');
                $row->image= $image;
            }
            $row->save();

            BeerItemsAndCategoryRelation::updateOrCreate(
                    ['beer_delivery_item_id' => $row->id],  ['beer_keg_category_id' => $request->category_id]
                    ); 
           


             


            if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    BeerItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'beer_delivery_item_id' => $row->id]
                    );     
                } 

            }



             

            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.index'); 
        }
        catch(\Exception $e)
        {
            dd($e);
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
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {

                $row =  BeerDeliveryItem::whereSlug($slug)->first();
                if($row)
                {
                    
                    $all_taxes = $this->getAllTaxFromTaxManagers();
                    $familyGroups = getDeliveryFamilyAndSubfamilyGroup();
          
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    $pluNumberList = $this->getPluList();
                    return view('admin.deliveryitems.edit',compact('title','model','breadcum','row','familyGroups','printclasses','getCondimentGroupsList','all_taxes','pluNumberList')); 
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

   

    public function update(Request $request, $slug)
    {
        try
        {
            $row =  BeerDeliveryItem::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            $row->description= $request->description;
            $row->price= $request->price;

            $row->plu_number = $request->plu_number?$request->plu_number:$row->plu_number;
            $row->is_available_in_stock = '0';
            if($request->is_available_in_stock)
            {
                $row->is_available_in_stock = '1';
            }

            //$row->print_class_id= $request->print_class_id;
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'beerandkeg','100');
                if($previous_row->image)
                {
                    unlinkfile('beerandkeg',$previous_row->image);
                }
                $row->image= $image;
            }
            $row->save();

              BeerItemsAndCategoryRelation::updateOrCreate(
                    ['beer_delivery_item_id' => $row->id],  ['beer_keg_category_id' => $request->category_id]
                    ); 

            

             


             BeerItemTaxManager::where('beer_delivery_item_id',$row->id)->delete();
             if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    BeerItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'beer_delivery_item_id' => $row->id]
                    );    
                } 

            }



            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

   


    public function destroy($slug)
    {
        try
        {
            $row = BeerDeliveryItem::whereSlug($slug)->first();
            BeerDeliveryItem::whereSlug($slug)->delete();
            if($row->image)
            {
                unlinkfile('beerandkeg',$row->image); 
            }
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

  



    public function foodItemNotRelatedtoplu()
    {
         $lists = BeerDeliveryItem::select('name','id')->where('plu_number',null)->orderBy('id', 'DESC')->get()->toArray();
         sort($lists);

         $all_item[0]=['Sn','Name'];
         $i = 1;

         foreach($lists as $item)
         {
            $all_item[$i]=[$i,$item['name']];
            $i++;
         }
         $file_name = "deliveryitemwithoutplu";



         return Excel::create($file_name, function($excel) use ($all_item) {
            $excel->sheet('mySheet', function($sheet) use ($all_item)
            {
                $sheet->fromArray($all_item);
            });
        })->download('xls');

        
    }




    
}
