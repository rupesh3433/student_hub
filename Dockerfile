FROM php:8.1-fpm-alpine

# Set ServerName to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/httpd/conf/httpd.conf

# Install necessary PHP extensions
RUN apk add --no-cache php8-gd php8-mbstring php8-xml php8-openssl

# Copy application files
COPY . /var/www/html/

EXPOSE 80
CMD ["php-fpm"]
