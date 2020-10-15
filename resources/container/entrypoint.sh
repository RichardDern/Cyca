#!/bin/bash

set -e

if [ "$1" = "php-fpm" ]; then
    . /app/.env

    if [ -z "${APP_KEY:-}" -o "$APP_KEY" = "" ]; then
        php /app/artisan key:generate --no-interaction
        echo "APP_KEY automatically set"
    else
        echo "APP_KEY already set"
    fi

    php /app/artisan migrate
    php /app/artisan storage:link
    php /app/artisan optimize
    php /app/artisan cache:clear
    chown -R www-data:www-data /app/
fi

exec "$@"
