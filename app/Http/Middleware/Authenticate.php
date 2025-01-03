<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        Log::info('Verificação de Autenticação', [
            'path' => $request->path(),
            'session_id' => session()->getId(),
            'is_authenticated' => auth()->check(),
            'session_data' => session()->all()
        ]);

        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}
