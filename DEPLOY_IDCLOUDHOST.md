# 🚀 Panduan Deploy ke IDCloudHost

## 📋 Cek Jenis Hosting Anda

### **Cloud VPS** (Direkomendasikan)
Jika Anda punya Cloud VPS, ikuti panduan lengkap di bawah.

### **Cloud Hosting**
Jika shared hosting, gunakan cPanel untuk upload file.

---

## ⚡ Deploy ke Cloud VPS IDCloudHost

### 1. **Akses VPS via SSH**
```bash
# Connect ke VPS Anda
ssh root@IP_VPS_ANDA

# Atau jika pakai key
ssh -i private_key root@IP_VPS_ANDA
```

### 2. **Update Sistem**
```bash
# Update package list
apt update && apt upgrade -y

# Install essential tools
apt install -y curl wget git unzip software-properties-common
```

### 3. **Install PHP 8.2**
```bash
# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 dan ekstensi Laravel
apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap php8.2-readline
```

### 4. **Install Composer**
```bash
# Download dan install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 5. **Install Node.js & NPM**
```bash
# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs
```

### 6. **Install Nginx**
```bash
apt install -y nginx
systemctl enable nginx
systemctl start nginx
```

### 7. **Install MySQL**
```bash
apt install -y mysql-server
mysql_secure_installation

# Buat database dan user
mysql -u root -p
CREATE DATABASE samztune_up CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'samztune_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON samztune_up.* TO 'samztune_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 8. **Clone Repository**
```bash
# Buat direktori web
mkdir -p /var/www
cd /var/www

# Clone dari GitHub
git clone https://github.com/samz29/web-samztuneup.git samztune-up
cd samztune-up
```

### 9. **Setup Aplikasi**
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies dan build assets
npm install
npm run build

# Copy environment file
cp .env.production .env

# Generate application key
php artisan key:generate
```

### 10. **Konfigurasi Environment**
Edit file `.env`:
```bash
nano .env
```

Update dengan konfigurasi Anda:
```env
APP_URL=https://yourdomain.com
DB_DATABASE=samztune_up
DB_USERNAME=samztune_user
DB_PASSWORD=your_strong_password

# API Keys (ganti dengan production keys)
BITESHIP_API_KEY=your_production_biteship_key
TRIPAY_API_KEY=your_production_triipay_key
# dll...
```

### 11. **Setup Database**
```bash
# Run migrations
php artisan migrate --seed

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 12. **Setup Permissions**
```bash
# Set proper ownership
chown -R www-data:www-data /var/www/samztune-up
chmod -R 755 /var/www/samztune-up/storage
chmod -R 755 /var/www/samztune-up/bootstrap/cache
```

### 13. **Konfigurasi Nginx**
```bash
# Buat konfigurasi site
nano /etc/nginx/sites-available/samztune-up
```

Isi dengan:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
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
```

Enable site:
```bash
ln -s /etc/nginx/sites-available/samztune-up /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

### 14. **Setup SSL dengan Let's Encrypt**
```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Generate SSL certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 15. **Setup Firewall**
```bash
# Install UFW
apt install -y ufw
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable
```

### 16. **Test Deployment**
```bash
# Test aplikasi
curl -I https://yourdomain.com

# Check Laravel logs jika ada error
tail -f /var/www/samztune-up/storage/logs/laravel.log
```

---

## 🔧 Troubleshooting IDCloudHost

### **Memory Limit PHP**
Jika dapat error memory:
```bash
# Edit php.ini
nano /etc/php/8.2/fpm/php.ini
# Cari memory_limit dan ubah ke 256M atau 512M
memory_limit = 512M

# Restart PHP-FPM
systemctl restart php8.2-fpm
```

### **Permission Issues**
```bash
# Fix storage permissions
chown -R www-data:www-data /var/www/samztune-up/storage
chmod -R 775 /var/www/samztune-up/storage
```

### **Database Connection**
Pastikan MySQL service running:
```bash
systemctl status mysql
systemctl start mysql
```

---

## 📞 Support IDCloudHost

Jika ada masalah dengan server IDCloudHost:
- **Panel Control**: Login ke panel.idcloudhost.com
- **Support Ticket**: Buat ticket di panel
- **Live Chat**: Tersedia 24/7

---

## ✅ Checklist Deploy

- [ ] ✅ VPS aktif dan dapat diakses SSH
- [ ] ✅ PHP 8.2+ terinstall
- [ ] ✅ Composer terinstall
- [ ] ✅ Node.js terinstall
- [ ] ✅ Nginx terinstall
- [ ] ✅ MySQL terinstall dan database dibuat
- [ ] ✅ Repository diclone
- [ ] ✅ Dependencies terinstall
- [ ] ✅ Environment file dikonfigurasi
- [ ] ✅ Database migrated
- [ ] ✅ Permissions diset
- [ ] ✅ Nginx dikonfigurasi
- [ ] ✅ SSL certificate terinstall
- [ ] ✅ Firewall diset
- [ ] ✅ Aplikasi dapat diakses

**Total waktu deploy**: ~30-45 menit