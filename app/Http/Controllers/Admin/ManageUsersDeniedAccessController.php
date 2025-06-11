<?php

namespace App\Http\Controllers\Admin;

use App\CurrentUserAccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\UserAccessRequest;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Model\User;
use App\UserLinkedDevice;
use Session;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
class ManageUsersDeniedAccessController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'user-denied-accesses';
        $this->base_route = 'admin.manage.users-access-denied';
        $this->resource_folder = 'admin.usersAccessRequests';
        $this->base_title = 'Manage Users Denied Access';
    }

    public function deniedaccess()
    {
        $title = 'Access Denied';
        return view('admin.users.denied-access', compact('title'));
    }
  
    public function manageUsersDeniedAccess()
    {
        $users = UserAccessRequest::with(['user' => function ($query) {
            return $query->select(['id', 'name', 'email', 'phone_number','role_id']);
        }])->where('status', 'pending')->get();
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'users'));
    }
    public function getUserRequestDetails($requestId)
    {
        $requestAccess = UserAccessRequest::select(['id', 'reason', 'user_id'])->where('id', $requestId)->first();
        if (!$requestAccess) {
            return response()->json(['error' => 'Request details not found']);
        }
        return response()->json($requestAccess);
    }
  

    
    public function approveUserAccessRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                 'user_requested_access' => 'required',
            ]);

            if ($validator->fails()) {
                
                $error = $this->validationHandle($validator->messages());
                return redirect()->back()->withErrors($error)->withInput();
            } else {
                $check_existing_request = UserAccessRequest::where('id', $request->user_requested_access)->first();
                if ($check_existing_request) {
                    $user = User::where('id', $check_existing_request->user_id)->first();

                     
                        $userId = Session::get('admin_userid');

                        UserAccessRequest::where('id', $request->user_requested_access)->update([
                            'status' => 'approved',
                            'reviewed_by' => $userId,
                            'reviewed_at' => Carbon::now(), 
                        ]);
                        $sms_msg = "Hello, " . $user->name . " , Your request has been approved. Please login to proceed.";
                        // send_sms($user->phone_number, $sms_msg);

                        sendMessage($sms_msg, $user->phone_number);

                        if ($user) {
                           
                            $userDevice = UserLinkedDevice::where('user_id', $user->id)->first();
                            if ($userDevice) {
                                $userDevice->device_id =  Str::uuid();
                                $userDevice->save();
                            }
                            CurrentUserAccessToken::where('user_id', $user->id)->delete();
                        }
                        return redirect()->route('admin.manage.users-access-denied')->with('success', 'User approved to use the system');
                    
                } else {
                    return redirect()->route('admin.manage.users-access-denied')->with('danger', 'User Request details not found');
                }
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Session::flash('danger', $msg);
            return redirect()->back()->withInput();
        }
    }
    public function declineUserAccessRequest(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'decline_request_response' => 'required|string|min:1',
                'declined_user_requested_access' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return redirect()->back()->withErrors($error)->withInput();
            } else {
                $check_existing_request = UserAccessRequest::where('id', $request->declined_user_requested_access)->first();
                if ($check_existing_request) {
                    $user = User::where('id', $check_existing_request->user_id)->first();

                    
                        $userId = Session::get('admin_userid');

                        UserAccessRequest::where('id', $request->declined_user_requested_access)->update([
                            'status' => 'declined',
                            'reviewed_by' => $userId,
                            'reviewed_at' => Carbon::now(),
                            'decline_reason'=>$request->decline_request_response, 
                        ]); 
                        

                        $sms_msg = "Hello, " . $user->name . " , " .$request->decline_request_response;
                        // send_sms($user->phone_number, $sms_msg);
                        sendMessage($sms_msg, $user->phone_number);

                        return redirect()->route('admin.manage.users-access-denied')->with('success', 'User request declined and notified via sms');
                    
                } else {
                    return redirect()->route('admin.manage.users-access-denied')->with('danger', 'User Request details not found');
                }
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Session::flash('danger', $msg);
            return redirect()->back()->withInput();
        }
    }
}
