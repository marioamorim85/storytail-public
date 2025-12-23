<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?: (string) Str::uuid();

        // Ensure we store it in Facade Context for Octane/Worker isolation
        Context::add('request_id', $requestId);

        $response = $next($request);

        // Always attach the ID to the response
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->logRequestSummary($request, $response);
    }

    protected function logRequestSummary(Request $request, Response $response): void
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        
        // Fallback for route name
        if (!$routeName) {
            $routeName = $request->path() . ' [' . $request->method() . ']';
        }

        $context = [
            'route' => $routeName,
            'method' => $request->method(),
            'status' => $response->getStatusCode(),
            'latency_ms' => defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0,
            'user_id' => $request->user()?->id,
            // Request ID is already in context, but adding here explicit for clarity in summary
            'request_id' => Context::get('request_id'), 
        ];

        Log::info('Request Summary', $context);
    }
}
