# ใช้ PHP-FPM image เป็น base
FROM php:8.2-fpm-alpine

# ติดตั้งส่วนขยาย PHP ที่จำเป็นสำหรับ MySQL (mysqli และ pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ติดตั้ง Nginx
RUN apk add --no-cache nginx

# ตั้งค่า Working Directory ใน Container
WORKDIR /var/www/html

# คัดลอกไฟล์โปรเจกต์ทั้งหมดของคุณไปยัง Container
COPY . .

# คัดลอก Nginx config ของเราเองไปยังโฟลเดอร์ conf.d ที่ถูกต้อง
# Nginx จะอ่านไฟล์ .conf ทั้งหมดในโฟลเดอร์นี้โดยอัตโนมัติ
COPY nginx.conf /etc/nginx/conf.d/default.conf

# คัดลอกไฟล์ PHP-FPM config (www.conf)
COPY www.conf /etc/php/8.2/fpm/pool.d/www.conf

# ตั้งค่าสิทธิ์ไฟล์และโฟลเดอร์ให้ Nginx และ PHP-FPM สามารถเข้าถึงได้
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port ที่ Nginx จะฟัง
EXPOSE 80

# คำสั่งที่จะรันเมื่อ Container เริ่มต้น (รัน Nginx และ PHP-FPM)
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
