<?php

namespace Illuminate\Http\Middleware;

class VerifyCsrfToken
{
    protected array $except = [
        // Adicione aqui quaisquer rotas que devam ser excluídas da verificação CSRF
    ];
}
