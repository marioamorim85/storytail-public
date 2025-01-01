<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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

        // Força o uso de HTTPS em produção
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
