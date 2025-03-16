FROM europe-west1-docker.pkg.dev/laviola-450518/laviola-base/php-8.3-apache-deps:latest



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
