FROM php:8.2-apache

# Define o diretório público para o Apache e configurações do Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=https://storytail-public.onrender.com

# Atualiza o documento root no Apache e instala pacotes necessários
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
    a2enmod rewrite && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copia o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os ficheiros do Laravel para o container
COPY . .

# Instala dependências do Laravel
RUN composer install --optimize-autoloader --no-dev

# Cria a base de dados SQLite e ajusta permissões
RUN touch database/database.sqlite && \
    chmod 777 database/database.sqlite

# Configurações do Apache
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n</Directory>' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Ajusta permissões das pastas essenciais do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
    mkdir -p /var/www/html/storage/logs && \
    touch /var/www/html/storage/logs/laravel.log && \
    chmod 666 /var/www/html/storage/logs/laravel.log

# Copia o entrypoint.sh para o container
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Define o entrypoint para o container
CMD ["/entrypoint.sh"]
