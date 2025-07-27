# Multi-stage build for optimized production image
FROM php:8.2-fpm AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions in one layer
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libgd-dev \
    libicu-dev \
    libxslt-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    zip \
    unzip \
    supervisor \
    nginx \
    cron \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        xml \
        curl \
        fileinfo \
        xsl \
        soap \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Create application user
RUN groupadd --force -g 1000 www \
    && useradd -ms /bin/bash --no-user-group -g 1000 -u 1000 www

# Copy PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/99-botble.ini

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Update nginx config for correct document root
RUN sed -i 's|root /app/public;|root /var/www/html/public;|g' /etc/nginx/nginx.conf

# Create necessary directories
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /var/run \
    && mkdir -p /var/log/nginx

# Copy application files
COPY . /var/www/html

# Install PHP dependencies as root first (to avoid permission issues)
RUN cd /var/www/html && \
    composer diagnose && \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set proper ownership after installation
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Switch to www user for Node.js operations
USER www

# Install Node dependencies and build assets
RUN cd /var/www/html && \
    npm ci --only=production \
    && npm run production \
    && rm -rf node_modules

# Switch back to root for final setup
USER root

# Create supervisor configuration
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf \
    && echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'logfile=/var/log/supervisor/supervisord.log' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'pidfile=/var/run/supervisord.pid' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '[program:nginx]' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile=/var/log/supervisor/nginx.err.log' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile=/var/log/supervisor/nginx.out.log' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'command=php-fpm -F' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile=/var/log/supervisor/php-fpm.err.log' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile=/var/log/supervisor/php-fpm.out.log' >> /etc/supervisor/conf.d/supervisord.conf

# Set up Laravel scheduler cron
RUN echo '* * * * * www cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1' > /etc/cron.d/laravel-scheduler \
    && chmod 0644 /etc/cron.d/laravel-scheduler \
    && crontab /etc/cron.d/laravel-scheduler

# Create startup script
RUN echo '#!/bin/bash' > /usr/local/bin/start.sh \
    && echo 'set -e' >> /usr/local/bin/start.sh \
    && echo '' >> /usr/local/bin/start.sh \
    && echo '# Wait for database to be ready' >> /usr/local/bin/start.sh \
    && echo 'echo "Waiting for database..."' >> /usr/local/bin/start.sh \
    && echo 'sleep 10' >> /usr/local/bin/start.sh \
    && echo '' >> /usr/local/bin/start.sh \
    && echo '# Run Laravel optimizations' >> /usr/local/bin/start.sh \
    && echo 'cd /var/www/html' >> /usr/local/bin/start.sh \
    && echo 'php artisan config:cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan route:cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan view:cache' >> /usr/local/bin/start.sh \
    && echo '' >> /usr/local/bin/start.sh \
    && echo '# Start cron service' >> /usr/local/bin/start.sh \
    && echo 'service cron start' >> /usr/local/bin/start.sh \
    && echo '' >> /usr/local/bin/start.sh \
    && echo '# Start supervisor' >> /usr/local/bin/start.sh \
    && echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# The nginx.conf already has the correct fastcgi_pass configuration

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Start the application
CMD ["/usr/local/bin/start.sh"]
