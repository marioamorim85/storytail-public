<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))

    // Configura os middlewares
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'csrf' => VerifyCsrfToken::class,
            'admin' => AdminMiddleware::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->web(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ]);
    })

    // Configura as rotas
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up', // Endpoint de verificação de saúde
    )

    // Configura exceções (se necessário)
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
