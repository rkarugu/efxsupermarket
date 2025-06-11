<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Model\MenuItemWithFamilyGroup;
use App\Http\Requests\Admin\FamilyGroupAddRequest;
use App\Http\Requests\Admin\FamilyGroupUpdateRequest;
use DB;
use Session;

class FamilyGroupForAnotherLayoutController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'alcoholic-family-groups';
        $this->title = 'Alcoholic Family Group';
        $this->pmodule = 'alcoholic-family-groups';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Category::whereLevel(3)->where('is_have_another_layout','1')->orderBy('id', 'DESC')->get();
            $breadcum = [$this->title =>route($this->model.'.index'),'Listing'=>''];
            return view('admin.alcoholicfamilygroups.index',compact('title','lists','model','breadcum','pmodule','permission')); 
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
            $getParentList = $this->getParentList('forAlcoholicFamilyGroup');
            $title = 'Add '.$this->title;;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.alcoholicfamilygroups.create',compact('title','model','breadcum','getParentList'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }


      
    }


    public function store(FamilyGroupAddRequest $request)
    {
        try
        {
            $row = new Category();
            $row->name= $request->name; 
            $row->level= 3; 
            $row->is_have_another_layout = '1';
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'family_groups','100');
                $row->image= $image;
            }
            $row->save();
            $family_group_id = $row->id;
            foreach($request->parent_ids as $parent_id)
            {
                
                CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id,'parent_id' => $parent_id]
                    );     
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
                   $getParentList = $this->getParentList('forAlcoholicFamilyGroup');
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    return view('admin.alcoholicfamilygroups.edit',compact('title','model','breadcum','row','getParentList')); 
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


    public function update(FamilyGroupUpdateRequest $request, $slug)
    {
        try
        {
            $row =  Category::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'family_groups','100');
               
                if($previous_row->image)
                {
                    unlinkfile('family_groups',$previous_row->image);
                }
                $row->image= $image; 
            }
            $row->save();
           
            CategoryRelation::where('category_id',$row->id)->delete();
            foreach($request->parent_ids as $parent_id)
            {
                
                CategoryRelation::updateOrCreate(
                    ['category_id' => $row->id,'parent_id' => $parent_id]
                    );     
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
                $is_valid = $this->canWeDeleteThis('FAMILYGROUP',$row->id);
                if($is_valid== true)
                {
                    Category::whereSlug($slug)->delete();
                    if($row->image)
                    {
                        unlinkfile('family_groups',$row->image);  
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
