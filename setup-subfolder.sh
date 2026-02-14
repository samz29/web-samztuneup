#!/bin/bash

# Setup script untuk SamzTune-Up di subfolder samztekno.com/samztuneup/
# Jalankan sebagai root

set -e

echo "🚀 Setup SamzTune-Up di Subfolder"

# Variables
DOMAIN="samztekno.com"
SUBFOLDER="samztuneup"
APP_DIR="/var/www/samztune-up"
WEB_ROOT="/var/www/html"

# Install dependencies
echo "📦 Installing dependencies..."
apt update
apt install -y nginx mysql-server php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd composer

# Setup PHP-FPM
systemctl enable php8.2-fpm
systemctl start php8.2-fpm

# Setup Nginx untuk subfolder
echo "🌐 Configuring Nginx untuk subfolder..."
cat > /etc/nginx/sites-available/${DOMAIN} << EOF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${WEB_ROOT};
    index index.html index.htm index.php;

    # Handle subfolder /${SUBFOLDER}/
    location /${SUBFOLDER}/ {
        alias ${APP_DIR}/public/;
        index index.php index.html index.htm;

        # Handle Laravel routes
        location ~ ^/${SUBFOLDER}/(.+\.php)$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME ${APP_DIR}/public/\$1;
            include fastcgi_params;
        }

        # Handle static assets
        location ~* ^/${SUBFOLDER}/(.+\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot))$ {
            alias ${APP_DIR}/public/\$1;
            expires 1y;
            add_header Cache-Control "public, immutable";
        }

        # Laravel routing
        try_files \$uri \$uri/ /${SUBFOLDER}/index.php?\$query_string;

        # Security
        location ~ /\. {
            deny all;
        }
    }

    # Default location untuk domain utama
    location / {
        try_files \$uri \$uri/ =404;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
}

# Redirect www ke non-www
server {
    listen 80;
    server_name www.${DOMAIN};
    return 301 http://${DOMAIN}\$request_uri;
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx config
nginx -t

# Setup aplikasi Laravel
echo "⚙️ Setup Laravel application..."
cd ${APP_DIR}

# Install dependencies
composer install --no-dev --optimize-autoloader

# Setup permissions
chown -R www-data:www-data ${APP_DIR}
chmod -R 755 ${APP_DIR}/storage
chmod -R 755 ${APP_DIR}/bootstrap/cache

# Setup .env
cp .env.example .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}/${SUBFOLDER}|g" .env
sed -i "s|APP_ENV=.*|APP_ENV=production|g" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" .env

# Generate app key
php artisan key:generate

echo "🔒 Setup SSL dengan Let's Encrypt..."
apt install -y certbot python3-certbot-nginx
certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN}

# Reload services
systemctl reload nginx
systemctl restart php8.2-fpm

echo "✅ Setup selesai!"
echo ""
echo "🌐 Akses aplikasi di: https://${DOMAIN}/${SUBFOLDER}/"
echo "🔧 Akses installer di: https://${DOMAIN}/${SUBFOLDER}/install"
echo ""
echo "📋 Selanjutnya:"
echo "1. Setup database MySQL"
echo "2. Jalankan installer di browser"
echo "3. Konfigurasi API keys production"
