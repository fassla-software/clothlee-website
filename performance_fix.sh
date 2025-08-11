#!/bin/bash
echo "🚀 Starting Performance Fix for clothlee.com"
echo "Time: $(date)"
echo "User: $(whoami)"
echo "Directory: $(pwd)"

# Clear all caches first
echo "Step 1: Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize Laravel
echo "Step 2: Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize Composer
echo "Step 3: Optimizing Composer..."
composer dump-autoload --optimize --classmap-authoritative

# Build production assets
echo "Step 4: Building assets..."
if [ -f "package.json" ]; then
    npm run production
else
    echo "No package.json found, skipping npm"
fi

# Update .env for production
echo "Step 5: Setting production environment..."
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' .env
sed -i 's/APP_ENV=local/APP_ENV=production/g' .env

# Create optimized .htaccess
echo "Step 6: Creating optimized .htaccess..."
cat >> public/.htaccess << 'HTACCESS'

# Performance Optimizations
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
</IfModule>
HTACCESS

echo "✅ Performance optimization complete!"
echo "🌐 Test your site: https://www.clothlee.com"
echo "📊 Check speed: https://pagespeed.web.dev/"
