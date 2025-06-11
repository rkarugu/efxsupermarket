<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DeviceTypeController extends Controller
{
    protected $model;
    protected $title;
    
    public function __construct()
    {
        $this->model = 'device-type';
        $this->title = 'Device Type';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', $this->model)) {
            return returnAccessDeniedPage();
        }
        
        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => ''];

        $types = DeviceType::get();

        return view('admin.device_type.index', compact('title', 'model', 'breadcum', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!can('add', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Add '.$this->title => ''];

        return view('admin.device_type.create', compact('title', 'model', 'breadcum'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return returnAccessDeniedPage();
        }

        try{
            $validator = Validator::make($request->all(),[
                'title'=>'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request){
                DeviceType::create([
                    'title'=>$request->title
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Type Added Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!can('edit', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'edit '.$this->title => ''];

        $type = DeviceType::find($id);

        return view('admin.device_type.edit', compact('title', 'model', 'breadcum','type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!can('edit', $this->model)) {
            return returnAccessDeniedPage();
        }

        try{
            $validator = Validator::make($request->all(),[
                'title'=>'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request,$id){
                DeviceType::find($id)->update([
                    'title'=>$request->title
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Type Updated Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        if (!can('delete', $this->model)) {
            return returnAccessDeniedPage();
        }

        try{
            $check = DB::transaction(function () use ($id){
                $type = DeviceType::find($id);
                $type->delete();
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Type Deleted Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
