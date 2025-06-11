<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsrAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->bearerToken() || $request->bearerToken() != env('ERP_ISR_KEY','Ats6rZwchRKc1DxbHaXbtS4ft81VIBsahRKc1Dx')){
            \Log::info("token: ".$request->bearerToken());
            return response()->json([
                'result'=>-1,
                'message'=>'Invalid API Request!'
            ]);
        }
        return $next($request);
    }
}
