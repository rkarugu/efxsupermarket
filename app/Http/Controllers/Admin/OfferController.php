<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Http\Requests\Admin\MenuItemAddRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Model\CategoryAndFoodItemTaxManager;
use DB;
use Session;

class OfferController extends Controller
{

    protected $model;
    protected $title;
      protected $pmodule;
    public function __construct()
    {
        $this->model = 'offers';
        $this->title = 'Offers';
        $this->pmodule = 'offers';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Category::whereLevel(9)->orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.offers.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $all_taxes = $this->getAllTaxFromTaxManagers();
            $getParentList = $this->getParentList('getSubmajorGroupsForOffers');
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.offers.create',compact('title','model','breadcum','getParentList','all_taxes')); 
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
            $row = new Category();
            $row->name= $request->name;
            $row->description=$request->description;
            $row->price = $request->price;
            $row->level= 9;
            $row->max_selection_limit= $request->max_selection_limit;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'menu_item_groups','100');
                $row->image= $image;
            }
            $row->save();
            CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    ); 


             if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    CategoryAndFoodItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'category_id' => $row->id]
                    );     
                } 

            }

            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.index'); 
        }
        catch(\Exception $e)
        {

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
                $row =  Category::whereSlug($slug)->first();
                if($row)
                {
                    $all_taxes = $this->getAllTaxFromTaxManagers();
                    $getParentList = $this->getParentList('getSubmajorGroupsForOffers');
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.offers.edit',compact('title','model','breadcum','row','getParentList','all_taxes')); 
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
            $row =  Category::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
           
            $row->description=$request->description;
             $row->price=$request->price;
             $row->max_selection_limit= $request->max_selection_limit;
           
          
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'menu_item_groups','100');
                if($previous_row->image)
                {
                    unlinkfile('menu_item_groups',$previous_row->image);
                }
                $row->image= $image;
            }
            $row->save();
            CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    ); 

            CategoryAndFoodItemTaxManager::where('category_id',$row->id)->delete();
            if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    CategoryAndFoodItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'category_id' => $row->id]
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
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___delete']) || $permission == 'superadmin')
            {

            

                $row = Category::whereSlug($slug)->first();
                $is_valid = $this->canWeDeleteThis('MENUITEMGROUP',$row->id);
                if($is_valid== true)
                {
                    Category::whereSlug($slug)->delete();
                    if($row->image)
                    {
                        unlinkfile('menu_item_groups',$row->image);
                        
                    }
                    Session::flash('success', 'Deleted successfully.');
                }
                else
                {
                    Session::flash('warning', 'Please delete related data');
                }
                
                return redirect()->back();
            }
            else
            {
                Session::flash('warning','Invalid Request');
                return redirect()->back();
            }
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    
}
