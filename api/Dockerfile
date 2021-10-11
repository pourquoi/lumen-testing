FROM composer:1.9.3 as builder

COPY composer.json composer.* /app/
WORKDIR /app
RUN mkdir -p /app/tests
RUN composer install --no-dev


FROM php:7.4-fpm

RUN apt update && apt install -y \
    build-essential \
    curl \
    zip \
    unzip \
    libpq-dev \
    supervisor \
    libmagickwand-dev --no-install-recommends

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# SUPERVISOR
COPY ./docker/supervisor/conf.d/lumen-worker.conf /etc/supervisor/conf.d/lumen-worker.conf
COPY ./docker/supervisor/conf.d/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
RUN touch /var/run/supervisor.sock
RUN chmod 777 /var/run/supervisor.sock

RUN docker-php-ext-install pdo pdo_pgsql

RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --from=builder /app/vendor /app/vendor

WORKDIR /app

COPY composer.json composer.* /app/

RUN mkdir -p /app/tests
RUN composer install

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
