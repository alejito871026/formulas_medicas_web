# Instructivo de Despliegue y Configuracion - Formulas Medicas Web

## 1. Proposito

En este instructivo documento el proceso de instalacion, configuracion y puesta en marcha del aplicativo Formulas Medicas Web para ambientes de desarrollo y despliegue productivo.

## 2. Arquitectura tecnica resumida

- Backend: Laravel 13 sobre PHP 8.3+.
- Frontend: Vite + Tailwind CSS.
- Base de datos: MySQL (soporta otros motores configurables en Laravel, incluyendo PostgreSQL).
- Contenedores: Docker/Docker Compose (Laravel Sail en desarrollo y Dockerfile de produccion).
- Reportes: Generacion de PDF mediante laravel-dompdf.
- Documentacion API: Scramble (Swagger/OpenAPI).

## 3. Requisitos de software y hardware

### 3.1 Requisitos minimos de software

- Git.
- PHP 8.3 o superior.
- Composer 2.x.
- Node.js 20.x y npm.
- MySQL 8.x (o motor equivalente soportado por Laravel).
- Opcional para contenedores: Docker Engine y Docker Compose.

### 3.2 Requisitos minimos de hardware sugeridos

- CPU: 2 nucleos.
- RAM: 4 GB (recomendado 8 GB para entorno local con Docker).
- Disco libre: 5 GB minimo.

## 4. Preparacion del codigo fuente

1. Clonar el repositorio en el servidor o equipo local.
2. Ingresar a la carpeta raiz del proyecto.
3. Verificar existencia de archivos base (artisan, composer.json, package.json).

## 5. Instalacion de dependencias

### 5.1 Dependencias backend

Ejecutar:

composer install

### 5.2 Dependencias frontend

Ejecutar:

npm install

## 6. Configuracion de base de datos

### 6.1 Creacion de base de datos

1. Crear una base de datos vacia en el motor seleccionado.
2. Crear usuario tecnico con permisos de lectura y escritura.
3. Registrar host, puerto, nombre de base de datos, usuario y contrasena para el archivo .env.

### 6.2 Motores soportados

La aplicacion permite configurar conexiones para:

- sqlite
- mysql
- mariadb
- pgsql
- sqlsrv

Para este proyecto se recomienda MySQL por compatibilidad con la configuracion de Docker Compose incluida.

## 7. Configuracion del archivo .env

Si no existe archivo .env, crearlo a partir de la plantilla del framework:

cp .env.example .env

Variables minimas recomendadas:

APP_NAME="Formulas Medicas Web"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-dominio

LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=formulas_medicas
DB_USERNAME=usuario_app
DB_PASSWORD=clave_segura

MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=usuario_smtp
MAIL_PASSWORD=clave_smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
MAIL_FROM_NAME="Formulas Medicas Web"
API_VERSION=1.0.0

Notas:

- APP_KEY debe generarse con php artisan key:generate.
- En entornos de pruebas locales se puede iniciar con MAIL_MAILER=log.
- Para JWT se debe definir el secreto con php artisan jwt:secret.

## 8. Ejecucion de migraciones y seeders

### 8.1 Inicializacion de esquema

php artisan migrate

### 8.2 Carga de datos base

php artisan db:seed

Seeders relevantes del proyecto:

- RoleSeeder.
- RenderUsersSeeder.
- DatabaseSeeder (opcional para datos demo).

Credenciales de prueba normalmente esperadas por seeding:

- admin@formulas.test
- despachador@formulas.test
- cliente@formulas.test
Contrasena: password123

## 9. Configuracion de servicios externos

## 9.1 Correo electronico

La aplicacion utiliza notificaciones por correo para eventos de negocio, por lo tanto se debe configurar SMTP o proveedor equivalente (Resend, SES, Postmark, etc.).

Variables posibles:

- MAIL_* para SMTP.
- RESEND_API_KEY.
- AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY / AWS_DEFAULT_REGION.
- POSTMARK_API_KEY.

## 9.2 Variables de despliegue opcionales

- RUN_DEMO_SEEDERS=true para ejecutar datos de demostracion al iniciar contenedor productivo.
- DATABASE_URL cuando la plataforma de hosting provee cadena de conexion unica.

## 9.3 Documentacion API con Swagger (Scramble)

Este proyecto tiene habilitada la documentacion de API para facilitar pruebas e integraciones.

Rutas de consulta:

- UI Swagger/OpenAPI: /docs/api
- Documento OpenAPI JSON: /docs/api.json

Configuracion relevante:

- Archivo de configuracion: config/scramble.php
- Middleware aplicado a la documentacion: web y RestrictedDocsAccess
- Version de API configurable por variable de entorno API_VERSION

Validacion recomendada:

1. Levantar aplicacion.
2. Abrir /docs/api y verificar carga de la interfaz.
3. Abrir /docs/api.json y verificar respuesta JSON valida.

## 10. Procedimiento de despliegue

## 10.1 Opcion A: despliegue local con Laravel Sail

