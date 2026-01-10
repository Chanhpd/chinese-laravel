#!/bin/bash

# Laravel Deployment Script for Google Cloud
# Run this script after deploying code to server

echo "Starting Laravel deployment..."

# Set correct ownership
echo "Setting ownership..."
sudo chown -R www-data:www-data /var/www/chinese-laravel/storage
sudo chown -R www-data:www-data /var/www/chinese-laravel/bootstrap/cache

# Set correct permissions
echo "Setting permissions..."
sudo chmod -R 775 /var/www/chinese-laravel/storage
sudo chmod -R 775 /var/www/chinese-laravel/bootstrap/cache

# Create directories if they don't exist
echo "Creating necessary directories..."
mkdir -p /var/www/chinese-laravel/storage/framework/sessions
mkdir -p /var/www/chinese-laravel/storage/framework/views
mkdir -p /var/www/chinese-laravel/storage/framework/cache
mkdir -p /var/www/chinese-laravel/storage/logs

# Set permissions for new directories
sudo chown -R www-data:www-data /var/www/chinese-laravel/storage
sudo chmod -R 775 /var/www/chinese-laravel/storage

# Clear all caches
echo "Clearing caches..."
sudo -u www-data php /var/www/chinese-laravel/artisan cache:clear
sudo -u www-data php /var/www/chinese-laravel/artisan config:clear
sudo -u www-data php /var/www/chinese-laravel/artisan view:clear
sudo -u www-data php /var/www/chinese-laravel/artisan route:clear

# Optimize for production
echo "Optimizing..."
sudo -u www-data php /var/www/chinese-laravel/artisan config:cache
sudo -u www-data php /var/www/chinese-laravel/artisan route:cache
sudo -u www-data php /var/www/chinese-laravel/artisan view:cache

# Restart web server (choose your web server)
echo "Restarting web server..."
# For Apache:
# sudo systemctl restart apache2
# For Nginx:
sudo systemctl restart nginx

echo "Deployment completed!"
