<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MobileDeviceMultipleSession
{
   /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            try{
                //$user = JWTAuth::toUser($request->input('token'));
                 $user = JWTAuth::parseToken()->authenticate();
                 $token = JWTAuth::getToken();
                 $userDeviceToken = $user->user_device_token;  
                 if (Hash::check($token, $userDeviceToken)) {
                     Log::info("The authenticated user is '{$user}' and the token is '{$token}'");

                 } else { 
                     return response()->json(['error' => 'You cannot access the system from multiple devices at the same time'], 403);
                 }  
            }catch (JWTException $e) {
                if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    
                    return response()->json(['token_expired'], $e->getStatusCode());
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return response()->json(['token_invalid'], $e->getStatusCode());
                }else{
                    return response()->json(['error'=>'Token is required']);
                }
            }
           return $next($request);
        }
}
