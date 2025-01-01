FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev

# Create SQLite database
RUN touch database/database.sqlite
RUN chmod 777 database/database.sqlite

# Optimize Laravel
RUN php artisan optimize
RUN php artisan view:cache
RUN php artisan config:cache
RUN php artisan route:cache

# Apache configuration
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n</Directory>' > /etc/apache2/conf-available/laravel.conf
RUN a2enconf laravel

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/public
RUN mkdir -p /var/www/html/public/storage
RUN chmod -R 775 /var/www/html/storage

# Setup storage and migrate
RUN php artisan storage:link
RUN php artisan migrate:fresh --seed --force
