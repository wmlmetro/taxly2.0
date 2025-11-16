#!/bin/sh

# Ensure the cache directory exists and has the right permissions
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/bootstrap/cache

# Run Horizon
exec php /var/www/artisan horizon
