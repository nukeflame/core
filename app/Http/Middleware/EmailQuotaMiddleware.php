<?php

namespace App\Http\Middleware;

use App\Models\Email;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailQuotaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Check daily email limit
        $todayEmailCount = Email::where('sender_email', $user->email)
            ->whereDate('created_at', today())
            ->count();

        $dailyLimit = config('mail.daily_limit', 100);

        if ($todayEmailCount >= $dailyLimit) {
            return response()->json([
                'error' => 'Daily email limit exceeded. Please try again tomorrow.'
            ], 429);
        }

        // Check hourly email limit
        $hourlyEmailCount = Email::where('sender_email', $user->email)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        $hourlyLimit = config('mail.hourly_limit', 10);

        if ($hourlyEmailCount >= $hourlyLimit) {
            return response()->json([
                'error' => 'Hourly email limit exceeded. Please wait before sending more emails.'
            ], 429);
        }

        return $next($request);
    }
}
