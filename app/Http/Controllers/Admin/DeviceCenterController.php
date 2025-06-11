<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\DeviceType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Interfaces\SmsService;
use App\Model\Restaurant;
use App\Model\WaLocationAndStore;
use App\Models\DeviceSimCard;
use App\Models\SimCardToDevice;
use App\Services\ExcelDownloadService;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class DeviceCenterController extends Controller
{
    protected $model;
    protected $title;
    
    public function __construct(protected SmsService $smsService)
    {
        $this->model = 'device-center';
        $this->title = 'Device Center';
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

        $devices = Device::query();
        if (Auth::user()->role_id != 1 && !Auth::user()->is_hq_user) {
            $devices->where('branch_id', Auth::user()->restaurant_id);
        }
        
        if (request()->filled('branch')) {
            $devices->where('branch_id', request()->branch);
        }
        $devices = $devices->get()->map(function($device){
            $holder= '';
            if ($device->latestDeviceLog) {
                if ($device->latestDeviceLog->is_received) {
                    $info = $device->latestDeviceLog->issuedTo;
                    $holder = $info->name.'('.$info->userRole->title.')';
                } else {
                    if ($device->secondLatestDeviceLog) {
                        $info = $device->secondLatestDeviceLog->issuedTo;
                        $holder = $info->name.'('.$info->userRole->title.')';
                    }
                }
            }
            return [
                'id' => $device->id,
                'device_no' => $device->device_no,
                'serial_no' => $device->serial_no,
                'deviceType' => $device->deviceType->title,
                'current_holder' => $holder,
                'simcard' => $device->simCard?->phone_number,
                'branch' => $device->branch ? $device->branch->name : 'HQ'
            ];
        });

        if (request()->filled('print')) {
            if (request()->print == 'pdf') {
                $pdf = PDF::loadView('admin.device_center.device_pdf', compact('devices'));
                $report_name = 'Devices ' . date('Y_m_d_H_i_A');
                return $pdf->download('devices.pdf');
            }
        }
        

        return view('admin.device_center.index', compact('title', 'model', 'breadcum', 'devices'));
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
        $breadcum = ['Device Manager' => '', $this->title => '', 'Add Device' => ''];

        $types = DeviceType::get();

        return view('admin.device_center.create', compact('title', 'model', 'breadcum','types'));
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
                'device_type'=>'required',
                'model'=>'required',
                'serial_no'=>'required',
                'device_no'=>'required',
                'branch' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }
            $check = DB::transaction(function () use ($request){
                Device::create([
                    'device_type_id'=>$request->device_type,
                    'model'=>$request->model,
                    'serial_no'=>$request->serial_no,
                    'device_no'=>$request->device_no,
                    'branch_id'=>$request->branch
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Added Successfully.'
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
        if (!can('show', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => ''];

        $device = Device::with('deviceType','latestDeviceLog','latestDeviceLog.issuedTo','simCard','latestRepair','branch')->where('device_no',$id)->first();
        $user = Auth::user();

        return view('admin.device_center.show', compact('title', 'model', 'breadcum', 'device','user'));
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
        $breadcum = ['Device Manager' => '', $this->title => '', 'Edit Device' => ''];

        $types = DeviceType::get();
        $device = Device::find($id);

        return view('admin.device_center.edit', compact('title', 'model', 'breadcum','device','types'));
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
                'device_type'=>'required',
                'model'=>'required',
                'serial_no'=>'required',
                'device_no'=>'required',
                'branch' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ], 422);
            }

            $check = DB::transaction(function () use ($request,$id){
                Device::find($id)->update([
                    'device_type_id'=>$request->device_type,
                    'model'=>$request->model,
                    'serial_no'=>$request->serial_no,
                    'device_no'=>$request->device_no,
                    'branch_id'=>$request->branch
                ]);
                return true;
            });
            
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Device Updated Successfully.'
                ], 200);         
            }
            
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getDeviceHistory($id): JsonResponse
    {
        try {
            $deviceHistory = DeviceLog::with('issuedBy','issuedTo')
            ->where('device_id',$id)
            ->orderBy('created_at','DESC')
            ->get()->map(function($device){
                $device->date_issued = date('Y-m-d H:i', strtotime($device->date_issued));
                return $device;
            });

            return $this->jsonify(['data' => $deviceHistory], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getDeviceUsers(Request $request,$id): JsonResponse
    {
        try {
            $role = Auth::user()->role_id;
            $users = User::query();

            //Route Managers
            if ($role==5) {
                $users->where('role_id',4);
            }

            // Sales Man
            if ($role==4) {
                $users->where('role_id',5);
            }
            
            $users = $users->get()->map(function($user){
                return [
                    'id'=>$user->id,
                    'name' => $user->name .'('.$user->userRole?->title.')',
                    'role' => $user->userRole?->title
                ];
            })->toArray();

            return $this->jsonify(['data' => $users], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function allocateDevice(Request $request)
    {
        $request->validate([
            'issued_to' => 'required',
            'type' => 'required|string',
            'comment' => 'nullable|string'
        ]);

        $issuedTo = User::find($request->issued_to);

        $otp = random_int(100000, 999999);
        
        try {
            $device = DeviceLog::create([
                'device_id' => $request->device_id,
                'issued_to' => $request->issued_to,
                'issued_by' => Auth::user()->id,
                'branch_id' => $issuedTo->restaurant_id,
                'date_issued' => Carbon::now(),
                'issue_type' => $request->type,
                'issued_by_comment' => $request->comment,
                'verify_otp' => $otp
            ]);

            $message = "<#> Your Allocation verification code is $otp\n\n";
            $this->smsService->sendMessage($message, $issuedTo->phone_number);

            return response()->json([
                'message' => 'Device Allocated successfully',
                'data' => $device
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyDeviceAllocate(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);
        $log = DeviceLog::find($request->logId);
        if($log->is_received){
            return response()->json([
                'message' => 'Device already'
            ], 500);
        }

        if ($log->verify_otp != $request->otp) {
            return response()->json([
                'message' => 'Please Provide the correct OTP. Received Failed.'
            ], 500);
        }
        DB::beginTransaction();
        try {
            $comment = $request->comment;
            if($log->issued_by_comment != null){
                $comment =$log->issued_by_comment; 
            }
            $log->update([
                'is_received' => true,
                'status' => 'Received',
                'issued_by_comment' => $comment,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Device Allocated successfully'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
        
        
        
       
    }

    public function initiateDeviceReturn($id)
    {
        $log = DeviceLog::where('device_id',$id)->first();
        if ($log && $log->device->latestDeviceLog->is_received ==0) {
            return response()->json([
                'message' => 'Device Not Allocated',
            ], 500);
        }
            
        DB::beginTransaction();
        $user = Auth::user();

        $otp = random_int(100000, 999999);
        
        try {
            $device = DeviceLog::create([
                'device_id' => $log->device_id,
                'issued_to' => $user->id,
                'issued_by' => $user->id,
                'branch_id' => $user->restaurant_id,
                'date_issued' => Carbon::now(),
                'issue_type' => 'Permanent',
                'verify_otp' => $otp
            ]);

            if($log->device->latestDeviceLog->issuedTo?->phone_number){
                $message = "<#> Your Return verification code is $otp\n\n";
                $this->smsService->sendMessage($message, $log->device->latestDeviceLog->issuedTo->phone_number);
            }
            
            DB::commit();
            return response()->json([
                'message' => 'Device Allocated successfully',
                'data' => $device
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulk_upload(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Bulk Device Upload' => ''];
        $processingUpload = false;

        return view('admin.device_center.device_upload', compact('title', 'model', 'breadcum','processingUpload'));
    }

    public function bulk_upload_process(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        }

        if($request->intent  && $request->intent == 'Template'){
            $payload = [];
            $data[] = $payload;
            return ExcelDownloadService::download('device_upload_template', collect($data), ['DEVICE TYPE', 'MODEL', 'SERIAL NO.','DEVICE NO.', 'SIM CARD NUMBER', 'BRANCH ID']);
        }

        $request->validate([
            'device_upload' => 'required|mimes:xlsx,xls,csv',
        ]);

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Bulk Device Upload' => ''];
        $processingUpload = false;

        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);

            $fileName = $request->file('device_upload');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $devices = [];
            $deviceRejected = [];

            foreach ($data as $index => $record) {
                if ($index == 0) continue;

                $deviceTypeQuery = DeviceType::where('title',$record[0])->first();
                
                
                $deviceType = $deviceTypeQuery ? $deviceTypeQuery->title : '';
                $deviceModel = $record[1];
                $serial = $record[2];
                $deviceNo = $record[3];

                // if ($record[4]) {
                //     $simQuery = DeviceSimCard::where('phone_number', $record[4])->first();
                //     $simCard = $simQuery ? $simQuery->phone_number : '';
                // } else {
                    $simCard = $record[4];
                // }
                

                $fetchDevice = Device::where('serial_no',$serial)
                ->orWhere('device_no',$deviceNo)
                ->first();
                $branchId = (int)$record[5];
                $branch = WaLocationAndStore::find($branchId);
                if ($fetchDevice || !$deviceType) {
                    $deviceRejected[] = [
                        'deviceType' => $deviceType,
                        'model' => $deviceModel,
                        'serial' => $serial,
                        'deviceNo' => $deviceNo,
                        'simCard' => $simCard,
                        'branch' => $branch?->location_name
                    ];
                } else{
                    $devices[] = [
                        'deviceType' => $deviceType,
                        'model' => $deviceModel,
                        'serial' => $serial,
                        'deviceNo' => $deviceNo,
                        'simCard' => $simCard,
                        'branch' => $branch?->location_name,
                        'branchId' => $branch?->wa_branch_id
                    ];
                }
                    
            }

            $processingUpload = true;
            
            return view('admin.device_center.device_upload', compact('title', 'model', 'breadcum','processingUpload','devices','deviceRejected'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }

        return view('admin.device_center.device_upload', compact('title', 'model', 'breadcum','processingUpload'));

    }

    public function bulk_upload_store(Request $request)
    {
        
        try {
            $check = DB::transaction(function () use ($request) {
                $devices = json_decode($request->devices, true);
                
                foreach ($devices as $device) {
                    $deviceType = DeviceType::where('title',$device['deviceType'])->first();
                    if ($deviceType) {
                        $deviceSimcard = $device['simCard'];
                        $simQuery = DeviceSimCard::where('phone_number', $deviceSimcard)->first();
                        
                        $device = Device::create([
                            'device_type_id'=>$deviceType->id,
                            'model'=>$device['model'],
                            'serial_no'=>$device['serial'],
                            'device_no'=>$device['deviceNo'],
                            'branch_id' => $device['branchId']
                        ]);

                        if($simQuery)
                        {
                            if ($simQuery->device) {
                                Device::find($simQuery->device->id)->update(['simcard_id'=>NULL]);
                            }
                            $device = Device::find($device->id)->update(['simcard_id'=>$simQuery->id]);
                            SimCardToDevice::create([
                                'device_id' => $device->id,
                                'simcard_id' => $simQuery->id,
                                'created_by' => Auth::user()->id
                            ]);
                        }  else {
                            if ($deviceSimcard) {
                                $simQuery = DeviceSimCard::create([
                                    'phone_number'=>$deviceSimcard
                                ]);
                                Device::find($device->id)->update(['simcard_id'=>$simQuery->id]);
                                SimCardToDevice::create([
                                    'device_id' => $device->id,
                                    'simcard_id' => $simQuery->id,
                                    'created_by' => Auth::user()->id
                                ]);
                            }
                            
                        }                      
                    }
                    
                }
                return true;
            });

            if ($check) {
                return response()->json([
                    'result' => 1,
                    'message' => 'Devices Uploaded Successfully',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDeviceSims(): JsonResponse
    {
        try {
            $sims = DeviceSimCard::doesntHave('device')->orderBy('created_at','DESC')
            ->get();

            return $this->jsonify(['data' => $sims], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getDeviceSim($id): JsonResponse
    {
        try {
            $sims = SimCardToDevice::where('device_id',$id)
            ->orderBy('created_at','DESC')
            ->get()->map(function($sim){
                return [
                    'id' => $sim->id,
                    'sim' => $sim->simCard->phone_number,
                    'createdBy' => $sim->createdBy->name,
                    'createdOn' => date('d M,y H:i', strtotime($sim->created_at))
                ];
            });

            return $this->jsonify(['data' => $sims], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function allocateDeviceSim(Request $request)
    {
        $request->validate([
            'sim_card' => 'required'
        ]);

        
        DB::beginTransaction();
        try {
            $device = Device::find($request->device_id);
            $device->update(['simcard_id'=>$request->sim_card]);
            
            SimCardToDevice::create([
                'device_id' => $request->device_id,
                'simcard_id' => $request->sim_card,
                'created_by' => Auth::user()->id
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Device Allocated Sim successfully',
                'data' => $device
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function removeDeviceSim(Request $request)
    {
        $request->validate([
            'sim_card' => 'required'
        ]);

        
        DB::beginTransaction();
        try {
            $device = Device::find($request->device_id);
            $device->update(['simcard_id'=>NULL]);
            
            SimCardToDevice::create([
                'simcard_id' => $request->sim_card,
                'created_by' => Auth::user()->id
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Device Removed Sim successfully',
                'data' => $device
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulk_allocate(Request $request)
    {
        if (!can('bulk-allocate', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Bulk Device Allocate' => ''];
        $processingUpload = false;

        return view('admin.device_center.device_bulk_allocate', compact('title', 'model', 'breadcum','processingUpload'));
    }

    public function bulk_allocate_process(Request $request)
    {
        if (!can('bulk-allocate', $this->model)) {
            return returnAccessDeniedPage();
        }

        if($request->intent  && $request->intent == 'Template'){
            $payload = [];
            $data[] = $payload;
            return ExcelDownloadService::download('device_allocate_template', collect($data), ['DEVICE NO', 'USER ID']);
        }

        $request->validate([
            'device_allocate' => 'required|mimes:xlsx,xls,csv',
        ]);

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Device Manager' => '', $this->title => '', 'Bulk Allocate' => ''];
        $processingUpload = false;

        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);

            $fileName = $request->file('device_allocate');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $devices = [];
            $deviceRejected = [];

            foreach ($data as $index => $record) {
                if ($index == 0) continue;

                $deviceQuery = Device::where('device_no',$record[0])->first();
                $userQuery = User::where('id',$record[1])->first();
                $holder= '';
                if ($deviceQuery || $userQuery) {
                    if ($deviceQuery->latestDeviceLog) {
                        if ($deviceQuery->latestDeviceLog->is_received) {
                            $info = $deviceQuery->latestDeviceLog->issuedTo;
                            $holder = $info->name.'('.$info->userRole->title.')';
                        } else {
                            if ($deviceQuery->secondLatestDeviceLog) {
                                $info = $deviceQuery->secondLatestDeviceLog->issuedTo;
                                $holder = $info->name.'('.$info->userRole->title.')';
                            }
                        }
                    }

                    if ($holder) {
                        $deviceRejected[] = [
                            'device' => $deviceQuery->device_no,
                            'holder' => $holder,
                            'user' => $userQuery?->name
                        ];
                    } else {
                        $devices[] = [
                            'device' => $deviceQuery->device_no,
                            'holder' => $holder,
                            'user' => $userQuery?->name,
                            'userId' => $userQuery?->id,
                        ];
                    }

                } else{
                    $deviceRejected[] = [
                        'device' => $record[0],
                        'holder' => $holder,
                        'user' => $userQuery?->name
                    ];
                }        
                    
            }

            $processingUpload = true;
            
            return view('admin.device_center.device_bulk_allocate', compact('title', 'model', 'breadcum','processingUpload','devices','deviceRejected'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }

        return view('admin.device_center.device_bulk_allocate', compact('title', 'model', 'breadcum','processingUpload'));

    }

    public function bulk_allocate_store(Request $request)
    {
        
        try {
            $check = DB::transaction(function () use ($request) {
                $devices = json_decode($request->devices, true);
                
                foreach ($devices as $device) {
                    $issuedTo = User::find($device['userId']);
                    $otp = random_int(100000, 999999);
                    $device = Device::where('device_no',$device['device'])->first();
                    
                    
                    if ($issuedTo->role_id == 4) {
                        $device = DeviceLog::create([
                            'device_id' => $device->id,
                            'issued_to' => $issuedTo->id,
                            'issued_by' => Auth::user()->id,
                            'branch_id' => $issuedTo->restaurant_id,
                            'date_issued' => Carbon::now(),
                            'issue_type' => 'Permanent',
                            'verify_otp' => $otp
                        ]);

                        $message = "<#> Your Allocation verification code is $otp\n\n";
                        $this->smsService->sendMessage($message, $issuedTo->phone_number);
                    } else {
                        $device = DeviceLog::create([
                            'device_id' => $device->id,
                            'issued_to' => $issuedTo->id,
                            'issued_by' => Auth::user()->id,
                            'branch_id' => $issuedTo->restaurant_id,
                            'date_issued' => Carbon::now(),
                            'issue_type' => 'Permanent',
                            'is_received' => true,
                            'status' => 'Received',
                        ]);
                    }
                    
                    $deviceType = DeviceType::where('title',$device['deviceType'])->first();
                    if ($deviceType) {
                        $simQuery = DeviceSimCard::where('phone_number', $device['simCard'])->first();
                        
                        $device = Device::create([
                            'device_type_id'=>$deviceType->id,
                            'model'=>$device['model'],
                            'serial_no'=>$device['serial'],
                            'device_no'=>$device['deviceNo']
                        ]);

                        if($simQuery)
                        {
                            if ($simQuery->device) {
                                Device::find($simQuery->device->id)->update(['simcard_id'=>NULL]);
                            }
                            $device = Device::find($device->id)->update(['simcard_id'=>$simQuery->id]);
                            SimCardToDevice::create([
                                'device_id' => $device->id,
                                'simcard_id' => $simQuery->id,
                                'created_by' => Auth::user()->id
                            ]);
                        }                       
                    }
                    
                }
                return true;
            });

            if ($check) {
                return response()->json([
                    'result' => 1,
                    'message' => 'Devices Allocated Successfully',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDeviceHolder($id)
    {
        $device = Device::with(['latestDeviceLog.issuedTo.userRole', 'secondLatestDeviceLog.issuedTo.userRole', 'deviceType', 'simCard', 'branch'])
    ->where('id', $id) // Replace $id with the actual device ID
    ->first();

if ($device) {
    $holder = '';
    if ($device->latestDeviceLog) {
        if ($device->latestDeviceLog->is_received) {
            $info = $device->latestDeviceLog->issuedTo;
            $holder = $info->name . ' (' . $info->userRole->title . ')';
        } else if ($device->secondLatestDeviceLog) {
            $info = $device->secondLatestDeviceLog->issuedTo;
            $holder = $info->name . ' (' . $info->userRole->title . ')';
        }
    }

    $result = [
        'id' => $device->id,
        'current_holder' => $holder,
        'branch' => $device->branch ? $device->branch->name : 'HQ',
    ];

    return $result;
}

return null;

    }
}
