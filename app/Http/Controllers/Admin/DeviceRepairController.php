<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\DeviceRepair;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class DeviceRepairController extends Controller
{
    protected $model;
    protected $title;
    
    public function __construct()
    {
        $this->model = 'device-repair';
        $this->title = 'Device Repair';
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

        $repairs = DeviceRepair::get();

        return view('admin.device_repair.index', compact('title', 'model', 'breadcum', 'repairs'));
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

        
        $devices = Device::whereDoesntHave('repair') // Devices not in repair
        ->orWhereHas('repair', function ($query) { // Devices with repairs that are completed
            $query->where('status', 'Completed');
        })->get()->map(function($device) {
            $holder = false;
        
            // if ($device->latestDeviceLog) {
            //     if ($device->latestDeviceLog->is_received) {
            //         $holder = true;
            //     } elseif ($device->secondLatestDeviceLog) {
            //         $holder = true;
            //     }
            // }
        
            // if ($holder == false) {
                return [
                    'id' => $device->id,
                    'device_no' => $device->device_no,
                ];
            // }
        
            return null;
        })->filter();

        $staffs = User::where('status','1')->get();

        return view('admin.device_repair.create', compact('title', 'model', 'breadcum','devices','staffs'));

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
                'device'=>'required',
                'repair_cost' => 'nullable',
                'charge_to' => 'required',
                'charged_user' => 'nullable',
                'comment' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request){
                DeviceRepair::create([
                    'created_by'=>Auth::user()->id,
                    'device_id' => $request->device,
                    'repair_cost' => $request->repair_cost,
                    'charge_to' => $request->charge_to,
                    'charged_user' => $request->charged_user,
                    'comment' => $request->comment
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Repair Added Successfully.'
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
        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Show '.$this->title => ''];

        $repair = DeviceRepair::find($id);

        return view('admin.device_repair.show', compact('title', 'model', 'breadcum','repair'));
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

        $repair = DeviceRepair::find($id);
        $devices = Device::whereDoesntHave('repair') // Devices not in repair
        ->orWhereHas('repair', function ($query) { // Devices with repairs that are completed
            $query->where('status', 'Completed');
        })
        ->orWhere('devices.id', $repair->device_id)
        ->get()->map(function($device) {
            $holder = false;
        
            if ($device->latestDeviceLog) {
                if ($device->latestDeviceLog->is_received) {
                    $holder = true;
                } elseif ($device->secondLatestDeviceLog) {
                    $holder = true;
                }
            }
        
            if ($holder == false) {
                return [
                    'id' => $device->id,
                    'device_no' => $device->device_no,
                ];
            }
        
            return null;
        })->filter();

        $staffs = User::where('status','1')->get();

        return view('admin.device_repair.edit', compact('title', 'model', 'breadcum','repair','devices','staffs'));
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
                'device'=>'required',
                'repair_cost' => 'nullable',
                'charge_to' => 'required',
                'charged_user' => 'nullable',
                'comment' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request,$id){
                DeviceRepair::find($id)->update([
                    'device_id' => $request->device,
                    'repair_cost' => $request->repair_cost,
                    'charge_to' => $request->charge_to,
                    'charged_user' => $request->charged_user,
                    'comment' => $request->comment
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Repair Updated Successfully.'
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
                $repair = DeviceRepair::find($id);
                $repair->delete();
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Repair Deleted Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    
    public function getDeviceRepair($id): JsonResponse
    {
        try {
            $repairs = DeviceRepair::with('device','chargeTo')
            ->where('device_id',$id)
            ->orderBy('created_at','DESC')
            ->get()->map(function($device){
                $device->repair_date = date('Y-m-d', strtotime($device->created_at));
                $device->repairedDate = $device->complete_date ? date('Y-m-d', strtotime($device->complete_date)): null;
                $device->charged_user = $device->charge_to=='Staff' ? $device->chargeTo?->name :$device->charge_to;
                $device->repairCost = manageAmountFormat($device->repair_cost);
                return $device;
            });

            return $this->jsonify(['data' => $repairs], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function repairComplete(Request $request)
    {
        $request->validate([
            'comment' => 'nullable|string'
        ]);

        try {
            $repair = DeviceRepair::find($request->repair_id)->update([
                'complete_date' => now(),
                'completed_by' => Auth::user()->id,
                'completed_comment' => $request->comment,
                'status' => 'Completed'
            ]);
           
            return response()->json([
                'message' => 'Device Repair successfully',
                'data' => $repair
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
