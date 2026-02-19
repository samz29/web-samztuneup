#!/bin/bash

# SamzTune-Up Auto Update Script
# Usage: ./update.sh [branch]
# Default branch: main

set -e

APP_DIR="/var/www/samztune-up"
BRANCH=${1:-main}
BACKUP_DIR="/var/www/backups/$(date +%Y%m%d_%H%M%S)"

echo "🚀 Updating SamzTune-Up from GitHub ($BRANCH branch)"
echo "==================================================="

cd "$APP_DIR"

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

backup_database() {
    print_status "Backing up database..."
    if ! command -v mysqldump &> /dev/null; then
        print_warning "mysqldump command not found. Skipping database backup."
        return
    fi

    if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
        print_warning "DB credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD) not found in .env. Skipping database backup."
        return
    fi

    mkdir -p "$BACKUP_DIR/database"
    export MYSQL_PWD="$DB_PASSWORD"
    mysqldump -u "$DB_USERNAME" "$DB_DATABASE" | gzip > "$BACKUP_DIR/database/$DB_DATABASE-$(date +%Y%m%d_%H%M%S).sql.gz"
    unset MYSQL_PWD
    print_status "Database backup completed: $BACKUP_DIR/database/"
}

backup_current() {
    print_status "Backing up application files..."
    mkdir -p "$BACKUP_DIR"
    # Backup .env and public storage
    cp .env "$BACKUP_DIR/.env.bak"
    if [ -d "storage/app/public" ]; then
        mkdir -p "$BACKUP_DIR/storage"
        cp -r storage/app/public/* "$BACKUP_DIR/storage/" 2>/dev/null || true
    fi
    print_status "Files backed up to: $BACKUP_DIR"
}

update_from_git() {
    print_status "Pulling latest changes from GitHub..."

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
    if curl -s -I -o /dev/null -w "%{http_code}" https://samztekno.com/ | grep -q "200\|301\|302"; then
        print_status "HTTP test passed"
    else
        print_warning "HTTP test failed - check web server configuration"
    fi
}

# Run installer automatically when app is not installed yet
run_installer_if_needed() {
    # If sentinel file exists, assume installed
    if [ -f storage/framework/installed ]; then
        print_status "Application already installed (sentinel found)"
        return
    fi

    # Try to detect via migrations table: if no migrations, treat as not installed
    if php artisan migrate:status 2>&1 | grep -q "No migrations found"; then
        print_status "No migrations found — will run installer"
    else
        # If migrate:status succeeded and migrations exist, assume installed
        print_status "Migrations detected — skipping auto-installer"
        return
    fi

    # Determine admin credentials from .env or generate
    ADMIN_EMAIL=$(grep -E '^ADMIN_EMAIL=' .env 2>/dev/null | cut -d'=' -f2 | tr -d '"')
    ADMIN_PASSWORD=$(grep -E '^ADMIN_PASSWORD=' .env 2>/dev/null | cut -d'=' -f2 | tr -d '"')

    if [ -z "$ADMIN_EMAIL" ]; then
        ADMIN_EMAIL=admin@samztekno.com
    fi
    if [ -z "$ADMIN_PASSWORD" ]; then
        if command -v openssl >/dev/null 2>&1; then
            ADMIN_PASSWORD=$(openssl rand -base64 12)
        else
            ADMIN_PASSWORD="$(date +%s)$(head -c 6 /dev/urandom | sha1sum | cut -c1-6)"
        fi
        echo "Generated admin credentials: $ADMIN_EMAIL / $ADMIN_PASSWORD" >> "$BACKUP_DIR/install-admin.txt"
    fi

    print_status "Running non-interactive installer (admin: $ADMIN_EMAIL)"
    php artisan app:install --admin-email="$ADMIN_EMAIL" --admin-password="$ADMIN_PASSWORD" --force || print_error "Auto-installer failed"

    # Ensure sentinel file exists
    if [ ! -f storage/framework/installed ]; then
        echo "$(date -u) - installed by update.sh" > storage/framework/installed || true
    fi

    print_status "Auto-installer finished (credentials logged to $BACKUP_DIR/install-admin.txt if generated)"
}


rollback() {
    print_warning "Rolling back to previous version..."
    if [ -d "$BACKUP_DIR" ]; then
        cp "$BACKUP_DIR/.env.bak" "$APP_DIR/.env" 2>/dev/null || true
        print_status "Rollback completed"
    else
        print_error "No backup found for rollback"
    fi
}

# Trap errors and rollback
trap 'print_error "Deployment failed! Rolling back..."; rollback; exit 1' ERR

# Main deployment process
main() {
    # Load environment variables from .env file
    if [ -f .env ]; then
        export $(grep -v '^#' .env | xargs)
    fi

    backup_current
    backup_database
    update_from_git
    install_dependencies
    setup_laravel
    # Run installer automatically if application is not yet installed
    run_installer_if_needed
    set_permissions
    restart_services
    test_deployment

    print_status "🎉 Deployment completed successfully!"
    print_status "Backup location: $BACKUP_DIR"
    print_status "URL: https://samztekno.com"
}

# Run main function
main
