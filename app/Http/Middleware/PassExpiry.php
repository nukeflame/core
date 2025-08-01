<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use App\User;

class PassExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // $expiry = auth()->user()->pass_expiry_date;
        // $today = Carbon::today();

        // if (($today == $expiry) || ($today > $expiry)) {
        //     return redirect('passwdpg');
        // }

        return $next($request);
    }
}
