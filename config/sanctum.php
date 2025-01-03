<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar quais domínios são "stateful", ou seja,
    | que podem usar autenticação via cookies/sessão.
    |
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'storytail-public.onrender.com',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Guard de Sanctum
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir qual guard o Sanctum usará para autenticar users.
    | Por padrão, 'web' é usado, que é o guard padrão do sistema.
    |
    */
    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Tempo de Expiração
    |--------------------------------------------------------------------------
    |
    | Define o tempo em minutos que os tokens de API devem expirar.
    | Null significa que não expiram.
    |
    */
    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Se precisar prefixar os tokens com algo específico, defina aqui.
    |
    */
    'prefix' => 'Bearer',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Configurações de middleware para rotas do Sanctum.
    |
    */
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
