<?php

namespace App\Http\Middleware;
use Closure;
use Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Session::has('otp_verification_user_id'))
        {
            return $next($request);
        }
        else
        {
        	return redirect()->route('admin.login');
        }
    }
}
