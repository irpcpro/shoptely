<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TelegramWebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response|bool
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): bool|Response
    {
        if($request->route()->parameters()['webhook_token'] == env('telegram_webhook_token')){
            return $next($request);
        }

        Log::error("ERROR:: you can't access to this route.");
        return true;
        // throw new \Exception("you can't access to this route.");
    }
}
