FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl zip libzip-dev libpng-dev libonig-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Copy .env.example to .env
RUN cp .env.example .env

# Install dependencies (for development, include dev dependencies)
RUN composer install --optimize-autoloader --no-interaction

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Expose port
EXPOSE 8000

# Generate key during build
CMD ["php","artisan","key:generate"]

# Generate key during build
CMD ["php","artisan","migrate"]

# Start Laravel development server
CMD ["php", "artisan", "serve",  "--host=0.0.0.0", "--port=8000"]
