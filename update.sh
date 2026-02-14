#!/bin/bash

# SamzTune-Up Auto Update Script
# Usage: ./update.sh [branch]
# Default branch: main

set -e

BRANCH=${1:-main}
BACKUP_DIR="/home/samztekn/backups/$(date +%Y%m%d_%H%M%S)"

echo "🚀 Updating SamzTune-Up from GitHub ($BRANCH branch)"
echo "==================================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Functions
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

backup_current() {
    print_status "Creating backup..."
    mkdir -p "$BACKUP_DIR"
    cp -r /home/samztekn/samztuneup/.env "$BACKUP_DIR/" 2>/dev/null || true
    cp -r /home/samztekn/samztuneup/storage/app/* "$BACKUP_DIR/storage/" 2>/dev/null || true
    print_status "Backup created at: $BACKUP_DIR"
}

update_from_git() {
    print_status "Pulling latest changes from GitHub..."

    cd /home/samztekn/samztuneup

    # Stash any local changes
    git stash push -m "Auto backup before update $(date)" 2>/dev/null || true

    # Pull latest changes
    git checkout $BRANCH
    git pull origin $BRANCH

    print_status "Code updated successfully"
}

install_dependencies() {
    print_status "Installing/updating dependencies..."

    # Install PHP dependencies
    if [ -f composer.json ]; then
        composer install --no-dev --optimize-autoloader
    fi

    # Install Node dependencies and build
    if [ -f package.json ]; then
        npm install
        npm run build
    fi
}

setup_laravel() {
    print_status "Setting up Laravel..."

    # Generate key if not exists
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        php artisan key:generate
    fi

    # Run migrations (safe)
    php artisan migrate --force

    # Clear and cache
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear

    # Cache for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
}

set_permissions() {
    print_status "Setting permissions..."

    # Storage permissions
    chmod -R 755 storage/
    chmod -R 755 bootstrap/cache/

    # Set ownership (adjust based on your web server)
    chown -R www-data:www-data ./
    # chown -R nginx:nginx ./  # If using Nginx
    # chown -R apache:apache ./  # If using Apache
}

restart_services() {
    print_status "Restarting web services..."

    # Restart PHP-FPM if needed
    systemctl restart php8.2-fpm 2>/dev/null || true
    systemctl restart php-fpm 2>/dev/null || true

    # Restart web server
    systemctl restart nginx 2>/dev/null || true
    systemctl restart apache2 2>/dev/null || true
}

test_deployment() {
    print_status "Testing deployment..."

    # Test Laravel
    if php artisan --version > /dev/null 2>&1; then
        print_status "Laravel is working"
    else
        print_error "Laravel test failed"
        exit 1
    fi

    # Test HTTP response
    if curl -s -o /dev/null -w "%{http_code}" http://localhost/samztuneup/ | grep -q "200\|301\|302"; then
        print_status "HTTP test passed"
    else
        print_warning "HTTP test failed - check web server configuration"
    fi
}

rollback() {
    print_warning "Rolling back to previous version..."
    if [ -d "$BACKUP_DIR" ]; then
        cp "$BACKUP_DIR/.env" /home/samztekn/samztuneup/ 2>/dev/null || true
        print_status "Rollback completed"
    else
        print_error "No backup found for rollback"
    fi
}

# Trap errors and rollback
trap 'print_error "Deployment failed! Rolling back..."; rollback; exit 1' ERR

# Main deployment process
main() {
    backup_current
    update_from_git
    install_dependencies
    setup_laravel
    set_permissions
    restart_services
    test_deployment

    print_status "🎉 Deployment completed successfully!"
    print_status "Backup location: $BACKUP_DIR"
    print_status "URL: https://samztekno.com/samztuneup"
}

# Run main function
main