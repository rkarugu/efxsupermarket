<?php

namespace App\Http\Middleware;

use App\CurrentUserAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $user = JWTAuth::parseToken()->authenticate();
        // $dbtoken = CurrentUserAccessToken::where('user_id', $user->id)->first();
        // if ($dbtoken->token != $request->token)
        // {
        //     return response()->json([
        //         'result'=>-1,
        //         'message'=>'Expired Token'
        //     ]);
        // }
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
