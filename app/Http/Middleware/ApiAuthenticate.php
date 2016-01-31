<?php

namespace Dietando\Http\Middleware;

use Closure;
use Dietando\Entities\AuthToken;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->has('auth_token')) {
            return response('Unauthorized.', 401);
        } else {
            $auth = AuthToken::where('token', '=', $request->get('auth_token'))->first();

            if(!$auth) {
                return response('Unauthorized', 401);
            }

            Auth::loginUsingId($auth->user_id);
        }

        return $next($request);
    }
}
