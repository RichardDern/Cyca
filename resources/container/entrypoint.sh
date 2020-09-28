#!/bin/bash

set -e

if [ "$1" = "php-fpm" ]; then
    php /app/artisan migrate
    php /app/artisan storage:link
    php /app/artisan optimize
    chown -R www-data:www-data /app/
fi

exec "$@"
