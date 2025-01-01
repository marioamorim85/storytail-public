#!/bin/bash

echo "Preparando o ambiente de Laravel..."

# Configuração de permissões iniciais
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

# Storage e cache
rm -f /var/www/html/public/storage
php artisan storage:link
mkdir -p storage/app/public/covers
chmod -R 775 storage/app/public
chown -R www-data:www-data storage/app/public

# Cache e configurações
php artisan config:cache || { echo "Erro ao atualizar config cache"; exit 1; }
php artisan route:cache || { echo "Erro ao atualizar route cache"; exit 1; }

# Migrações
php artisan migrate --force || { echo "Erro ao executar migrações"; exit 1; }

# Inicia Apache
echo "Iniciando o servidor Apache..."
tail -f /var/www/html/storage/logs/laravel.log & apache2-foreground
