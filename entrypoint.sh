#!/bin/sh
set -e

echo "Ajustando permisos de almacenamiento..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Verificar que la variable DATABASE_URL este definida (para depuracion)
if [ -n "$DATABASE_URL" ]; then
    echo "DATABASE_URL detectada: $(echo $DATABASE_URL | sed 's/:[^:]*@/:***@/')"
else
    echo "ADVERTENCIA: DATABASE_URL no esta definida."
fi

# Forzar la optimizacion de cache en produccion
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enlace simbolico para storage (si se usa)
php artisan storage:link --force || true

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Ejecutar bootstrap de BD en segundo plano para no bloquear el arranque HTTP en Render.
(
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando migraciones y seeders base..." | tee -a /var/www/html/storage/logs/bootstrap-db.log

    php artisan migrate --force 2>&1 | tee -a /var/www/html/storage/logs/bootstrap-db.log

    php artisan db:seed --class=RoleSeeder --force 2>&1 | tee -a /var/www/html/storage/logs/bootstrap-db.log

    php artisan db:seed --class=RenderUsersSeeder --force 2>&1 | tee -a /var/www/html/storage/logs/bootstrap-db.log

    RUN_DEMO_SEEDERS_NORMALIZED=$(echo "${RUN_DEMO_SEEDERS:-false}" | tr '[:upper:]' '[:lower:]')

    if [ "$RUN_DEMO_SEEDERS_NORMALIZED" = "true" ] || [ "$RUN_DEMO_SEEDERS_NORMALIZED" = "1" ] || [ "$RUN_DEMO_SEEDERS_NORMALIZED" = "yes" ]; then
        echo "RUN_DEMO_SEEDERS=${RUN_DEMO_SEEDERS:-false}: ejecutando DatabaseSeeder..." | tee -a /var/www/html/storage/logs/bootstrap-db.log
        php artisan db:seed --class=DatabaseSeeder --force 2>&1 | tee -a /var/www/html/storage/logs/demo-seeder.log
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] DatabaseSeeder finalizado" | tee -a /var/www/html/storage/logs/bootstrap-db.log
    else
        echo "RUN_DEMO_SEEDERS=${RUN_DEMO_SEEDERS:-false}: desactivado, se omite DatabaseSeeder." | tee -a /var/www/html/storage/logs/bootstrap-db.log
    fi

    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Bootstrap de BD finalizado." | tee -a /var/www/html/storage/logs/bootstrap-db.log
) &

echo "Bootstrap de base de datos lanzado en segundo plano."

# Arrancar el servidor web Nginx en primer plano
echo "Iniciando Nginx..."
nginx -g 'daemon off;'
