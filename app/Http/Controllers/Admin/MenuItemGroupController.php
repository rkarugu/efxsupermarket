<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Http\Requests\Admin\MenuItemAddRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use DB;
use Session;

class MenuItemGroupController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'menu-item-groups';
        $this->title = 'Menu Item Group';
        $this->pmodule = 'menu-item-groups';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Category::whereLevel(2)->orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.menuitemgroups.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $getParentList = $this->getParentList('getSubmajorGroupsForMenuItems');
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.menuitemgroups.create',compact('title','model','breadcum','getParentList'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(MenuItemAddRequest $request)
    {
        try
        {
            $row = new Category();
            $row->name= $request->name;
            $row->available_from= $request->available_from;
            $row->available_to= $request->available_to;
            $row->description=$request->description;
            $row->restaurant_id=$request->restaurant_id;
            $row->wa_location_and_store_id=$request->wa_location_and_store_id;
            if($request->is_have_another_layout)
            {
                $row->is_have_another_layout = '1';
            }
           
          
            $row->level= 2;
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

                    $getParentList = $this->getParentList('getSubmajorGroupsForMenuItems');
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.menuitemgroups.edit',compact('title','model','breadcum','row','getParentList')); 
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


    public function update(MenuItemUpdateRequest $request, $slug)
    {
        try
        {
            $row =  Category::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            $row->available_from= $request->available_from;
            $row->available_to= $request->available_to;
            $row->description=$request->description;
            $row->restaurant_id=$request->restaurant_id;
            $row->wa_location_and_store_id=$request->wa_location_and_store_id;
            $row->is_have_another_layout = '0';
            if($request->is_have_another_layout)
            {
                $row->is_have_another_layout = '1';
            }
          
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
