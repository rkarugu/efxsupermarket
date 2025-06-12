<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class endOfDayLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $store_id = Auth::user()->wa_location_and_store_id;
       $date = Carbon::now()->subDay()->toDateString();

        $record = DB::table('wa_close_branch_end_of_days')
            ->where('wa_location_and_store_id', $store_id)
            ->whereDate('opened_date', $date)
            ->first();

        if (config('app.env') === 'local' || config('app.skip_eod_middleware') === true) {
            return $next($request);
        }

       // if (!$record) {
       //     return redirect()->route('admin.dashboard')->with('warning', 'End of Day For Branch closing was not done Yesteryear. ');
       // }
       return $next($request);
    }
}
