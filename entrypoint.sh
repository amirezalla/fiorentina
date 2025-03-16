#!/bin/bash
# Fetch certificate and key from Cloud Storage
gsutil cp gs://laviola-sql/cloudflare_origin.pem /tmp/cloudflare_origin.pem
gsutil cp gs://laviola-sql/cloudflare_origin.key /tmp/cloudflare_origin.key

# Set permissions on the key file
chmod 600 /tmp/cloudflare_origin.key

# Optionally, if Apache requires the files in /etc/ssl/certs and /etc/ssl/private,
# move them (make sure the directories are writable or adjust Apache config accordingly)
cp /tmp/cloudflare_origin.pem /etc/ssl/certs/cloudflare_origin.pem
cp /tmp/cloudflare_origin.key /etc/ssl/private/cloudflare_origin.key
# Setup Laravel Schedule Run
echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/cron.log 2>&1" | crontab -

# Start cron
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