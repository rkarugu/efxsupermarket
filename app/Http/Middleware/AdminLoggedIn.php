<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class AdminLoggedIn
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            Session::forget('otp_session');
            Session::forget('otp_verification_user_id');
            Session::forget('AdminLoggedIn');
            Session::forget('userdata');
            Session::forget('admin_userid');
            Session::forget('activity_time');
            return redirect()->route('admin.login');
        }

        if (Session::has('AdminLoggedIn')) {
            $activity = Session::get('activity_time');
            if ($activity && $activity < date('Y-m-d H:i:s', strtotime("-15 minutes"))) {
                Session::forget('otp_session');
                Session::forget('otp_verification_user_id');
                Session::forget('AdminLoggedIn');
                Session::forget('userdata');
                Session::forget('admin_userid');
                Session::forget('activity_time');

                Auth::logout();
                return redirect()->route('admin.login');
            } else {
                Session::put('activity_time', date('Y-m-d H:i:s'));
                Session::put('userdata', Auth::user());
                Session::put('admin_userid', Auth::id());
            }
            return $next($request);
        } else {
            return redirect()->route('admin.login');
        }
    }
}
