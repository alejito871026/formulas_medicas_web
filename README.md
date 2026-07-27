# Formulas Medicas Web

Aplicacion web en Laravel para la gestion de formulas medicas en dispensario, con control por roles e interfaz diferenciada por actor.

## Funcionalidades implementadas

- Login, registro y logout para usuarios del sistema.
- Interfaces por rol:
	- cliente
	- despachador
	- administrativo
- Middleware de rol para proteger rutas web.
- API REST para formulas medicas protegida con JWT (`auth:api`).
- Endpoints de autenticacion API (`register`, `login`, `me`, `logout`).

## Requisitos

- PHP 8.3+
- Composer
- Node.js
- MySQL o motor configurado en `.env`

## Instalacion

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Dependencias para modulo API/JWT

Instala los paquetes si aun no estan en tu entorno:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

## Base de datos y datos semilla

```bash
php artisan migrate
php artisan db:seed
```

Seeder crea roles base y usuarios de prueba:

- `admin@formulas.test` / `password123` (administrativo)
- `despachador@formulas.test` / `password123` (despachador)
- `cliente@formulas.test` / `password123` (cliente)

## Rutas web

- `GET /login`
- `GET /register`
- `GET /dashboard` (requiere auth)
- Modulos protegidos por middleware `rol`.

## Rutas API (prefijo `/api`)

Publicas:

- `POST /api/register`
- `POST /api/login`

Protegidas JWT (`Authorization: Bearer <token>`):

- `POST /api/logout`
- `GET /api/me`
- `GET /api/formulas-medicas`
- `POST /api/formulas-medicas`
- `GET /api/formulas-medicas/{id}`
- `PUT/PATCH /api/formulas-medicas/{id}`
- `DELETE /api/formulas-medicas/{id}`

## Pruebas con Postman

1. Ejecutar `POST /api/login` y copiar `access_token`.
2. Configurar Bearer Token en la coleccion.
3. Probar CRUD de `formulas-medicas`.
4. Verificar codigos HTTP: `200`, `201`, `204`, `401`, `403`, `422`.

