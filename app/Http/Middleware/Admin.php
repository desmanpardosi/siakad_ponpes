<?php
/*
    Developer   : Desman Harianto Pardosi
    E-mail      : desman@pardosi.net
    Website     : www.dhp.pw
*/

namespace App\Http\Middleware;

use Closure;

class Admin
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
        if ($request->user() && !in_array($request->user()->role, [0,1]))
        {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