1. Instalar dependencias:

composer install
npm install

2. Levantar servicios:

./vendor/bin/sail up -d

3. Ejecutar migraciones:

./vendor/bin/sail artisan migrate

4. Ejecutar seeders:

./vendor/bin/sail artisan db:seed

5. Levantar frontend en desarrollo (si aplica):

./vendor/bin/sail npm run dev

## 10.2 Opcion B: despliegue por contenedor productivo

1. Construir imagen:

docker build -t formulas-medicas-web .

2. Ejecutar contenedor:

docker run -d -p 80:80 --env-file .env formulas-medicas-web

3. Verificar logs de arranque y bootstrap de base de datos en:

storage/logs/bootstrap-db.log

4. Confirmar acceso HTTP desde APP_URL configurada.

## 10.3 Opcion C: despliegue con Docker Compose

1. Ajustar variables en .env (APP_PORT, DB_*, etc.).
2. Ejecutar:

docker compose up -d --build

3. Verificar salud de servicios:

docker compose ps

## 10.4 Opcion D: despliegue en Render con rama dedicada

En este proyecto tambien puedo desplegar en Render usando una rama especifica para ese entorno. Este enfoque me permite separar cambios de despliegue respecto a la rama principal.

Flujo recomendado de control de versiones:

1. Crear y mantener una rama de despliegue, por ejemplo render, con los ajustes de infraestructura requeridos.
2. Desde el servidor o entorno de build asociado, posicionarme en la rama main.
3. Actualizar codigo con git pull origin main antes de cada despliegue.
4. Verificar que el ultimo commit descargado corresponda a la version aprobada.

Pasos tecnicos sugeridos:

1. Configurar en Render el servicio Web conectado al repositorio.
2. Seleccionar la rama main como rama de despliegue.
3. Definir variables de entorno en Render, como minimo:

APP_ENV=production
APP_DEBUG=false
APP_KEY=...
APP_URL=https://tu-servicio.onrender.com
DB_CONNECTION=pgsql o mysql segun servicio contratado
DB_HOST=...
DB_PORT=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
MAIL_MAILER=...

4. Configurar comando de build y arranque segun estrategia:

- Si se despliega con Dockerfile del proyecto, Render ejecuta build de contenedor y usa el proceso de arranque definido en entrypoint.sh.
- Si se despliega sin Docker, configurar build para instalar dependencias y compilar assets, y start command para iniciar PHP/Nginx segun el runtime.

5. Confirmar en logs de Render:

- Ejecucion correcta de migraciones.
- Ejecucion de seeders base (si aplica).
- Inicio exitoso de Nginx y PHP-FPM.
6. Verificar exposicion de documentacion API:

- Abrir /docs/api.
- Validar descarga/lectura de /docs/api.json.

Notas operativas para Render:

- Si se requiere poblar datos de demostracion, habilitar RUN_DEMO_SEEDERS=true.
- El script de entrada del proyecto registra el bootstrap de base de datos en storage/logs/bootstrap-db.log.
- Cuando se use base de datos administrada por Render, preferir variables seguras del panel y no credenciales hardcodeadas.

## 11. Puesta en marcha del sistema

Checklist de arranque:

1. La aplicacion responde en la URL definida.
2. El login permite autenticacion correcta.
3. Los modulos cargan segun el rol.
4. Se pueden crear registros de prueba (paciente, formula, cita).
5. Se genera al menos un reporte PDF (citas o entregas).
6. Se envia o registra notificacion de correo segun configuracion.

## 12. Endurecimiento basico para produccion

- APP_ENV=production.
- APP_DEBUG=false.
- Rotacion y monitoreo de logs.
- Uso de HTTPS.
- Credenciales de base de datos y correo en variables seguras.
- Politica de backups de base de datos y archivos adjuntos.

## 13. Consideraciones de mantenimiento

Mantenimiento preventivo recomendado:

- Revisar crecimiento de logs en storage/logs.
- Ejecutar respaldo de base de datos con periodicidad definida.
- Actualizar dependencias de seguridad (composer y npm) de forma controlada.
- Validar expiracion de certificados y credenciales SMTP.
- Monitorear uso de disco por archivos de carga y reportes.

Mantenimiento correctivo:

- Analizar excepciones en logs de Laravel.
- Verificar conectividad con base de datos.
- Reprocesar migraciones y seeders solo bajo ventana controlada.
- Confirmar permisos de escritura en storage y bootstrap/cache.

## 14. Comandos de soporte operativo

Limpiar y reconstruir cache de aplicacion:

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

Ejecucion de pruebas:

php artisan test

Compilacion de frontend para produccion:

npm run build

## 15. Criterio de aceptacion del despliegue

Considero el despliegue exitoso cuando:

- El sistema autentica usuarios por rol.
- Los modulos principales operan sin errores criticos.
- Las migraciones y seeders finalizan correctamente.
- Los reportes PDF se generan.
- El entorno queda documentado y reproducible para soporte tecnico.
