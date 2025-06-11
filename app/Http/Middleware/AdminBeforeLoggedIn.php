<?php
namespace App\Http\Middleware;

use Closure;
use Session;

class AdminBeforeLoggedIn{
    public function handle($request, Closure $next){
        //dd(Session::get('LoggedIn'));
       
        if(!Session::has('AdminLoggedIn'))
        {

            return $next($request);
        }
        else
        {
        	
        	Session::flash('warning','Invalid request');
        	return redirect()->route('admin.dashboard');
        }

        
    }
}
