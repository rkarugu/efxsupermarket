<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReturnReasonController extends Controller
{
     // protected $model;
     protected $base_route;
     protected $resource_folder;
     protected $title;
     protected $pmodule;
     protected $module;
     protected $model;
 
     public function __construct()
     {
         // $this->model = "order-taking-schedules";
         $this->base_route = "return-reasons";
         $this->resource_folder = "admin.return_reasons";
         $this->title = "Return Reasons";
         $this->pmodule = "return-reasons";
         $this->module = "return-reasons";
         $this->model = "return-reasons";
     }
 
   
    public function index()
    {
        $model = $this->model;
        $title = $this->title;
        $reasons = ReturnReason::all();
        return view('admin.return_reasons.index', compact('reasons', 'model', 'title'));
        
    }

    
    public function create()
    {
        $model = $this->model;
        $title = $this->title;
        return view('admin.return_reasons.create', compact('model', 'title'));

    }

    
    public function store(Request $request)
    {
        $returnReason = new ReturnReason();
        $returnReason->reason = $request->reason;
        $returnReason->created_by = Auth::user()->id;
        $returnReason->save();
        Session::flash('success','Return Reason  Added Successfully');
        return redirect()->route('return-reasons.index');
    }

  
    public function show(string $id)
    {
        //
    }

    
    public function edit($id)
    {
        $model = $this->model;
        $title = $this->title;
        $id = base64_decode($id);
        $returnReason = ReturnReason::find($id);
        return view('admin.return_reasons.edit', compact('model', 'title', 'returnReason'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $reason = ReturnReason::find($id);
            $reason->reason = $request->reason;
            $reason->use_for_pos = $request->use_for_pos;
            $reason->save();
            Session::flash('success', 'Reason Updated Successfully');
            return redirect()->route('return-reasons.index');

        } catch (\Throwable $th) {
            Session::flash('warning',$th->getMessage());
            return redirect()->route('return-reasons.index');
        }
    }

   
    public function destroy(string $id)
    {
        $reason  = ReturnReason::find($id);
        $reason->delete();
        Session::flash('success','Return Reason  Deleted Successfully');
        return redirect()->route('return-reasons.index');
    }
}
