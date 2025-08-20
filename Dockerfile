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

# คัดลอกไฟล์ Nginx config ของเราเอง
COPY nginx.conf /etc/nginx/nginx.conf

# คัดลอกไฟล์ PHP-FPM config (www.conf)
# นี่คือส่วนสำคัญที่ต้องเพิ่มเข้าไปเพื่อให้ PHP-FPM รันที่พอร์ต 9000
COPY www.conf /etc/php/8.2/fpm/pool.d/www.conf

# ตั้งค่าสิทธิ์ไฟล์และโฟลเดอร์ให้ Nginx และ PHP-FPM สามารถเข้าถึงได้
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port ที่ Nginx จะฟัง
EXPOSE 80

# คำสั่งที่จะรันเมื่อ Container เริ่มต้น (รัน Nginx และ PHP-FPM)
# เพิ่ม -c /etc/nginx/nginx.conf เพื่อให้ Nginx ใช้ config ที่เรากำหนด
CMD php-fpm -F && nginx -c /etc/nginx/nginx.conf -g "daemon off;"
