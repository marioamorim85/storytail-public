#!/bin/bash

echo "Preparando o ambiente de Laravel..."

# Configuração de permissões
echo "Configurando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

# Setup de logs
echo "Configurando logs..."
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
    mkdir -p /var/www/html/storage/logs
    touch /var/www/html/storage/logs/laravel.log
    chmod 666 /var/www/html/storage/logs/laravel.log
fi

# Verifica conexão com a base de dados
echo "Verificando conexão com a base de dados..."
until php -r "new PDO(getenv('DB_CONNECTION') . ':host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
    echo "Aguardando pela base de dados..."
    sleep 3
done
echo "Base de dados conectada com sucesso!"

# Recria o link de storage
echo "Recriando o link de storage..."
rm -rf /var/www/html/public/storage
php artisan storage:link || { echo "Erro ao criar o link de storage"; exit 1; }

# Cache e rotas
echo "Atualizando caches e rotas..."
php artisan config:cache || { echo "Erro ao criar o cache de configuração"; exit 1; }
php artisan route:cache || { echo "Erro ao criar o cache de rotas"; exit 1; }

# Migrações
echo "Executando migrações..."
php artisan migrate --force || { echo "Erro ao executar migrações"; exit 1; }

echo "Iniciando o servidor Apache..."
tail -f /var/www/html/storage/logs/laravel.log & apache2-foreground
