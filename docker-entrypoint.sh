#!/bin/bash
set -e

echo "🚀 Memulai Deployment..."

# Install dependencies jika belum (Safety net)
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev
fi

# Setup Aplikasi
echo "🔗 Linking Storage..."
php artisan storage:link || true

echo "🧹 Clearing Cache..."
php artisan optimize:clear

# Nyalakan Server
echo "🔥 Starting Server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
