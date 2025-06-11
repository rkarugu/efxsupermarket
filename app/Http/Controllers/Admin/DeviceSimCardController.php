<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceSimCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeviceSimCardController extends Controller
{
    protected $model;
    protected $title;
    
    public function __construct()
    {
        $this->model = 'device-sim-card';
        $this->title = 'Device Sim Card';
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

        $simCards = DeviceSimCard::get();

        return view('admin.device_sim_card.index', compact('title', 'model', 'breadcum', 'simCards'));
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

        return view('admin.device_sim_card.create', compact('title', 'model', 'breadcum'));

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
                'imei'=>'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request){
                DeviceSimCard::create([
                    'phone_number'=>$request->phone_number
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Sim Card Added Successfully.'
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

        $simCard = DeviceSimCard::find($id);

        return view('admin.device_sim_card.edit', compact('title', 'model', 'breadcum','simCard'));
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
                'imei'=>'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request,$id){
                DeviceSimCard::find($id)->update([
                    'phone_number'=>$request->phone_number
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Sim Card Updated Successfully.'
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
                $sim = DeviceSimCard::find($id);
                $sim->delete();
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Sim Card Deleted Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
