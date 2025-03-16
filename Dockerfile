FROM php:8.3-apache

# ----------- Priority 1: Install System Dependencies and PHP Extensions -----------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache modules (rewrite and SSL)
RUN a2enmod rewrite ssl

# ----------- Priority 2: Install Composer (from official composer image) -----------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----------- Priority 3: Set the Working Directory -----------
WORKDIR /var/www/html

# ----------- Priority 4: Copy Composer Files First for Caching -----------
# This allows Docker to cache dependency layers if composer.json/lock (and vendor) haven't changed.
COPY composer.json composer.lock ./
# Since you already have vendor/ in your repo, copy it here.
COPY vendor/ vendor/

# (Optional) Run composer install if you want to enforce autoload optimization.
# If vendor/ is already up-to-date, this step should be fast.
RUN composer install --no-dev --optimize-autoloader || true

# ----------- Priority 5: Copy the Remaining Application Files -----------
# Copy all files (including node_modules if needed)
COPY . .

# ----------- Priority 6: Set Permissions -----------
RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p bootstrap/cache && \
    chown -R www-data:www-data bootstrap/cache && \
    chmod -R 775 bootstrap/cache

# ----------- Priority 7: Configure Apache -----------
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# ----------- Priority 8: Expose Port 8080 -----------
EXPOSE 8080

# ----------- Priority 9: Add Entrypoint Script -----------
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# ----------- Priority 10: Set Entrypoint -----------
ENTRYPOINT ["entrypoint.sh"]
