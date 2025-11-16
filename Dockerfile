# Use platform-agnostic base image
FROM --platform=linux/amd64 php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Install Composer dependencies (including dev for seeding)
RUN composer install --no-scripts --no-interaction

# Copy application code
COPY . .

# Now run composer scripts and optimize autoloader (include dev for seeding)
RUN composer dump-autoload --optimize

# Generate Swagger documentation
RUN php artisan l5-swagger:generate

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Create nginx cache directory with proper permissions
RUN mkdir -p /var/cache/nginx/fastcgi && \
    chown -R www-data:www-data /var/cache/nginx

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup scripts
COPY docker/start-queue-worker.sh /var/www/docker/
COPY docker/start-horizon.sh /var/www/docker/
RUN chmod +x /var/www/docker/start-queue-worker.sh /var/www/docker/start-horizon.sh

# Copy entrypoint script and ensure it has Unix line endings
COPY --chmod=+x docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Start supervisor
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
