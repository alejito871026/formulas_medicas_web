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

# Ejecutar tareas de base de datos en segundo plano para no bloquear el arranque del puerto HTTP.
(
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando migraciones y seeders..." \
        >> /var/www/html/storage/logs/bootstrap-db.log

    echo "Ejecutando migraciones de base de datos..."
    php artisan migrate --force \
        >> /var/www/html/storage/logs/bootstrap-db.log 2>&1

    echo "Sembrando roles base..."
    php artisan db:seed --class=RoleSeeder --force \
        >> /var/www/html/storage/logs/bootstrap-db.log 2>&1

    echo "Sembrando usuarios base de despliegue..."
    php artisan db:seed --class=RenderUsersSeeder --force \
        >> /var/www/html/storage/logs/bootstrap-db.log 2>&1

    # Sembrado demo opcional para entornos de exhibicion/seminario.
    if [ "$RUN_DEMO_SEEDERS" = "true" ]; then
        echo "RUN_DEMO_SEEDERS=true: ejecutando DatabaseSeeder..."
        php artisan db:seed --class=DatabaseSeeder --force \
            >> /var/www/html/storage/logs/demo-seeder.log 2>&1
        echo "$(date '+%Y-%m-%d %H:%M:%S') DatabaseSeeder finalizado" \
            >> /var/www/html/storage/logs/demo-seeder.log
    else
        echo "RUN_DEMO_SEEDERS desactivado: se omite DatabaseSeeder." \
            >> /var/www/html/storage/logs/bootstrap-db.log
    fi

    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Tareas de base de datos finalizadas." \
        >> /var/www/html/storage/logs/bootstrap-db.log
) &

echo "Tareas de base de datos lanzadas en segundo plano."

# Arrancar el servidor web Nginx en primer plano
echo "Iniciando Nginx..."
nginx -g 'daemon off;'
