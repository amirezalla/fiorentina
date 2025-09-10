#!/bin/bash
set -euo pipefail

# --- Locate PHP CLI ---
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

# --- Write cron with explicit PATH + absolute php path ---
CRON_ENV=$'SHELL=/bin/bash\nPATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'
cat <<CRON | crontab -
${CRON_ENV}
* * * * * cd /var/www/html && ${PHP_BIN} artisan schedule:run >> /var/log/schedule.log 2>&1
* * * * * cd /var/www/html && ${PHP_BIN} artisan queue:work --queue=imports --stop-when-empty --sleep=3 --tries=1 --timeout=120 >> /var/log/queue-imports.log 2>&1
CRON

service cron start
#!/bin/bash
set -e

# Set the Apache port configuration based on the PORT environment variable
if [ -z "${PORT}" ]; then
  PORT=8080
fi

echo "Updating Apache to listen on port ${PORT}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost *:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Start Apache in the foreground
exec apache2-foreground