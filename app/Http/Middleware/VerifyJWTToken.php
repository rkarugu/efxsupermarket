<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Client\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        dd('hit');
        try {
            // $user = JWTAuth::toUser($request->input('token'));
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['token_expired'], $e->getStatusCode());
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['token_invalid'], $e->getStatusCode());
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException ) {
                return response()->json(['token_invalid'], $e->getStatusCode());
            }else {
                return response()->json(['error' => 'Token is required']);
            }
        }
        return $next($request);
    }
}