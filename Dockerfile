FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

# ติดตั้ง Nginx และส่วนขยาย PHP ที่จำเป็น
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

# สร้างไดเรกทอรีสำหรับ PHP-FPM socket และตั้งค่าสิทธิ์การเข้าถึง
RUN mkdir /tmp/php-fpm-socket && chmod 777 /tmp/php-fpm-socket

COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY . .
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
EXPOSE 8080
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
