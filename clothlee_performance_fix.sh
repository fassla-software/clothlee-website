#!/bin/bash
echo "🚀 clothlee.com Performance Optimization Script"
echo "Time: $(date)"
echo "Directory: $(pwd)"

# Laravel optimizations
echo "Step 1: Laravel Cache Optimization..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Composer optimization
echo "Step 2: Composer Optimization..."
composer dump-autoload --optimize --classmap-authoritative

# Asset optimization
echo "Step 3: Asset Optimization..."
if [ -f "package.json" ]; then
    npm run production
fi

# Environment optimization
echo "Step 4: Environment Optimization..."
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' .env
sed -i 's/APP_ENV=local/APP_ENV=production/g' .env

# Image optimization (if imagemagick is available)
echo "Step 5: Image Optimization..."
if command -v convert &> /dev/null; then
    find public -name "*.jpg" -exec convert {} -strip -interlace Plane -quality 85% {} \;
    find public -name "*.png" -exec convert {} -strip {} \;
    echo "✅ Images optimized"
else
    echo "⚠️  ImageMagick not found, skipping image optimization"
fi

# Create performance .htaccess
echo "Step 6: Creating Performance .htaccess..."
cat >> public/.htaccess << 'HTACCESS'

# Performance Optimization for clothlee.com
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript
    AddOutputFilterByType DEFLATE application/xml application/xhtml+xml application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript application/x-javascript
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    Header append Vary: Accept-Encoding
    Header set Cache-Control "max-age=31536000, public"
</IfModule>
HTACCESS

echo "✅ Performance optimization complete!"
echo "🌐 Test your site: https://clothlee.com"
echo "📊 Test speed: https://pagespeed.web.dev/analysis/https-clothlee-com/"
echo "⏱️  Wait 2-5 minutes for changes to take effect, then test again"
