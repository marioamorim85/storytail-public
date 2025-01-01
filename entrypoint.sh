#!/bin/bash

echo "Preparando o ambiente de Laravel..."

# Configuração de permissões
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

# Setup de logs
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
    mkdir -p /var/www/html/storage/logs
    touch /var/www/html/storage/logs/laravel.log
    chmod 666 /var/www/html/storage/logs/laravel.log
fi

# Setup SQLite
if [ ! -f /var/www/html/database/database.sqlite ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chmod 777 /var/www/html/database/database.sqlite
fi

# Recria o link de storage
echo "Recriando o link de storage..."
rm -rf /var/www/html/public/storage
php artisan storage:link || { echo "Erro ao criar o link de storage"; exit 1; }

# Cache e rotas
php artisan config:cache
php artisan route:cache

# Migrações
php artisan migrate --force

echo "Iniciando o servidor Apache..."
tail -f /var/www/html/storage/logs/laravel.log & apache2-foreground
