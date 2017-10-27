<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
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
        if(auth()->check() && !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN ,'You have no permissions to perform this action');
        }
        return $next($request);
    }
}
