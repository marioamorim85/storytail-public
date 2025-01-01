#!/bin/bash

# Mostrar erros de Laravel nos logs
echo "Preparando o ambiente de Laravel..."

# Certifica-te de que as pastas de cache e logs têm permissões corretas
echo "Definindo permissões para storage e cache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Garante que o ficheiro de log existe e tem as permissões corretas
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
    echo "Criando o ficheiro de log: storage/logs/laravel.log..."
    mkdir -p /var/www/html/storage/logs
    touch /var/www/html/storage/logs/laravel.log
    chmod 666 /var/www/html/storage/logs/laravel.log
fi

# Certifica-te de que o ficheiro de base de dados SQLite existe
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Criando o ficheiro de base de dados SQLite: database/database.sqlite..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chmod 777 /var/www/html/database/database.sqlite
fi

# Garante que o link de storage foi criado
echo "Criando o link de storage, se necessário..."
php artisan storage:link || true

# Atualiza cache de configuração e rotas
echo "Atualizando cache de configuração e rotas..."
php artisan config:cache || { echo "Erro ao atualizar config cache"; exit 1; }
php artisan route:cache || { echo "Erro ao atualizar route cache"; exit 1; }

# Executa migrações (opcional, apenas para ambientes de produção)
echo "Executando migrações..."
php artisan migrate --force || { echo "Erro ao executar migrações"; exit 1; }

# Inicia o Apache e acompanha os logs
echo "Iniciando o servidor Apache..."
tail -f /var/www/html/storage/logs/laravel.log & apache2-foreground
