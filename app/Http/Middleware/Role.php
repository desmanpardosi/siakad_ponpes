<?php
/*
    Developer      : Desman Harianto Pardosi
    WhatsApp       : 0811 666 824
    E-mail         : desman@pardosi.net
*/


namespace App\Http\Middleware;

use Closure;
use Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {
        if (!Auth::check()) 
            return redirect('login');

        $user = Auth::user();

        foreach($roles as $role) {
            if($user->role == $role)
                return $next($request);
        }

        return redirect()->route('home');
    }
}
