# ------------------------------------------------------------------------------
# ----| PHP and dependencies |--------------------------------------------------
# ------------------------------------------------------------------------------

# Use php as parent container
FROM php:7.4-fpm as php-fpm

RUN set -ex; \
    \
    # Install system-wide dependencies
    apt-get update && apt-get install -y \
        cron \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmagickwand-dev \
        libmemcached-dev \
        libpng-dev \
        locales \
        locales-all \
        memcached \
        unzip \
        zip \
        zlib1g-dev; \
    \
    # Install PHP dependencies
    pecl install APCu-5.1.18; \
    pecl install imagick; \
    pecl install memcached; \
    pecl install redis; \
    \
    docker-php-ext-enable \
        apcu \
        imagick \
        memcached \
        redis; \
    \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        pcntl \
        pdo_mysql; \
    \
    # Cleaning
    docker-php-source delete; \
    apt-get autoremove --purge -y; \
    rm -rf /var/cache/apt; \
    rm -rf /var/lib/apt/lists/*

# Opcache
# Sourced from https://github.com/monicahq/docker/blob/master/fpm/Dockerfile
# Thanks to Monica's devs !
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="20000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

RUN set -ex; \
    \
    docker-php-ext-enable opcache; \
    { \
        echo '[opcache]'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=${PHP_OPCACHE_VALIDATE_TIMESTAMPS}'; \
        echo 'opcache.max_accelerated_files=${PHP_OPCACHE_MAX_ACCELERATED_FILES}'; \
        echo 'opcache.memory_consumption=${PHP_OPCACHE_MEMORY_CONSUMPTION}'; \
        echo 'opcache.max_wasted_percentage=${PHP_OPCACHE_MAX_WASTED_PERCENTAGE}'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.fast_shutdown=1'; \
    } > $PHP_INI_DIR/conf.d/opcache-recommended.ini; \
    \
    echo 'apc.enable_cli=1' >> $PHP_INI_DIR/conf.d/docker-php-ext-apcu.ini; \
    \
    echo 'memory_limit=512M' > $PHP_INI_DIR/conf.d/memory-limit.ini

# ------------------------------------------------------------------------------
# ----| Composer |--------------------------------------------------------------
# ------------------------------------------------------------------------------

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------------------------
# ----| Getting Cyca |----------------------------------------------------------
# ------------------------------------------------------------------------------

WORKDIR /app

COPY . /app/

RUN set -ex; \
    \
    [ ! -e ".env" ] && cp .env.example .env; \
    composer update; \
    chown -R www-data:www-data ./

# ------------------------------------------------------------------------------
# ----| Cron |------------------------------------------------------------------
# ------------------------------------------------------------------------------

COPY resources/container/cron /etc/cron.d/cyca

RUN set -ex; \
    \
    chmod 0644 /etc/cron.d/cyca && crontab /etc/cron.d/cyca

# ------------------------------------------------------------------------------

VOLUME /app

COPY resources/container/entrypoint.sh /usr/local/bin

RUN set -ex; \
    \
    chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php-fpm"]
