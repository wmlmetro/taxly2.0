#!/bin/bash

# Wait for database to be ready
echo "Waiting for database connection..."
while ! nc -z db 3306; do
  sleep 1
done
echo "Database connection established!"

# Wait for Redis to be ready
echo "Waiting for Redis connection..."
while ! nc -z redis 6379; do
  sleep 1
done
echo "Redis connection established!"

# Run Laravel migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache

# Start supervisor
exec "$@"
