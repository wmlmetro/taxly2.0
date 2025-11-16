#!/bin/sh

# Ensure the cache directory exists and has the right permissions
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/bootstrap/cache

# Run the queue worker
exec php /var/www/artisan queue:work --sleep=3 --tries=3 --timeout=90
