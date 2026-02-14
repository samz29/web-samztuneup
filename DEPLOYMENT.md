# Panduan Deploy Laravel SamzTune-Up

## 📋 Daftar Hosting yang Direkomendasikan

### 1. **Railway** (Paling Mudah - Gratis untuk Starter)

Railway.app - Platform as a Service modern

**Keuntungan:**

- Deploy langsung dari GitHub
- Database PostgreSQL/MySQL otomatis
- SSL gratis
- Auto-scaling

**Cara Deploy:**

1. Buat akun di railway.app
2. Connect GitHub repository
3. Railway otomatis detect Laravel dan setup
4. Database akan dibuat otomatis

### 2. **DigitalOcean App Platform**

App Platform - Managed hosting

**Keuntungan:**

- $5/bulan untuk starter
- Auto-scaling
- Built-in database
- Global CDN

### 3. **VPS/Cloud Server** (Lebih Kontrol)

- DigitalOcean Droplet ($6/bulan)
- AWS EC2 Lightsail
- Google Cloud Compute Engine

### 4. **Shared Hosting** (Termurah)

- Niagahoster, Rumahweb, dll.
- cPanel hosting

---

## 🚀 Langkah Deploy ke VPS/Cloud Server

### Persiapan Server

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2+ dan ekstensi
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### Upload dan Setup Aplikasi

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/samz29/web-samztuneup.git samztune-up
cd samztune-up

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Setup environment
cp .env.production .env
# Edit .env dengan konfigurasi database dan API keys

# Generate app key
php artisan key:generate

# Setup database
mysql -u root -p
CREATE DATABASE samztune_up;
GRANT ALL PRIVILEGES ON samztune_up.* TO 'samztune_user'@'localhost' IDENTIFIED BY 'your_password';
FLUSH PRIVILEGES;
EXIT;

# Run migrations dan seeders
php artisan migrate --seed

# Setup permissions
sudo chown -R www-data:www-data /var/www/samztune-up
sudo chmod -R 755 /var/www/samztune-up/storage
sudo chmod -R 755 /var/www/samztune-up/bootstrap/cache
```

### Konfigurasi Nginx

```bash
# Buat konfigurasi site
sudo nano /etc/nginx/sites-available/samztune-up

# Isi file:
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/samztune-up/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}

# Enable site
sudo ln -s /etc/nginx/sites-available/samztune-up /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Setup SSL (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## 🔧 Konfigurasi Production

### Environment Variables

Edit `.env` file di server dengan:

- Database credentials
- API keys (Google Maps, HERE Maps, Tripay, Biteship)
- APP_URL dengan domain Anda
- APP_ENV=production
- APP_DEBUG=false

### Cron Job untuk Scheduler (Opsional)

```bash
# Setup Laravel scheduler
crontab -e
# Tambahkan: * * * * * cd /var/www/samztune-up && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (Jika menggunakan queue)

```bash
# Setup supervisor untuk queue
sudo apt install supervisor -y
# Konfigurasi supervisor untuk Laravel queue worker
```

---

## 🧪 Testing Deploy

1. **Test Basic Functionality**

    ```bash
    curl -I https://yourdomain.com
    ```

2. **Test Database Connection**

    ```bash
    php artisan tinker
    # Test: DB::connection()->getPdo();
    ```

3. **Test API Endpoints**
    - Test Tripay integration
    - Test Biteship (jika sudah diupgrade)

---

## 🚨 Troubleshooting

### Common Issues:

1. **500 Error**: Check `.env` file dan permissions
2. **Database Connection Failed**: Verify DB credentials
3. **Assets Not Loading**: Run `npm run build` dan check public/build
4. **Permission Denied**: Fix storage/ dan bootstrap/cache permissions

### Logs Location:

- Laravel logs: `storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/`
- PHP logs: `/var/log/php8.2-fpm.log`

---

## 💰 Estimasi Biaya

- **Railway**: Gratis - $10/bulan
- **DigitalOcean App Platform**: $5/bulan
- **VPS Basic**: $5-10/bulan
- **Shared Hosting**: $3-5/bulan

Pilih sesuai kebutuhan dan budget Anda!
