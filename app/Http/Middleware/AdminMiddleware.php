<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o utilizador autenticado é um administrador
        if ($request->user()?->user_type_id !== 1) {
            return redirect('/')->with('toast_status', 'Unauthorized access.');
        }

        return $next($request);
    }
}
