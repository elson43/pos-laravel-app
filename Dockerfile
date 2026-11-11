FROM php:8.4-cli

# Install ekstensi PHP & Node.js untuk memproses CSS/Desain
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependencies PHP & Node.js, lalu build desain
RUN composer install --optimize-autoloader --no-dev
RUN npm install
RUN npm run build

# Jalankan server
CMD php artisan serve --host=0.0.0.0 --port=$PORT