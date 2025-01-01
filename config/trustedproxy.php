<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Aqui podes configurar quais proxies são confiáveis na tua aplicação.
    | A configuração abaixo está preparada para funcionar em ambientes como Render.
    |
    */

    'proxies' => '*', // Permite todos os proxies. Usa '*' em ambientes com balanceadores de carga como Render.

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | Define quais cabeçalhos devem ser usados para detectar a informação original
    | do cliente quando a aplicação está atrás de um proxy.
    |
    */

    'headers' => Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,
];
