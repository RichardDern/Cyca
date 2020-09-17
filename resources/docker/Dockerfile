# ------------------------------------------------------------------------------
# ----| PHP and dependencies |--------------------------------------------------
# ------------------------------------------------------------------------------

FROM php:7.4-fpm as php-fpm

EXPOSE 80
WORKDIR /app

RUN apt-get update && apt-get install -y \
        cron \
        locales \
        locales-all \
        git \
        curl \
        zip \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        memcached \
        libmemcached-dev \
        zlib1g-dev \
        libmagickwand-dev

RUN pecl install \
        redis \
        memcached \
        imagick

RUN docker-php-ext-enable \
        redis \
        memcached \
        imagick

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-configure intl

RUN docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        exif \
        pcntl \
        bcmath \
        intl

RUN docker-php-source delete

RUN apt-get autoremove --purge -y

RUN rm -rf /var/cache/apt
RUN rm -rf /var/lib/apt/lists/*

RUN echo "* * * * * /usr/local/bin/php /app/artisan schedule:run > /proc/1/fd/1 2>/proc/1/fd/2" > /etc/cron.d/cyca
RUN chmod 0644 /etc/cron.d/cyca
RUN crontab /etc/cron.d/cyca

# ------------------------------------------------------------------------------
# ----| Composer and dependencies |---------------------------------------------
# ------------------------------------------------------------------------------

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------------------------
# ----| Node and dependencies |-------------------------------------------------
# ------------------------------------------------------------------------------

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g laravel-echo-server

# ------------------------------------------------------------------------------

VOLUME /var/run/php-fpm.sock

CMD ["/app/resources/docker/start.sh"]
