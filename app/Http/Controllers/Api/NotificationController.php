<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Model\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    //

    public function markNotificationSeen(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id'
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }
        $user = JWTAuth::toUser($request->token);


       $notification = Notification::find($request->notification_id)->update(['is_seen' => "1"]);


        return response()->json(['status' => true, 'message' => 'Marked as read']);


    }
}