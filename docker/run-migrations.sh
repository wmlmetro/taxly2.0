#!/bin/sh

# wait for db connection
echo "Waiting for database connection..."
while ! nc -z mysql-service 3306; do
  sleep 1
done

echo "Running migrations..."
php artisan migrate --force
