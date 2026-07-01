#!/bin/bash

# Hentikan proses jika ada error
set -e

DEPLOY_PATH="/home/clingdental/web/kasir.clingdental.com/public_html"

echo "📂 Masuk ke direktori deploy..."
cd $DEPLOY_PATH

echo "⬇️  Pull kode terbaru dari GitHub..."
git pull origin main

echo "🔧 Install Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "🗃️  Jalankan migrasi database..."
php artisan migrate --force

echo "🌱 Jalankan seeder (opsional, hapus kalau tidak perlu)..."
# php artisan db:seed --force

echo "⚙️  Clear dan cache konfigurasi..."
php artisan config:clear
php artisan config:cache

echo "🛣️  Cache routes..."
php artisan route:clear
php artisan route:cache

echo "🖼️  Cache views..."
php artisan view:clear
php artisan view:cache

echo "🔗 Buat symlink storage..."
php artisan storage:link 2>/dev/null || true

echo "🔄 Restart queue worker (aktifkan kalau pakai queue)..."
# php artisan queue:restart

echo "🔑 Set permission folder storage & cache..."
chmod -R 775 storage bootstrap/cache

echo "✅ Deploy berhasil selesai!"
