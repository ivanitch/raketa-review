FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Add Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Add user
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
RUN chown -R www:www /var/www/html
USER www

# Set working directory
WORKDIR /var/www/html

EXPOSE 80/tcp
EXPOSE 9000

CMD ["php-fpm"]