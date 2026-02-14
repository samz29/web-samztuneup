# 🚀 **AUTO-DEPLOYMENT SETUP GUIDE**

## 📋 **Opsi Auto-Deployment**

### **1. Manual Auto-Update (Recommended)**
Script `update.sh` untuk update manual dari GitHub.

### **2. GitHub Webhook Auto-Deploy**
Deploy otomatis setiap push ke branch main.

### **3. Cron Job Auto-Update**
Update otomatis setiap jam/jam tertentu.

---

## ⚡ **Setup Manual Auto-Update**

### **Upload Script ke Server**
```bash
# Di server Anda
cd /home/samztekn/samztuneup

# Download script dari GitHub
wget https://raw.githubusercontent.com/samz29/web-samztuneup/main/update.sh
chmod +x update.sh
```

### **Jalankan Update**
```bash
# Update ke branch main (default)
./update.sh

# Update ke branch tertentu
./update.sh development

# Update dengan backup otomatis
./update.sh main  # Auto backup sebelum update
```

### **Apa yang dilakukan script:**
- ✅ Backup `.env` dan storage files
- ✅ Pull latest code dari GitHub
- ✅ Install/update dependencies
- ✅ Run migrations
- ✅ Cache configuration
- ✅ Set permissions
- ✅ Restart services
- ✅ Test deployment

---

## 🔄 **Setup GitHub Webhook Auto-Deploy**

### **1. Setup Webhook Endpoint**
```bash
# Buat direktori webhook
mkdir -p /var/www/webhooks
cd /var/www/webhooks

# Download webhook handler
wget https://raw.githubusercontent.com/samz29/web-samztuneup/main/webhook-handler.sh
chmod +x webhook-handler.sh

# Edit secret di file
nano webhook-handler.sh
# Ganti: SECRET="your_webhook_secret_here"
```

### **2. Setup Nginx untuk Webhook**
Tambahkan ke `/etc/nginx/sites-available/default`:
```nginx
location /webhook {
    alias /var/www/webhooks/webhook-handler.sh;
    include fastcgi_params;
    fastcgi_pass unix:/var/run/php-fpm/www.sock;
}
```

### **3. Setup GitHub Webhook**
1. **Buka Repository Settings** → **Webhooks** → **Add webhook**
2. **Payload URL**: `https://samztekno.com/webhook`
3. **Content type**: `application/json`
4. **Secret**: Masukkan secret yang sama di script
5. **Events**: Pilih "Just the push event"
6. **Branch**: `main`

### **4. Test Webhook**
```bash
# Test manual webhook
curl -X POST https://samztekno.com/webhook \
  -H "Content-Type: application/json" \
  -d '{"ref": "refs/heads/main"}'
```

---

## ⏰ **Setup Cron Job Auto-Update**

### **Edit Crontab**
```bash
crontab -e
```

### **Tambahkan Schedule**
```bash
# Update setiap jam pada jam kerja (9-17)
0 9-17 * * 1-5 /home/samztekn/samztuneup/update.sh main

# Update setiap 6 jam
0 */6 * * * /home/samztekn/samztuneup/update.sh main

# Update setiap hari jam 2 pagi
0 2 * * * /home/samztekn/samztuneup/update.sh main
```

---

## 🔧 **Konfigurasi Production**

### **Environment Variables**
Pastikan `.env` production sudah benar:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://samztekno.com/samztuneup

# Database
DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

# API Keys Production
BITESHIP_API_KEY=your_prod_biteship_key
TRIPAY_API_KEY=your_prod_triipay_key
```

### **Permissions**
```bash
# Set ownership
chown -R www-data:www-data /home/samztekn/samztuneup

# Make scripts executable
chmod +x /home/samztekn/samztuneup/update.sh
chmod +x /var/www/webhooks/webhook-handler.sh
```

---

## 📊 **Monitoring & Logs**

### **Cek Status Update**
```bash
# Cek log webhook
tail -f /home/samztekn/webhook.log

# Cek Laravel logs
tail -f /home/samztekn/samztuneup/storage/logs/laravel.log

# Cek cron logs
grep "update.sh" /var/log/syslog
```

### **Backup Locations**
- Auto backup: `/home/samztekn/backups/YYYYMMDD_HHMMSS/`
- Manual backup: Buat sendiri sebelum update besar

---

## 🚨 **Troubleshooting**

### **Update Gagal**
```bash
# Cek log detail
/home/samztekn/samztuneup/update.sh main 2>&1 | tee update.log

# Rollback jika perlu
cp /home/samztekn/backups/latest_backup/.env /home/samztekn/samztuneup/
```

### **Webhook Tidak Trigger**
```bash
# Test webhook endpoint
curl -X POST https://samztekno.com/webhook \
  -H "Content-Type: application/json" \
  -d '{"ref": "refs/heads/main"}'

# Cek nginx error
tail -f /var/log/nginx/error.log
```

### **Permission Issues**
```bash
# Fix permissions
chown -R www-data:www-data /home/samztekn/samztuneup
chmod -R 755 /home/samztekn/samztuneup/storage
```

---

## 📈 **Workflow Deployment**

### **Development → Production**
1. **Push ke GitHub** → Auto-deploy via webhook
2. **Test staging** → Manual update script
3. **Deploy production** → Cron job atau manual

### **Emergency Rollback**
```bash
# Quick rollback
cd /home/samztekn/samztuneup
git checkout HEAD~1  # Rollback 1 commit
./update.sh main     # Re-run setup
```

---

## 🎯 **Rekomendasi Setup**

**Untuk Production:**
1. **Webhook** untuk auto-deploy setiap push
2. **Cron job** sebagai fallback setiap 6 jam
3. **Manual script** untuk control penuh

**Keamanan:**
- ✅ Gunakan webhook secret
- ✅ Backup otomatis sebelum update
- ✅ Test di staging dulu
- ✅ Monitor logs regularly

---

**Siap setup auto-deployment?** Pilih opsi mana yang ingin Anda gunakan! 🚀

**Scripts tersedia di repository:**
- `update.sh` - Auto update script
- `webhook-handler.sh` - GitHub webhook handler
- `auto-deploy.sh` - Full deployment script