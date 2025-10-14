# ============================================
# .env.example
# ============================================

APP_NAME=""
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourapp.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=email_sync
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Queue
QUEUE_CONNECTION=redis
REDIS_QUEUE=default

# Microsoft Graph API
MICROSOFT_CLIENT_ID=your_client_id_here
MICROSOFT_CLIENT_SECRET=your_client_secret_here
MICROSOFT_REDIRECT_URI=https://yourapp.com/auth/microsoft/callback
MICROSOFT_TENANT_ID=common
MICROSOFT_AUTHORITY=https://login.microsoftonline.com/common

# Webhook Configuration
WEBHOOK_URL=https://yourapp.com/api/webhooks/microsoft
WEBHOOK_CLIENT_STATE=your_random_secret_string_min_32_chars

# Email Sync Configuration
SYNC_LOCK_TIMEOUT=300
MAX_EMAILS_PER_SYNC=1000
SUBSCRIPTION_RENEWAL_DAYS=2
RETRY_FAILED_AFTER_HOURS=1
MAX_CONSECUTIVE_FAILURES=5
DELTA_TOKEN_TTL_DAYS=30
CLEANUP_OLD_EMAILS_DAYS=90

# Storage
FILESYSTEM_DISK=local
ATTACHMENT_STORAGE_DISK=attachments
ATTACHMENT_STORAGE_DRIVER=local
MAX_ATTACHMENT_SIZE_MB=25

# AWS S3 (if using)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_ATTACHMENTS_BUCKET=

# Monitoring & Alerts
METRICS_ENABLED=true
ALERT_CONSECUTIVE_FAILURES=3
ALERT_STALE_SYNC_HOURS=24
LOG_SLACK_WEBHOOK_URL=

# Rate Limiting
WEBHOOK_MAX_AGE=5
WEBHOOK_PROCESS_DELAY=5

# ============================================
# Deployment Script (deploy.sh)
# ============================================

#!/bin/bash

set -e

echo "🚀 Starting deployment..."

# Pull latest code
echo "📦 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📚 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️  Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Clear application cache
echo "🧹 Clearing cache..."
php artisan cache:clear

# Optimize application
echo "⚡ Optimizing application..."
php artisan optimize

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment completed successfully!"

# ============================================
# Supervisor Configuration (email-sync-worker.conf)
# ============================================

[program:email-sync-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --queue=email-sync,default --sleep=3 --tries=3 --max-time=3600 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600

[program:subscription-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --queue=subscriptions --sleep=3 --tries=1 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/subscription-worker.log
stopwaitsecs=3600

[program:attachment-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --queue=attachments --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/attachment-worker.log
stopwaitsecs=3600

[program:laravel-scheduler]
process_name=%(program_name)s
command=/bin/bash -c "while [ true ]; do (php /var/www/html/artisan schedule:run --verbose --no-interaction &); sleep 60; done"
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/scheduler.log

# ============================================
# Nginx Configuration (nginx.conf)
# ============================================

server {
    listen 80;
    listen [::]:80;
    server_name yourapp.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourapp.com;
    root /var/www/html/public;

    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/yourapp.crt;
    ssl_certificate_key /etc/ssl/private/yourapp.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Logging
    access_log /var/log/nginx/yourapp-access.log;
    error_log /var/log/nginx/yourapp-error.log;

    # Rate limiting for webhook endpoint
    location /api/webhooks/ {
        limit_req zone=webhook burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # API rate limiting
    location /api/ {
        limit_req zone=api burst=10 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 50M;
}

# Rate limit zones
http {
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req_zone $binary_remote_addr zone=webhook:10m rate=100r/m;
}

# ============================================
# Docker Compose (docker-compose.yml)
# ============================================

version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: email-sync-app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - ./storage/attachments:/var/www/html/storage/attachments
    networks:
      - app-network
    environment:
      - APP_ENV=${APP_ENV}
      - APP_KEY=${APP_KEY}
      - DB_HOST=mysql
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis

  nginx:
    image: nginx:alpine
    container_name: email-sync-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx:/etc/nginx/conf.d
      - ./docker/ssl:/etc/ssl
    networks:
      - app-network
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    container_name: email-sync-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - app-network
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    container_name: email-sync-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass "${REDIS_PASSWORD}"
    volumes:
      - redis-data:/data
    networks:
      - app-network
    ports:
      - "6379:6379"

  queue-worker:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: email-sync-worker
    restart: unless-stopped
    working_dir: /var/www/html
    command: php artisan queue:work redis --queue=email-sync,default --sleep=3 --tries=3 --timeout=300
    volumes:
      - ./:/var/www/html
    networks:
      - app-network
    depends_on:
      - mysql
      - redis

  scheduler:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: email-sync-scheduler
    restart: unless-stopped
    working_dir: /var/www/html
    command: /bin/bash -c "while [ true ]; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
    volumes:
      - ./:/var/www/html
    networks:
      - app-network
    depends_on:
      - mysql
      - redis

networks:
  app-network:
    driver: bridge

volumes:
  mysql-data:
    driver: local
  redis-data:
    driver: local

# ============================================
# Dockerfile
# ============================================

FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]

# ============================================
# GitHub Actions CI/CD (.github/workflows/deploy.yml)
# ============================================

name: Deploy to Production

on:
  push:
    branches: [ main ]
  workflow_dispatch:

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, json, pdo, mysql, redis
          coverage: none

      - name: Copy .env
        run: php -r "file_exists('.env') || copy('.env.testing', '.env');"

      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader

      - name: Generate key
        run: php artisan key:generate

      - name: Run migrations
        run: php artisan migrate --force
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: password

      - name: Run tests
        run: php artisan test
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: password

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
      - uses: actions/checkout@v3

      - name: Deploy to Production
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/html
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            sudo supervisorctl restart all

      - name: Slack Notification
        uses: 8398a7/action-slack@v3
        if: always()
        with:
          status: ${{ job.status }}
          text: 'Deployment to production: ${{ job.status }}'
          webhook_url: ${{ secrets.SLACK_WEBHOOK }}

# ============================================
# Health Check Endpoint (routes/web.php)
# ============================================

Route::get('/health', function () {
    $checks = [
        'database' => false,
        'redis' => false,
        'queue' => false,
        'storage' => false,
    ];

    // Check database
    try {
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (\Exception $e) {
        Log::error('Health check - Database failed: ' . $e->getMessage());
    }

    // Check Redis
    try {
        Redis::ping();
        $checks['redis'] = true;
    } catch (\Exception $e) {
        Log::error('Health check - Redis failed: ' . $e->getMessage());
    }

    // Check queue
    try {
        $size = Redis::llen('queues:default');
        $checks['queue'] = is_numeric($size);
    } catch (\Exception $e) {
        Log::error('Health check - Queue failed: ' . $e->getMessage());
    }

    // Check storage
    try {
        Storage::disk('local')->exists('test');
        $checks['storage'] = true;
    } catch (\Exception $e) {
        Log::error('Health check - Storage failed: ' . $e->getMessage());
    }

    $allHealthy = !in_array(false, $checks, true);

    return response()->json([
        'status' => $allHealthy ? 'healthy' : 'unhealthy',
        'timestamp' => now()->toIso8601String(),
        'checks' => $checks,
        'version' => config('app.version', '1.0.0')
    ], $allHealthy ? 200 : 503);
});
