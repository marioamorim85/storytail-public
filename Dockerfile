FROM php:8.2-apache

# Define o diretório público para o Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instala pacotes necessários
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Instala extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

# Copia o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os ficheiros do Laravel
COPY . .

# Instala dependências do Laravel
RUN composer install --optimize-autoloader --no-dev

# Cria a base de dados SQLite e ajusta permissões
RUN touch database/database.sqlite
RUN chmod 777 database/database.sqlite

# Configurações do Apache
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n</Directory>' > /etc/apache2/conf-available/laravel.conf
RUN a2enconf laravel

# Permissões de pastas do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Garante que as permissões do Laravel estão corretas
RUN mkdir -p /var/www/html/storage/logs
RUN touch /var/www/html/storage/logs/laravel.log
RUN chmod 666 /var/www/html/storage/logs/laravel.log

# Copia o entrypoint.sh para o container
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Define o entrypoint
CMD ["/entrypoint.sh"]
