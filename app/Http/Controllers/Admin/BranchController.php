<?php

namespace App\Http\Controllers\Admin;
use DB;
use Session;
use Exception;
use App\Model\Branch;
use App\Model\WaBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'branches';
        $this->title = 'Branches';
        $this->pmodule = 'branches';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaBranch::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.branch.index',compact('title','lists','model','breadcum','pmodule','permission'));
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
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.branch.create',compact('title','model','breadcum'));
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
             $validator = Validator::make($request->all(), [
                'branch_name' => 'required|max:255',
                'branch_code' => 'required|unique:wa_branches',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new WaBranch();
                $row->branch_name= $request->branch_name;
                $row->branch_code= $request->branch_code;
                $row->save();
                Session::flash('success', 'Record added successfully.');
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
                $row =  WaBranch::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.branch.edit',compact('title','model','breadcum','row')); 
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
            $row =  WaBranch::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'branch_name' => 'required|max:255',
                'branch_code' => 'required|unique:wa_branches,branch_code,' . $row->id,
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row->branch_name= $request->branch_name;
                $row->branch_code= $request->branch_code;
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


    public function destroy($slug)
    {
        try
        {
            WaBranch::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    // API
    public function bankBranchList()
    {
        return response()->json(Branch::with('bank')->orderBy('branch')->get());
    }

    public function bankBranchCreate(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'required|exists:wa_bank,id',
            'branch' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $bankBranch = Branch::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch added successfully',
            'data' => $bankBranch
        ], 201);
    }

    public function bankBranchEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_branch,id',
            'bank_id' => 'required|exists:wa_bank,id',
            'branch' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $bankBranch = Branch::find($request->id);

            array_shift($data);
            $bankBranch->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch updated successfully',
            'data' => $bankBranch
        ]);
    }

    public function bankBranchDelete($id)
    {
        try {
            $bankBranch = Branch::find($id);

            $bankBranch->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Branch deleted successfully',
        ]);
    }
}
