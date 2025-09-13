# syntax=docker/dockerfile:1

FROM europe-west1-docker.pkg.dev/laviola-450518/laviola-base/php-8.3-apache-deps:latest

# -----------------------------
# Add supercronic (cron runner without PAM)
# -----------------------------
ARG SUPERCRONIC_VERSION=v0.2.29
ARG SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/${SUPERCRONIC_VERSION}/supercronic-linux-amd64
RUN set -eux; \
    apt-get update && apt-get install -y --no-install-recommends ca-certificates curl; \
    curl -fsSL "$SUPERCRONIC_URL" -o /usr/local/bin/supercronic; \
    chmod +x /usr/local/bin/supercronic; \
    apt-get purge -y --auto-remove curl; \
    rm -rf /var/lib/apt/lists/*

# -----------------------------
# Working dir
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# Copy app (keep your flow)
# -----------------------------
COPY composer.json composer.lock ./
COPY . .

# -----------------------------
# Minimal build-time perms (ok even if runtime is read-only)
# -----------------------------
RUN mkdir -p bootstrap/cache \
    && chown -R www-data:www-data bootstrap/cache \
    && chmod -R 775 bootstrap/cache

# -----------------------------
# Runtime ENV for Cloud Run (read-only FS)
# - Logs to STDERR (no storage/logs writes)
# - Purifier cache to /tmp/purifier
# - Compiled views to /tmp/view
# - Avoid disk-backed cache/session
# -----------------------------
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    VIEW_COMPILED_PATH=/tmp/view \
    PURIFIER_CACHE_PATH=/tmp/purifier \
    SESSION_DRIVER=cookie \
    CACHE_DRIVER=array \
    QUEUE_CONNECTION=database

# -----------------------------
# Apache & Entrypoint
# -----------------------------
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
