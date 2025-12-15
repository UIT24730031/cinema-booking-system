# Sử dụng PHP 7.4 với Apache
FROM php:7.4-apache

# Cài đặt extensions PHP cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy toàn bộ source code vào /var/www/html
COPY . /var/www/html/

# Set quyền cho thư mục
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Khởi động Apache
CMD ["apache2-foreground"]
