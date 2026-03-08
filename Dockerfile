FROM php:8.4-fpm

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Ekstensi PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Jalankan instalasi dependensi
RUN composer install --no-interaction --optimize-autoloader

# Beri izin folder storage
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000