<?php

namespace App\Http\Controllers\Api;

use App\Alert;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\Role;
use App\Model\User;
use App\Services\AlertService;
use App\UserAccessRequest;
use App\UserLinkedDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;

class ManageDeniedUsersAccessController extends Controller
{
    public function __construct(protected SmsService $smsService)
    {
    }


    public function storeUserAccessRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_access' => 'required',
                'username' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error], 422);
            } else {
                $user_name = $request->username;
                $user = User::where('email', $user_name)
                    ->orWhere('phone_number', $user_name)
                    ->first();

                if ($user == null) {
                    return response()->json(['status' => false, 'message' => 'Account not found.'], 422);
                } else {
                    $check_existing_request = UserAccessRequest::where('user_id', $user->id)->where('status', 'pending')->get();
                    if ($check_existing_request->count() >= 1) {
                        return response()->json(['status' => false, 'message' => 'You have an existing pending request waiting for approval.'], 422);
                    } else {
                        UserAccessRequest::create([
                            'user_id' => $user->id,
                            'reason' => $request->request_access,
                        ]);
                        $userDevice = UserLinkedDevice::where('user_id', $user->id)->first();
                        if ($userDevice) {
                            $userDevice->has_pending_request = true;
                            $userDevice->save();
                        }

                        $message = "$user->name is requesting for access into their device, citing: '$request->request_access'";
                        $this->smsService->sendMessage($message, '0728600363');
                        $this->smsService->sendMessage($message, '0740489494');


//                        $alert = match ($user->role_id) {
//                            4 => 'salesman_app_access_requests',
//                            6 => 'driver_app_access_requests',
//                            default => 'general_app_access_requests',
//                        };
//
//                        foreach ($user->routes as $route) {
//                            AlertService::send($alert, $message, routeId: $route->id);
//                        }

                        return response()->json(['status' => true, 'message' => 'Request submitted successfully, waiting for approval from the office.'], 200);
                    }
                }
            }
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => $e->getTrace()], 500);
        }
    }

    public function invalidateMobileSessions()
    {
        try {
            $users = User::where('device_category', '!=', 'Web')->orWhereNull('device_category')->get();
            foreach ($users as $user) {
                $user->user_device_token = null;
                $user->online_status = "offline";
                $user->device_id = null;
                $user->save();
            }
            return response()->json(['status' => true, 'message' => 'Request successfull.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => $e->getTrace()], 500);
        }
    }
}
