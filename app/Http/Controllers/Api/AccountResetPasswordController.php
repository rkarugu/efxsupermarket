<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountResetPasswordController extends Controller
{
    public function resetAccountPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits:10',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        } else {


            $getUser = User::where('phone_number', $request->phone_number)->first();

            if (!$getUser) {
                return response()->json(['status' => false, 'message' => 'User details not found. Check your phone number and try again.'], 422);
            }


            try {
                $code = rand(1111, 9999);
                $message = "Your login verification code is " . $code;
                sendMessage($message, $request->phone_number);
            } catch (\Throwable $e) {
//            pass
            }
            $getUser->verification_code = $code;
            $getUser->save();

            return response()->json(['status' => true, 'message' => 'reset code sent successfully.', 'requires_code' => true, 'user' => $getUser], 200);
        }
    }

    public function setNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_code' => 'required|digits:4',
            'phone_number' => 'required|digits:10',
            'password' => 'required|string|min:6|max:20|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error], 422);
        } else {
            $getUser = User::where('phone_number', $request->phone_number)->first();

            if (!$getUser) {
                return response()->json(['status' => false, 'message' => 'User details not found. Check your phone number and try again.'], 422);
            }

            if ($getUser->verification_code != $request->reset_code) {
                return response()->json(['status' => false, 'message' => 'Please provide the right code sent to you or request for a new verificaton code.'], 422);
            }

            $getUser->verification_code = null;
            $getUser->password = bcrypt($request->password);
            $getUser->save();

            return response()->json(['status' => true, 'message' => 'password reset successfully.'], 200);
        }
    }
}
