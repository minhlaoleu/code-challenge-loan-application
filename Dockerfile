FROM php:8.2-fpm-alpine

# Copy source code and choose working directory
COPY . /var/www/html
WORKDIR /var/www/html

RUN set -eux \
    && apk add --no-cache \
\
# Install pdo_mysql
&& docker-php-ext-configure pdo_mysql --with-zlib-dir=/usr \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && true

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

COPY . .

RUN composer install
