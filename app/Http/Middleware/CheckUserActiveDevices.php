<?php

namespace App\Http\Middleware;

use App\CurrentUserAccessToken;
use Closure;
use Illuminate\Http\Client\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckUserActiveDevices
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
        try {

            $token = JWTAuth::getToken();
            $tokenParts = explode('.', $token);
            $encodedPayload = $tokenParts[1];
            $decodedPayload = base64_decode(strtr($encodedPayload, '-_', '+/'));
            $payload = json_decode($decodedPayload, true);
            $userId = $payload['sub'];

            try {
                $user = CurrentUserAccessToken::where('user_id', $userId)->first();
                if(!$user ||  !($user->token == $token)){
                    return response()->json(['status'=>false, 'message'=>'Please login from your authenticated device.'],401);
                }
            } catch (\Throwable $e) {
                // pass
            }
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

                return response()->json(['token_expired'], $e->getStatusCode());
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['token_invalid'], $e->getStatusCode());
            } else {
                return response()->json(['error' => 'Token is required']);
            }
        }
        return $next($request);
    }
}