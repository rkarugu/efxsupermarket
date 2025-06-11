<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckPreviousDayOperationShiftBalanced
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        $restaurant_id = Auth::user()->restaurant_id;
//        $date = Carbon::now()->subDay()->toDateString();
//
//        $previousShift = DB::table('operation_shifts')
//            ->where('restaurant_id', $restaurant_id)
//            ->where('date', $date)
//            ->first();
//
//
//        if ($previousShift) {
//            if (!$previousShift->balanced && !$previousShift->manual_override) {
//                if ($request->expectsJson() || $request->is('api/*')) {
//                    return response()->json([
//                        'status' => -1,
//                        'error' => 'Previous day\'s operation shift is not balanced'
//                    ], 400);
//                } else {
//                    return redirect()->route('admin.dashboard')->with('warning', 'Previous day\'s operation shift is not balanced');
//                }
//            }
//        }
        return $next($request);
    }
}
