<?php

namespace App\Http\Middleware;

use App\Models\AdminAuthToken;
use Closure;
use Illuminate\Http\Request;

class EnsureRequestIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('token') && $request->header('token') === "spring_music_007_mission_token") {
            return $next($request);
        } else if ($request->header('admin-token')) {
            $adminAuthToken = AdminAuthToken::where('admin_auth_token', $request->header('admin-token'))->first();
            if ($adminAuthToken) {
                return $next($request);
            }
            abort(401);
        }
        abort(403);
    }
}
