<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\MajorGroupManager;
use App\Model\Category;
use App\Http\Requests\Admin\MajorGroupUpdateRequest;
use DB;
use Session;

class MajorGroupManagerController extends Controller
{


    protected $model;
    protected $title;
     protected $pmodule;

    public function __construct()
    {
        $this->model = 'major-group-managers';
        $this->title = 'Major Group Manager';
        $this->pmodule = 'major-group-managers';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$this->pmodule.'___view']) || $permission == 'superadmin')
        {
            $title = $this->title;
            $model = $this->model;
            $lists = Category::whereLevel(0)->orderBy('display_order', 'asc')->get();
            $breadcum = [$this->title=>route($this->model.'.index'),'Listing'=>''];
            return view('admin.majorgroupmanager.index',compact('title','lists','model','breadcum','permission','pmodule'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        
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
                    $title = 'Edit '.$this->title;
                    $model = $this->model;
                    $breadcum = [$this->title =>route($model.'.index'),'Edit'=>''];
                    return view('admin.majorgroupmanager.edit',compact('title','model','breadcum','row'));
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


    public function update(MajorGroupUpdateRequest $request, $slug)
    {
        try
        {
            $row =  Category::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            $row->display_order=$request->display_order;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'major_groups','100');
               
                if($previous_row->image)
                {
                    unlinkfile('major_groups',$previous_row->image);
                }
                $row->image= $image;
               
            }
            $row->save();
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
       
    }

   

    
}
