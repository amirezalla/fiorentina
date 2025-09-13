#!/usr/bin/env bash
set -euo pipefail

# --- PHP CLI ---
PHP_BIN="$(command -v php || true)"
if [[ -z "${PHP_BIN}" ]]; then
  for p in /usr/local/bin/php /usr/bin/php /opt/bitnami/php/bin/php /opt/php*/bin/php; do
    [[ -x "$p" ]] && PHP_BIN="$p" && break
  done
fi
if [[ -z "${PHP_BIN}" ]]; then
  echo "ERROR: PHP CLI not found." >&2
  exit 1
fi
echo "Using PHP at: ${PHP_BIN}"

# --- Runtime writable dirs in /tmp ---
mkdir -p /tmp/purifier /tmp/view /tmp/laravel-logs
chmod 777 /tmp/purifier /tmp/view /tmp/laravel-logs

# Point storage/logs at /tmp (safe even if root FS is read-only)
if [[ ! -L /var/www/html/storage/logs ]]; then
  rm -rf /var/www/html/storage/logs 2>/dev/null || true
  ln -s /tmp/laravel-logs /var/www/html/storage/logs 2>/dev/null || true
fi

# --- Don’t rely on cached config/route (file logger may be baked in) ---
# If the files exist and FS allows, remove them; otherwise continue.
rm -f /var/www/html/bootstrap/cache/config.php 2>/dev/null || true
rm -f /var/www/html/bootstrap/cache/routes-*.php 2>/dev/null || true

# Ask any workers to gracefully restart (safe if none)
${PHP_BIN} artisan queue:restart || true

# Clear compiled views (they’ll go to /tmp/view)
${PHP_BIN} artisan view:clear || true

# Avoid config:cache/route:cache here (write to bootstrap/cache). If you really need them,
# do it at build-time only, not at runtime in Cloud Run.

# --- Supercronic schedule ---
cat >/etc/cron.app <<'EOF'
# Run Laravel scheduler every minute
* * * * * cd /var/www/html && php artisan schedule:run

# Drain the 'imports' queue every minute; no overlap
* * * * * flock -n /tmp/queue-imports.lock -c 'cd /var/www/html && php artisan queue:work --queue=imports --stop-when-empty --sleep=1 --tries=1 --timeout=900 --max-time=840'
EOF

if command -v supercronic >/dev/null 2>&1; then
  /usr/local/bin/supercronic -json /etc/cron.app &
else
  echo "ERROR: supercronic not found." >&2
  exit 1
fi

# --- Apache for Cloud Run ---
: "${PORT:=8080}"
echo "Updating Apache to listen on port ${PORT}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
