FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
   APP_ENV=production \
   APP_DEBUG=false \
   APP_URL=https://storytail-public.onrender.com

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .

# Setup inicial de diretórios e arquivos
RUN mkdir -p database \
   && mkdir -p storage/app/public/covers \
   && mkdir -p public/storage \
   && chown -R www-data:www-data /var/www/html \
   && touch database/database.sqlite \
   && chmod -R 777 database \
   && chmod -R 775 storage

# Instalação e configuração
RUN composer install --optimize-autoloader --no-dev \
   && php artisan config:cache \
   && php artisan migrate --force --seed \
   && php artisan storage:link

# Configurações do Apache
RUN echo '<Directory /var/www/html/public>\n\
   Options Indexes FollowSymLinks\n\
   AllowOverride All\n\
   Require all granted\n\
   <FilesMatch "\.(jpg|jpeg|png|gif)$">\n\
       Require all granted\n\
   </FilesMatch>\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf && \
   a2enconf laravel

# Ajustes finais de permissões
RUN chmod -R 775 storage/app/public \
   && chown -R www-data:www-data storage public/storage \
   && mkdir -p /var/www/html/storage/logs \
   && touch /var/www/html/storage/logs/laravel.log \
   && chmod 666 /var/www/html/storage/logs/laravel.log

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]
