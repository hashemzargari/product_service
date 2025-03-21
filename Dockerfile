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
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . .

# Install dependencies
RUN composer install

# Create startup script
RUN echo '#!/bin/sh\n\
    mkdir -p /var/www/logs\n\
    mkdir -p /var/www/var/cache\n\
    mkdir -p /var/www/var/cache/routes\n\
    chown -R www-data:www-data /var/www/logs\n\
    chown -R www-data:www-data /var/www/var\n\
    chmod -R 775 /var/www/logs\n\
    chmod -R 775 /var/www/var\n\
    touch /var/www/var/cache/routes/routes.php\n\
    chown www-data:www-data /var/www/var/cache/routes/routes.php\n\
    chmod 664 /var/www/var/cache/routes/routes.php\n\
    php-fpm' > /usr/local/bin/docker-php-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-php-entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM with our entrypoint script
CMD ["docker-php-entrypoint.sh"]