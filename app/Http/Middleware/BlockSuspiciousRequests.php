<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlockSuspiciousRequests
{
    /**
     * Lista de caminhos suspeitos a bloquear.
     */
    protected $blockedPaths = [
        'wp-admin',
        'wp-includes',
        'xmlrpc.php',
        'wlwmanifest.xml',
        'setup-config.php',
    ];

    /**
     * Lista de User-Agents suspeitos.
     */
    protected $blockedUserAgents = [
        'python-httpx',
        'curl',
        'wget',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Bloquear endpoints específicos
        foreach ($this->blockedPaths as $path) {
            if ($request->is($path)) {
                Log::warning('Acesso bloqueado ao caminho suspeito.', [
                    'path' => $path,
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
                abort(404); // Retorna erro 404 para impedir o acesso
            }
        }

        // Bloquear User-Agents maliciosos
        $userAgent = $request->header('User-Agent');
        foreach ($this->blockedUserAgents as $blockedAgent) {
            if (stripos($userAgent, $blockedAgent) !== false) {
                Log::warning('Acesso bloqueado por User-Agent.', [
                    'user_agent' => $userAgent,
                    'ip' => $request->ip(),
                ]);
                abort(403, 'Acesso negado.'); // Retorna erro 403 Forbidden
            }
        }

        return $next($request);
    }
}

