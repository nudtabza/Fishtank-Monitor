#!/bin/sh

# Start PHP-FPM
php-fpm -F &

# Wait for php-fpm.sock to be created
while [ ! -S /var/run/php-fpm.sock ]; do
    echo "Waiting for php-fpm.sock..."
    sleep 0.1
done

# Start Nginx
nginx -g 'daemon off;'
