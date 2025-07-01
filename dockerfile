# Dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy source
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]





# FROM php:8.2-fpm

# RUN apt-get update && apt-get install -y \
#     zip unzip git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
#     && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www

# COPY . .

# RUN composer install --no-dev --optimize-autoloader
# RUN chmod -R 777 storage bootstrap/cache

# CMD ["php-fpm"]
