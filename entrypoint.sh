#!/usr/bin/env bash
set -euo pipefail

# --- Locate PHP CLI (absolute path avoids PATH issues) ---
PHP_BIN="$(command -v php || true)"
if [[ -z "${PHP_BIN}" ]]; then
  for p in /usr/local/bin/php /usr/bin/php /opt/bitnami/php/bin/php /opt/php*/bin/php; do
    [[ -x "$p" ]] && PHP_BIN="$p" && break
  done
fi
if [[ -z "${PHP_BIN}" ]]; then
  echo "ERROR: PHP CLI not found. Install php-cli or adjust PATH." >&2
  exit 1
fi
echo "Using PHP at: ${PHP_BIN}"

# --- One-time maintenance BEFORE starting workers/scheduler ---
# 1) Ask any long-running Laravel workers to gracefully restart (if any exist)
#    This sets a cache flag checked by workers; safe even if none are running.
${PHP_BIN} artisan queue:restart || true

# 2) Clear runtime caches and rebuild config cache with *current* env
#    (Avoid route:cache unless you’re 100% sure no route closures are used.)
${PHP_BIN} artisan optimize:clear || true   # clears cache/config/route/view/events if available
${PHP_BIN} artisan config:clear || true
${PHP_BIN} artisan view:clear   || true
${PHP_BIN} artisan cache:clear  || true
${PHP_BIN} artisan config:cache || true

# --- Write supercronic schedule file (DESC: runs every minute) ---
# Use flock to prevent overlapping queue workers.
cat >/etc/cron.app <<EOF
# Run Laravel scheduler every minute
* * * * * cd /var/www/html && ${PHP_BIN} artisan schedule:run

# Drain the 'imports' queue every minute; no overlap; exit when empty
* * * * * flock -n /tmp/queue-imports.lock -c 'cd /var/www/html && ${PHP_BIN} artisan queue:work --queue=imports --stop-when-empty --sleep=1 --tries=1 --timeout=900 --max-time=840'
EOF

# --- Start supercronic in background (logs to stdout in JSON) ---
if command -v supercronic >/dev/null 2>&1; then
  /usr/local/bin/supercronic -json /etc/cron.app &
else
  echo "ERROR: supercronic not found." >&2
  exit 1
fi

# --- Configure Apache for Cloud Run ---
: "${PORT:=8080}"
echo "Updating Apache to listen on port ${PORT}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf

# --- Start Apache in the foreground (PID 1) ---
exec apache2-foreground
