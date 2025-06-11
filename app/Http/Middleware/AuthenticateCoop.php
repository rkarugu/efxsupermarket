<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCoop
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('Authorization') === true) {
            $credentials = base64_decode(substr($request->header('Authorization'), 6));
            list($username, $password) = explode(':', $credentials);

            if (($username == env('COOP_USERNAME')) && ($password == env('COOP_PASSWORD'))) {
                return $next($request);
            }
        }

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('HTTP/1.1 401 Authorization Required');
        header('WWW-Authenticate: Basic realm="Unauthorized Access"');

        return response()->json(['message' => 'Invalid access credentials'], 401);
    }
}
