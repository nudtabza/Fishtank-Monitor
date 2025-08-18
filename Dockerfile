# ใช้ PHP-FPM image เป็น base
FROM php:8.2-fpm-alpine

# ตั้งค่า Working Directory ใน Container
WORKDIR /var/www/html

# อัปเดตและติดตั้ง Nginx พร้อมส่วนขยาย PHP ที่จำเป็น
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

# *** แก้ไข: คัดลอกไฟล์การตั้งค่า Nginx ไปยังโฟลเดอร์ 'conf.d' ที่ถูกต้อง ***
COPY nginx.conf /etc/nginx/conf.d/default.conf

# คัดลอกไฟล์โปรเจกต์ของคุณ
COPY . .

# ตั้งค่าสิทธิ์ไฟล์และโฟลเดอร์ให้ถูกต้อง
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
