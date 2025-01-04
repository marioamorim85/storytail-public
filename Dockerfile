FROM php:8.2-apache

# Define o diretório público para o Apache e configurações do Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=https://storytail-public.onrender.com \
    COMPOSER_MEMORY_LIMIT=-1

# Atualiza pacotes e instala dependências necessárias
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    apt-get update && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        sqlite3 \
        libsqlite3-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite && \
    a2enmod rewrite headers deflate && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Copia o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os ficheiros do Laravel
COPY . .

# Configura o diretório e arquivo do SQLite com permissões adequadas
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite && \
    chmod 777 /var/www/html/database/database.sqlite && \
    chmod 777 /var/www/html/database

# Instala dependências do Laravel, configura o ambiente e executa o seed
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts && \
    composer dump-autoload --optimize --no-dev --classmap-authoritative && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan storage:link && \
    php artisan migrate --force --seed

# Configurações do Apache para permitir acesso ao storage
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    <FilesMatch "\\.(jpg|jpeg|png|gif|css|js)$">\n\
        Require all granted\n\
    </FilesMatch>\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Ajusta permissões das pastas essenciais do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage && \
    mkdir -p /var/www/html/storage/logs && \
    touch /var/www/html/storage/logs/laravel.log && \
    chmod 666 /var/www/html/storage/logs/laravel.log

# Configura o PHP para melhor desempenho
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory_limit.ini && \
    echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Copia o entrypoint.sh para o container
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Define o entrypoint para o container
CMD ["/entrypoint.sh"]
