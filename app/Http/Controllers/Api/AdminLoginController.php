<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\User;
use App\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    public function __construct(protected SmsService $smsService)
    {
        
    }
    public function validateUser(Request $request)
    {

        $user_name = $request->Email_PhoneNo;
        $row = User::with('userRole')->where('email', $user_name)
            ->orWhere('phone_number', $user_name)
            ->first();

        if (!($row && Hash::check($request->password, $row->password))) {
            return [
                'permission'=> null,
                'row'=> $row,
            ];
        }
        if ($row->role_id == 1) {
            $permission = 'superadmin';
        } else {
            $permission = getPreviousPermissionsArray($row);
        }

        return [
            'permission'=> $permission,
            'row'=> $row,
        ];
    }
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Email_PhoneNo' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        }

        if ($this->validateUser($request)['permission'] != null)
        {
            $permission = $this->validateUser($request)['permission'];
            $row = $this->validateUser($request)['row'];
            if ($permission == 'superadmin' || (isset($permission['device-manager___reset']))){
                /*send OTP*/
                $otp = random_int(1000, 9999);
                $message = "<#> Your login verification code is $otp\n\n";


                OtpVerification::create([
                    'phone_number' => $row->phone_number,
                    'otp' => $otp
                ]);
                // sendOtp($message, substr($row->phone_number, 1));
                $this->smsService->sendMessage($message, $row->phone_number);
                return response()->json([
                    'status'=> true,
                    'message'=>'OTP sent to admin',
                ]);

            }
            return response()->json(['status' => false, 'message' => 'You dont not have Authority to reset devices'], 422);
        }

        return response()->json(['status' => false, 'message' => 'Invalid username or password'], 422);
    }

    public function resetDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email_PhoneNo' => 'required',
            'password' => 'required',
            'salesman_phone' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        }

        if ($this->validateUser($request)['permission'] != null)
        {
            $permission = $this->validateUser($request)['permission'];
            $row = $this->validateUser($request)['row'];
            if ($permission == 'superadmin' || (isset($permission['device-manager___reset']))){
                $uuid = Str::uuid();
                $salesman = User::where('phone_number', $request->salesman_phone)->first();

                $salesman->linkedDevice()->update(['device_id' => $uuid]);
                return response()->json([
                    'status'=> true,
                    'message'=> 'Device Id for '.$salesman->name.' reset successfully',
                    'device_id'=> $uuid,
                ]);
            }
            return response()->json(['status' => false, 'message' => 'User does not Have permission to reset devices'], 422);
        }

        return response()->json(['status' => false, 'message' => 'Invalid username or password'], 422);

    }
}
