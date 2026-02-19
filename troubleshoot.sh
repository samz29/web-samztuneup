#!/bin/bash

# Troubleshooting script untuk SamzTune-Up 404 error
# Jalankan di server

echo "🔍 Troubleshooting SamzTune-Up 404 Error"
echo "========================================"

# Check if Nginx is running
echo "1. Checking Nginx status..."
if systemctl is-active --quiet nginx; then
    echo "✅ Nginx is running"
else
    echo "❌ Nginx is not running"
    echo "Starting Nginx..."
    systemctl start nginx
fi

# Check PHP-FPM
echo ""
echo "2. Checking PHP-FPM status..."
if systemctl is-active --quiet php8.2-fpm; then
    echo "✅ PHP-FPM is running"
else
    echo "❌ PHP-FPM is not running"
    echo "Starting PHP-FPM..."
    systemctl start php8.2-fpm
fi

# Check file permissions
echo ""
echo "3. Checking file permissions..."
APP_DIR="/var/www/samztune-up"

if [ -d "$APP_DIR" ]; then
    echo "✅ Application directory exists"

    # Check ownership
    OWNER=$(stat -c '%U' $APP_DIR)
    if [ "$OWNER" = "www-data" ]; then
        echo "✅ Correct ownership (www-data)"
    else
        echo "❌ Wrong ownership: $OWNER"
        echo "Fixing ownership..."
        chown -R www-data:www-data $APP_DIR
    fi

    # Check storage permissions
    STORAGE_PERM=$(stat -c '%a' $APP_DIR/storage)
    if [ "$STORAGE_PERM" = "755" ]; then
        echo "✅ Storage permissions correct"
    else
        echo "❌ Wrong storage permissions: $STORAGE_PERM"
        chmod -R 755 $APP_DIR/storage
        chmod -R 755 $APP_DIR/bootstrap/cache
    fi

    # Check if index.php exists
    if [ -f "$APP_DIR/public/index.php" ]; then
        echo "✅ index.php exists"
    else
        echo "❌ index.php not found"
    fi

    # Check storage symlink
    if [ -L "$APP_DIR/public/storage" ]; then
        echo "✅ Storage symlink exists"
    else
        echo "❌ Storage symlink missing"
        echo "Fixing symlink..."
        cd $APP_DIR && php artisan storage:link
    fi

else
    echo "❌ Application directory does not exist: $APP_DIR"
fi

# Check Nginx configuration
echo ""
echo "4. Checking Nginx configuration..."
if nginx -t 2>/dev/null; then
    echo "✅ Nginx configuration is valid"
else
    echo "❌ Nginx configuration has errors"
    nginx -t
fi

# Check if site is enabled
echo ""
echo "5. Checking if site is enabled..."
if [ -L "/etc/nginx/sites-enabled/samztekno.com" ]; then
    echo "✅ Site is enabled"
else
    echo "❌ Site is not enabled"
    echo "Enabling site..."
    ln -s /etc/nginx/sites-available/samztekno.com /etc/nginx/sites-enabled/ 2>/dev/null || echo "Failed to enable site"
fi

# Test Laravel
echo ""
echo "6. Testing Laravel..."
cd $APP_DIR
if [ -f "artisan" ]; then
    echo "✅ Artisan file exists"

    # Test artisan
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel is working"
    else
        echo "❌ Laravel has issues"
        php artisan --version
    fi

    # Check if .env exists
    if [ -f ".env" ]; then
        echo "✅ .env file exists"
    else
        echo "❌ .env file missing"
        cp .env.example .env 2>/dev/null || echo "No .env.example found"
    fi

else
    echo "❌ Artisan file not found"
fi

# Test URL
echo ""
echo "7. Testing URL access..."
if command -v curl >/dev/null 2>&1; then
    echo "Testing http://samztekno.com/ ..."
    RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://samztekno.com/ 2>/dev/null || echo "000")
    if [ "$RESPONSE" = "200" ]; then
        echo "✅ URL returns 200 OK"
    elif [ "$RESPONSE" = "404" ]; then
        echo "❌ URL returns 404 Not Found"
    else
        echo "⚠️ URL returns $RESPONSE"
    fi
else
    echo "curl not available, skipping URL test"
fi

echo ""
echo "========================================"
echo "Troubleshooting complete!"
echo ""
echo "Common fixes:"
echo "1. Reload Nginx: sudo systemctl reload nginx"
echo "2. Clear Laravel cache: php artisan config:clear && php artisan cache:clear"
echo "3. Check logs: tail -f /var/log/nginx/error.log"
echo "4. Check Laravel logs: tail -f $APP_DIR/storage/logs/laravel.log"
