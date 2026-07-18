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

# Ejecutar migraciones automaticamente
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Sembrar roles base para permitir registro/autenticacion en entornos nuevos
echo "Sembrando roles base..."
php artisan db:seed --class=RoleSeeder --force

# Crear/actualizar usuarios de acceso inicial en Render
echo "Sembrando usuarios base de despliegue..."
php artisan db:seed --class=RenderUsersSeeder --force

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Sembrado demo opcional para entornos de exhibicion/seminario.
# Se ejecuta en segundo plano para no bloquear la deteccion de puerto en Render.
if [ "$RUN_DEMO_SEEDERS" = "true" ]; then
    echo "RUN_DEMO_SEEDERS=true: ejecutando DatabaseSeeder en segundo plano..."
    (
        php artisan db:seed --class=DatabaseSeeder --force \
            >> /var/www/html/storage/logs/demo-seeder.log 2>&1
        echo "$(date '+%Y-%m-%d %H:%M:%S') DatabaseSeeder finalizado" \
            >> /var/www/html/storage/logs/demo-seeder.log
    ) &
    echo "Seeder demo lanzado. Revisa storage/logs/demo-seeder.log para seguimiento."
else
    echo "RUN_DEMO_SEEDERS desactivado: se omite DatabaseSeeder."
fi

# Arrancar el servidor web Nginx en primer plano
echo "Iniciando Nginx..."
nginx -g 'daemon off;'
