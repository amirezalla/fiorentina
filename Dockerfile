FROM europe-west1-docker.pkg.dev/laviola-450518/laviola-base/php-8.3-apache-deps:latest

# -----------------------------
# Add supercronic (cron runner without PAM)
# -----------------------------
ARG SUPERCRONIC_VERSION=v0.2.29
ARG SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/${SUPERCRONIC_VERSION}/supercronic-linux-amd64
# (Optional) Add checksum verification by setting SUPERCRONIC_SHA256 and checking it.
RUN set -eux; \
    apt-get update && apt-get install -y --no-install-recommends ca-certificates curl; \
    curl -fsSL "$SUPERCRONIC_URL" -o /usr/local/bin/supercronic; \
    chmod +x /usr/local/bin/supercronic; \
    apt-get purge -y --auto-remove curl; \
    rm -rf /var/lib/apt/lists/*

# -----------------------------
# Set Working Directory
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# Copy Dependency Files (cache-friendly)
# -----------------------------
COPY composer.json composer.lock ./

# -----------------------------
# Copy Application Code
# -----------------------------
COPY . .

# -----------------------------
# Permissions & Cache Dir
# -----------------------------
RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p bootstrap/cache && \
    chown -R www-data:www-data bootstrap/cache && \
    chmod -R 775 bootstrap/cache

# -----------------------------
# Apache Configuration & Expose
# -----------------------------
COPY apache.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 8080

# -----------------------------
# Entrypoint
# -----------------------------
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
