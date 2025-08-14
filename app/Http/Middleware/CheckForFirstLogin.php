<?php

namespace App\Http\Middleware;

use App\Http\Controllers\FirstLoginPasswordController;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CheckForFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return $next($request);
        }

        if ($request->routeIs('password.first-login.*')) {
            return $next($request);
        }

        if ($user->requires_password_reset) {
            $controller = new FirstLoginPasswordController();
            $token = $controller->generateFirstLoginToken($user);

            Cookie::make('reset_token', $token,  60);
            Cookie::make('reset_email', $user->email, 60);

            return redirect()->route(
                'first-login.form',
                [
                    'token' => $token,
                    'email' => $user->email
                ]
            );
        }

        return $next($request);
    }
}
