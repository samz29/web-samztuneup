# SamzTune-Up 🔧

**SamzTune-Up** adalah aplikasi manajemen bengkel otomotif berbasis web yang dibangun dengan Laravel.

## 🚀 Deploy ke Hosting

Dokumentasi lengkap cara deploy tersedia di:

- **[DEPLOYMENT.md](DEPLOYMENT.md)** — Panduan lengkap deploy ke VPS/Cloud Server (Railway, DigitalOcean, dll.)
- **[DEPLOY_IDCLOUDHOST.md](DEPLOY_IDCLOUDHOST.md)** — Panduan khusus deploy ke IDCloudHost
- **[AUTO_DEPLOY.md](AUTO_DEPLOY.md)** — Setup auto-deployment dengan GitHub Webhook & Cron Job

### Quick Start

```bash
# 1. Clone repository
git clone https://github.com/samz29/web-samztuneup.git
cd web-samztuneup

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env, lalu jalankan migrasi
php artisan migrate --seed

# 5. Buat storage symlink
php artisan storage:link
```

### Auto-Deploy ke VPS

Jalankan script otomatis untuk deploy ke VPS Ubuntu/Debian:

```bash
# Download dan jalankan script (sebagai root)
wget https://raw.githubusercontent.com/samz29/web-samztuneup/main/auto-deploy.sh
chmod +x auto-deploy.sh
sudo ./auto-deploy.sh
```

Setelah deploy, update file `.env` dengan API keys production Anda (Tripay, Biteship, Google Maps, HERE Maps).

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
