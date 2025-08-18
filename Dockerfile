FROM php:8.2-fpm-alpine
WORKDIR /var/www/html
RUN apk update && apk add --no-cache \
    nginx \
    postgresql-client \
    php82-fpm \
    php82-session \
    php82-json \
    php82-mbstring \
    php82-openssl \
    php82-curl \
    php82-dom \
    php82-exif \
    php82-fileinfo \
    php82-iconv \
    php82-gd \
    php82-xml \
    php82-xmlreader \
    php82-simplexml \
    php82-xmlwriter \
    php82-zip \
    php82-zlib \
    php82-ctype \
    php82-bcmath \
    php82-intl \
    php82-tokenizer \
    php82-pdo_pgsql \
    php82-pdo_sqlite
COPY . .
COPY nginx.conf /etc/nginx/nginx.conf
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
EXPOSE 8080
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
CMD ["entrypoint.sh"]