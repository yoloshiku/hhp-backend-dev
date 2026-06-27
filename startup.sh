#!/bin/bash
set -e

# Fix corrupted php.ini line
sed -i 's|extension=pdo_sqlsrv.so /home/site/wwwroot/startup.sh||g' /usr/local/etc/php/php.ini

# Write SQL Server extensions to conf.d
cat > /usr/local/etc/php/conf.d/sqlsrv.ini << 'EOINI'
extension=sqlsrv.so
extension=pdo_sqlsrv.so
EOINI

cd /home/site/wwwroot

# Ensure writable dirs exist
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 bootstrap/cache storage

# Laravel post-deploy commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec php-fpm
