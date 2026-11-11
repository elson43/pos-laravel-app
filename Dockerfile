FROM php:8.4-cli

# Install ekstensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy semua file ke dalam sistem Render
WORKDIR /app
COPY . .

# Install dependencies Laravel
RUN composer install --optimize-autoloader --no-dev

# Jalankan server
CMD php artisan serve --host=0.0.0.0 --port=$PORT