#!/usr/bin/env bash
set -euo pipefail

# --- Locate PHP CLI once (absolute path avoids PATH issues in cron-like runners) ---
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

# --- Write supercronic schedule file ---
cat >/etc/cron.app <<EOF
# Run Laravel scheduler every minute
* * * * * cd /var/www/html && ${PHP_BIN} artisan schedule:run
# Drain the 'imports' queue every minute; exits when empty
* * * * * cd /var/www/html && ${PHP_BIN} artisan queue:work --queue=imports --stop-when-empty --sleep=3 --tries=1 --timeout=120
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
