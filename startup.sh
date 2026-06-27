#!/bin/bash
cd /home/site/wwwroot

# Ensure writable dirs exist
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 bootstrap/cache storage

# Run Laravel post-deploy commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

#!/bin/bash
set -e

# Install SQL Server extensions if not already present
if [ ! -f /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_sqlsrv.so ]; then
    apt-get update -y
    apt-get install -y unixodbc-dev
    pecl install sqlsrv pdo_sqlsrv
fi

# Write ini file cleanly (separate file, not appending to php.ini)
cat > /usr/local/etc/php/conf.d/sqlsrv.ini << 'EOF'
extension=sqlsrv.so
extension=pdo_sqlsrv.so
EOF

# Run Laravel migrations
cd /home/site/wwwroot
php artisan config:cache
php artisan migrate --force

# Start PHP-FPM
php-fpm

# Configure Nginx to serve from /public
cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 8080;
    listen [::]:8080;
    root /home/site/wwwroot/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        gzip_static on;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

service nginx reload
