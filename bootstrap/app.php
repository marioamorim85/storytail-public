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
            'admin' => AdminMiddleware::class, // Define o alias 'admin'
        ]);

        $middleware->web(append: [
            VerifyCsrfToken::class, // Adiciona o middleware CSRF às rotas web
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
