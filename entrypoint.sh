#!/bin/sh

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground so the container doesn't exit
nginx -g "daemon off;"