# Use official PHP image with FPM (FastCGI Process Manager)
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies (for example, extensions needed for Laravel)
RUN docker-php-ext-install pdo pdo_mysql

# Copy the existing Laravel application to the container
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 for the app to be accessible
EXPOSE 80

# Start the Laravel application using Artisan server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
