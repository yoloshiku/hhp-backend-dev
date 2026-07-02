#!/bin/bash
set -e

# Copy production php.ini baseline
# (moved here so Azure startup command can be a single line)
cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Fix corrupted php.ini line
sed -i 's|extension=pdo_sqlsrv.so /home/site/wwwroot/startup.sh||g' /usr/local/etc/php/php.ini

# Write SQL Server extensions to conf.d
cat > /usr/local/etc/php/conf.d/sqlsrv.ini << 'EOINI'
extension=sqlsrv.so
extension=pdo_sqlsrv.so
EOINI

# Write correct nginx config to disk
# init_container.sh will re-launch nginx AFTER exec php-fpm using
# sites-available/default, so we also schedule a delayed background
# restart (see bottom of script) to apply the correct config last.
cat > /etc/nginx/sites-enabled/default << 'EONGINX'
server {
    listen 8080;
    listen [::]:8080;
    root /home/site/wwwroot/public;
    index index.php index.html index.htm;
    server_name _;
    port_in_redirect off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    error_page 500 502 503 504 /50x.html;
    location = /50x.html {
        root /html/;
    }

    location ~ /\.git {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        include fastcgi_params;
        fastcgi_param HTTP_PROXY "";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param QUERY_STRING $query_string;
        fastcgi_intercept_errors off;
        fastcgi_connect_timeout         300;
        fastcgi_send_timeout           3600;
        fastcgi_read_timeout           3600;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
}
EONGINX

# Sync config to sites-available so both locations are consistent
cp /etc/nginx/sites-enabled/default /etc/nginx/sites-available/default

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

# Background job: wait for init_container.sh to re-launch nginx after
# exec php-fpm, then overwrite config and restart nginx with correct settings.
# 10s delay is enough for init_container.sh to finish its nginx re-launch.
(
  sleep 10
  cp /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
  nginx -s stop
  sleep 1
  nginx
) &

exec php-fpm
