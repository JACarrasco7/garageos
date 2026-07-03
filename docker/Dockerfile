FROM dunglas/frankenphp:php8.4-zts-alpine

WORKDIR /app

# Install extensions
RUN install-php-extensions opcache redis pcntl intl pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build assets
RUN npm ci && npm run build

# Cache config
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 8000

CMD ["frankenphp", "php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
