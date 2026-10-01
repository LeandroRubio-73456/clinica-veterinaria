#!/bin/sh
# Arranque de la demo: la base SQLite se recrea con datos de ejemplo en cada inicio,
# así cualquier cambio que hagan los visitantes desaparece al reiniciar.
set -e

PORT="${PORT:-8080}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Sin APP_KEY definida se genera una temporal (las sesiones no sobreviven a un reinicio, igual que los datos).
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
fi

rm -f "$DB_DATABASE"
su -s /bin/sh www-data -c "touch '$DB_DATABASE'"
su -s /bin/sh www-data -p -c "php artisan migrate:fresh --seed --force"
for seeder in $DEMO_SEEDERS; do
    su -s /bin/sh www-data -p -c "php artisan db:seed --class=$seeder --force"
done
php artisan storage:link >/dev/null 2>&1 || true
su -s /bin/sh www-data -p -c "php artisan optimize"

if [ "$RUN_SCHEDULER" = "true" ]; then
    su -s /bin/sh www-data -p -c "php artisan schedule:work" &
fi

exec apache2-foreground
