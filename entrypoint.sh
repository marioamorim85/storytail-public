#!/bin/bash

# Mostrar erros de Laravel nos logs
echo "Preparando o ambiente de Laravel..."

# Certifica-te de que as pastas de cache e logs têm permissões corretas
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Garante que o ficheiro de log existe e tem as permissões corretas
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
    echo "Criando o ficheiro de log: storage/logs/laravel.log..."
    touch /var/www/html/storage/logs/laravel.log
    chmod 666 /var/www/html/storage/logs/laravel.log
fi

# Garante que o link de storage foi criado
php artisan storage:link || true

# Atualiza cache de configuração e rotas
php artisan config:cache
php artisan route:cache

# Executa migrações (opcional, apenas para ambientes de desenvolvimento)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Executando migrações..."
    php artisan migrate:fresh --seed --force
fi

# Inicia o Apache e acompanha os logs
echo "Iniciando o servidor Apache..."
tail -f /var/www/html/storage/logs/laravel.log & apache2-foreground
