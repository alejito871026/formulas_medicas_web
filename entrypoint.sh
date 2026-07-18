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

# Arrancar el servidor web Nginx en primer plano
echo "Iniciando Nginx..."
nginx -g 'daemon off;'
