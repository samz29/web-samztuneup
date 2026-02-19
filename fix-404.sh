#!/bin/bash

# Fix script untuk SamzTune-Up 404 error
# Jalankan sebagai root

echo "🔧 Fixing SamzTune-Up 404 Error"
echo "==============================="

APP_DIR="/var/www/samztune-up"
DOMAIN="samztekno.com"
SUBFOLDER="samztuneup"

# 1. Fix permissions
echo "1. Fixing permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR/storage
chmod -R 755 $APP_DIR/bootstrap/cache
find $APP_DIR -type f -name "*.php" -exec chmod 644 {} \;
echo "✅ Permissions fixed"

# 2. Setup Nginx configuration
echo "2. Setting up Nginx configuration..."
cat > /etc/nginx/sites-available/$DOMAIN << 'EOF'
server {
    listen 80;
    server_name samztekno.com www.samztekno.com;
    root /var/www/samztune-up/public;
    index index.php index.html index.htm;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handler
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security
    location ~ /\. {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
}

# Redirect www to non-www
server {
    listen 80;
    server_name www.samztekno.com;
    return 301 http://samztekno.com$request_uri;
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

echo "✅ Nginx configuration updated"

# 3. Test Nginx config
echo "3. Testing Nginx configuration..."
if nginx -t; then
    echo "✅ Nginx config is valid"
    systemctl reload nginx
    echo "✅ Nginx reloaded"
else
    echo "❌ Nginx config has errors"
    exit 1
fi

# 4. Setup Laravel
echo "4. Setting up Laravel..."
cd $APP_DIR

# Create .env if not exists
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || echo "APP_KEY=base64:$(openssl rand -base64 32)" > .env
fi

# Set correct APP_URL
sed -i 's|APP_URL=.*|APP_URL=https://samztekno.com|g' .env
sed -i 's|APP_ENV=.*|APP_ENV=production|g' .env
sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|g' .env

# Generate key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
fi

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Laravel configured"

# 5. Create storage directories
echo "5. Creating storage directories..."
mkdir -p storage/app/public/logos
mkdir -p storage/app/public/favicons
mkdir -p storage/logs
chmod -R 755 storage

echo "✅ Storage directories created"

# 6. Test the application
echo "6. Testing application..."
sleep 2

if curl -s http://samztekno.com/ | grep -q "SamzTune"; then
    echo "✅ Application is accessible!"
else
    echo "⚠️ Application may still have issues"
    echo "Check logs:"
    echo "  - Nginx: tail -f /var/log/nginx/error.log"
    echo "  - Laravel: tail -f $APP_DIR/storage/logs/laravel.log"
fi

echo ""
echo "==============================="
echo "Fix script completed!"
echo ""
echo "Try accessing: http://samztekno.com/"
echo "Or installer: http://samztekno.com/install"
