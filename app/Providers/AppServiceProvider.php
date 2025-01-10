<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\BlockSuspiciousRequests;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define o comprimento padrão para colunas de string no banco de dados
        Schema::defaultStringLength(191);

        // Configurações para ambiente de produção
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Configurações de sessão baseadas no domínio atual
            $domain = parse_url(config('app.url'), PHP_URL_HOST); // Obtém o domínio do APP_URL

            Config::set('session.secure', true);
            Config::set('session.http_only', true);
            Config::set('session.same_site', 'lax');
            Config::set('session.domain', $domain); // Define o domínio dinamicamente
            Config::set('session.cookie', 'storytail_session');
            Config::set('session.path', '/');
        }

        // Aplica o middleware globalmente
        $this->app['router']->pushMiddlewareToGroup('web', BlockSuspiciousRequests::class);
    }
}
