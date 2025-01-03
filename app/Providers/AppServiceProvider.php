<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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
            Config::set('session.secure', true);
            Config::set('session.http_only', true);
            Config::set('session.same_site', 'lax');
            Config::set('app.force_https', true);
            Config::set('session.domain', 'storytail-public.onrender.com');
            Config::set('session.cookie', 'storytail_session');
            Config::set('session.path', '/');
        }
    }
}
