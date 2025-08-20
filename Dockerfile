# ใช้ PHP-FPM image เป็น base
FROM php:8.2-fpm-alpine

# ตั้งค่า Working Directory ใน Container
WORKDIR /var/www/html

# ติดตั้งส่วนขยาย PHP ที่จำเป็นสำหรับ MySQL (mysqli และ pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ติดตั้ง Nginx
RUN apk add --no-cache nginx

# ลบ default Nginx config
RUN rm -f /etc/nginx/conf.d/default.conf

# คัดลอก Nginx config ของเราเอง (จะสร้างในขั้นตอนถัดไป)
COPY nginx.conf /etc/nginx/conf.d/default.conf

# คัดลอกไฟล์โปรเจกต์ทั้งหมดของคุณไปยัง Container
COPY . .

# ตั้งค่าสิทธิ์ไฟล์และโฟลเดอร์
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port ที่ Nginx จะฟัง
EXPOSE 80

# คำสั่งที่จะรันเมื่อ Container เริ่มต้น (รัน Nginx และ PHP-FPM)

CMD ["sh", "-c", "php-fpm && nginx -g 'daemon off;'"]
