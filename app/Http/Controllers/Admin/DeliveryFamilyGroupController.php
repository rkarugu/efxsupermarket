<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\BeerKegCategory;
use App\Model\BeerAndKegCategoryRelation;
use App\Http\Requests\Admin\MenuItemAddRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use DB;
use Session;

class DeliveryFamilyGroupController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'delivery-family-groups';
        $this->title = 'Delivery Family Group';
        $this->pmodule = 'delivery-family-groups';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = BeerKegCategory::whereLevel(2)->orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.deliveryfamilygroups.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $getParentList = $this->getDeliveryParentList('getSubMajorGroup');
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.deliveryfamilygroups.create',compact('title','model','breadcum','getParentList'));
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
            $row = new BeerKegCategory();
            $row->name= $request->name;
            $row->description=$request->description;
            if($request->parent_id == '5')
            {
                $row->is_have_another_layout = '1';
            }
           
          
            $row->level= 2;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'beerandkeg','100');
                $row->image= $image;
            }
            $row->save();
            BeerAndKegCategoryRelation::updateOrCreate(
                    ['beer_and_keg_category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    ); 
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
                $row =  BeerKegCategory::whereSlug($slug)->first();
                if($row)
                {

                   $getParentList = $this->getDeliveryParentList('getSubMajorGroup');
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.deliveryfamilygroups.edit',compact('title','model','breadcum','row','getParentList')); 
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
            $row =  BeerKegCategory::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
           
            $row->description=$request->description;
            $row->is_have_another_layout = '0';
            if($request->parent_id=='5')
            {
                $row->is_have_another_layout = '1';
            }
          
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
            BeerAndKegCategoryRelation::updateOrCreate(
                    ['beer_and_keg_category_id' => $row->id],  ['parent_id' => $request->parent_id]
                    ); 
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
                $row = BeerKegCategory::whereSlug($slug)->first();
                $is_valid = $this->canWeDeleteThisDeliveryType('FamilyGroup',$row->id);
                if($is_valid== true)
                {
                BeerKegCategory::whereSlug($slug)->delete();
                if($row->image)
                {
                    unlinkfile('beerandkeg',$row->image);
                    
                }
                Session::flash('success', 'Deleted successfully.');
                }
                else
                {
                Session::flash('warning', 'Please delete related data');
                }
            }
            else
            {
                Session::flash('warning','Invalid Request');
            }

                
            
            return redirect()->back();
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    
}
