#!/bin/sh
set -eu
if [ -z "${APP_KEY:-}" ]; then echo 'APP_KEY es obligatoria.' >&2; exit 1; fi
sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf
sed -i "s/\*:80/*:${PORT:-80}/" /etc/apache2/sites-available/000-default.conf
php artisan migrate --force
if [ "${SEED_ON_BOOT:-false}" = "true" ]; then php artisan solaris:seed-if-empty; fi
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
