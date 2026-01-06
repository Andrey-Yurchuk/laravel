FROM dunglas/frankenphp:php8.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions \
    zip \
    pdo_pgsql \
    pgsql \
    redis \
    pcntl \
    sockets \
    xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock artisan ./

RUN composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && git config --global --add safe.directory /var/www/html

EXPOSE 80

COPY docker/frankenphp/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]

