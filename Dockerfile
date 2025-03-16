FROM php:8.3-apache

# -----------------------------
# Priority 1: System Dependencies & PHP Extensions
# These layers rarely change so they will be cached
# -----------------------------
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

RUN a2enmod rewrite ssl


# -----------------------------
# Priority 3: Set Working Directory
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# Priority 4: Copy Dependency Files for Caching
# Copy composer files first so that if only app code changes later,
# this layer is cached.
# -----------------------------
COPY composer.json composer.lock ./



# -----------------------------
# Priority 5: Copy the Remaining Application Code
# -----------------------------
COPY . .

# -----------------------------
# Priority 6: Set File Permissions & Cache Directory
# -----------------------------
RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p bootstrap/cache && \
    chown -R www-data:www-data bootstrap/cache && \
    chmod -R 775 bootstrap/cache

# -----------------------------
# Priority 7: Apache Configuration & Expose Port
# -----------------------------
COPY apache.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 8080

# -----------------------------
# Priority 8: Copy and Set Entrypoint
# -----------------------------
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
