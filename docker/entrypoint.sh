#!/bin/sh

# Create necessary directories if they don't exist
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/logs

# Ensure the bootstrap/cache directory exists and has the right permissions
rm -rf /var/www/bootstrap/cache/*
mkdir -p /var/www/bootstrap/cache

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Wait for database to be ready
echo "Waiting for database connection..."
while ! nc -z mysql-service 3306; do
  sleep 1
done
echo "Database connection established!"

# Wait for Redis to be ready
echo "Waiting for Redis connection..."
while ! nc -z redis-service 6379; do
  sleep 1
done
echo "Redis connection established!"

# Clear and cache routes and config
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create required cache files
php artisan config:cache
php artisan route:cache
php artisan view:cache



# Create storage link if it doesn't exist
echo "Creating storage link..."
php artisan storage:link --force || true

# Set proper permissions again after operations
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Start supervisor
exec "$@"
