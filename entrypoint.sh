#!/bin/sh

# เปลี่ยน user/group ของ php-fpm.sock ไปเป็น nginx เพื่อให้ Nginx เข้าถึงได้
# สร้างไฟล์ sock ที่ต้องการ
mkdir /var/run/php-fpm

# Start PHP-FPM
php-fpm -F &

# Wait for php-fpm.sock to be created
while [ ! -S /tmp/php-fpm-socket/php-fpm.sock ]; do
    echo "Waiting for php-fpm.sock..."
    sleep 0.1
done

# Start Nginx
nginx -g 'daemon off;'
