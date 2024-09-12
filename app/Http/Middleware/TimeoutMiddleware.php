<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $timeout = 30 * 60; // 30 minutes

        if ($user) {
            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity > $timeout))
            {
                Auth::logout();
                return response()->json([
                    'message' => 'Session expirée'
                ], 401);
            }

            session(['last_activity' => time()]);
            
        }

        return $next($request);
    }
}
