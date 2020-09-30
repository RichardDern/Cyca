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
        pecl install redis; \
        pecl install memcached; \
        pecl install imagick; \
        \
        docker-php-ext-enable \
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
        cp .env.example .env; \
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
