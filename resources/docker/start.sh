#!/bin/bash

chown -R www-data:www-data /app

rm /app/laravel-echo-server.lock
php /app/artisan storage:link

php-fpm
