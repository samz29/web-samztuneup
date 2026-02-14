#!/bin/bash

# SamzTune-Up Auto Deployment Script for IDCloudHost
# Run this script on your IDCloudHost server

set -e  # Exit on any error

echo "🚀 Starting SamzTune-Up Deployment to IDCloudHost"
echo "================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root (sudo)"
   exit 1
fi

# Update system
print_status "Updating system packages..."
apt update && apt upgrade -y

# Install essential tools
print_status "Installing essential tools..."
apt install -y curl wget git unzip software-properties-common ufw

# Install PHP 8.2
print_status "Installing PHP 8.2 and extensions..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap php8.2-readline

# Install Composer
print_status "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js
print_status "Installing Node.js 18..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

# Install Nginx
print_status "Installing and configuring Nginx..."
apt install -y nginx
systemctl enable nginx

# Install MySQL
print_status "Installing MySQL..."
apt install -y mysql-server

# Secure MySQL installation (non-interactive)
print_status "Securing MySQL installation..."
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'StrongRootPass123!';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mysql -e "FLUSH PRIVILEGES;"

print_status "Creating database and user..."
mysql -u root -pStrongRootPass123! -e "
CREATE DATABASE samztune_up CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'samztune_user'@'localhost' IDENTIFIED BY 'SamzTune2024!';
GRANT ALL PRIVILEGES ON samztune_up.* TO 'samztune_user'@'localhost';
FLUSH PRIVILEGES;
"

# Clone repository
print_status "Cloning repository..."
cd /var/www
rm -rf samztune-up  # Remove if exists
git clone https://github.com/samz29/web-samztuneup.git samztune-up
cd samztune-up

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
print_status "Installing Node dependencies and building assets..."
npm install
npm run build

# Setup environment file
print_status "Setting up environment file..."
cp .env.production .env

# Generate application key
print_status "Generating application key..."
php artisan key:generate

# Setup database
print_status "Running database migrations..."
php artisan migrate --seed

# Cache configuration
print_status "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup permissions
print_status "Setting up permissions..."
chown -R www-data:www-data /var/www/samztune-up
chmod -R 755 /var/www/samztune-up/storage
chmod -R 755 /var/www/samztune-up/bootstrap/cache

# Configure Nginx
print_status "Configuring Nginx..."
cat > /etc/nginx/sites-available/samztune-up << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/samztune-up/public;
    index index.php index.html index.htm;

    # Handle Laravel routes
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

    # Security headers
    location ~ /\. {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/samztune-up /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# Restart services
print_status "Restarting services..."
systemctl restart php8.2-fpm
systemctl restart nginx
systemctl restart mysql

# Setup firewall
print_status "Configuring firewall..."
ufw --force reset
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# Final setup
print_status "Performing final setup..."
php artisan storage:link

print_status "🎉 Deployment completed successfully!"
print_status ""
print_status "Next steps:"
print_status "1. Update your .env file with production API keys"
print_status "2. Point your domain to this server IP"
print_status "3. Install SSL certificate with Let's Encrypt"
print_status "4. Test the application"
print_status ""
print_status "Server IP: $(curl -s ifconfig.me)"
print_status "Application URL: http://$(curl -s ifconfig.me)"
print_status ""
print_warning "Remember to change the default MySQL root password!"
print_warning "Default root password: StrongRootPass123!"
print_warning "Database user: samztune_user / SamzTune2024!"
