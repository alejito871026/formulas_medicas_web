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



GATES AND POLICY
Policy formal para Fórmulas Médicas
Creé la policy con reglas por rol y propiedad del registro:
viewAny: cliente, despachador, administrativo
view/update/delete: dueño o despachador o administrativo
create: cliente o administrativo
createForPaciente: administrativo o dueño del paciente
Archivo: FormulaMedicaPolicy.php
Registro de Policy + Gates del sistema
Registré la policy en el provider.
Definí Gates para acceso modular:
acceso-dashboard
acceso-pacientes
acceso-formulas
acceso-medicamentos
acceso-inventarios
acceso-entregas
acceso-citas


Datos para iniciar sesión y validar por actor:

Administrativo
Correo: admin@formulas.test
Contraseña: password123
Despachador de medicamentos
Correo: despachador@formulas.test
Contraseña: password123
Cliente
Correo: cliente@formulas.test
Contraseña: password123





Sí, ese flujo es totalmente viable y es justo como operan muchos dispensarios digitales.
Para que funcione bien, lo clave es manejarlo como un proceso con estados y eventos auditables.

Trazabilidad Recomendada (Cliente Web)

Radicación de solicitud
Validación documental
Validación clínica/administrativa
Validación de cobertura/autorización EPS
Reserva de inventario
Agendamiento de cita de entrega
Preparación del pedido
Entrega (total o parcial)
Cierre y encuesta
Flujo Operativo


Estados Que Debes Tener

Solicitud: radicada, e

Reglas de Negocio Clave

El cliente sí puede crear cita, pero solo después de que la solicitud esté aprobada o parcial.
Se permite subir PDF/JPG/PNG con validaciones:
tamaño máximo (ej. 10MB),
escaneo antivirus,
integridad del archivo.
Una fórmula vencida pasa a vencida automáticamente.
Si no hay stock total, habilitar entrega parcial y crear saldo pendiente.
Citas con recordatorio automático (24h y 2h antes).
Si hay no_asistio, permitir reprogramación limitada (ej. 2 intentos).
Qué Registrar Para Trazabilidad Real

quien (usuario/rol).
que (evento).
cuando (timestamp).
desde/hacia (cambio de estado).
evidencia (archivo, comentario, motivo rechazo, observación clínica).
canal (web, callcenter, presencial).
Eventos Mínimos (Bitácora)

Eventos Mínimos (Bitácora)

solicitud_creada
formula_adjunta
documento_validado / documento_rechazado
eps_aprobada / eps_rechazada
inventario_reservado
cita_propuesta
cita_confirmada / cita_reprogramada
pedido_preparado
entrega_parcial / entrega_total
caso_cerrado
KPIs para Control del Proceso

Tiempo promedio desde radicación hasta aprobación.
Tiempo promedio desde aprobación hasta entrega.
% de entregas parciales.
% no-asistencia a cita.
% solicitudes rechazadas por calidad documental.
Nivel de cumplimiento SLA (ej. < 72h).
SLA Sugerido

Revisión documental: <= 8 horas hábiles.
Validación de cobertura: <= 24 horas.
Propuesta de cita: <= 24 horas después de aprobación.
Entrega: <= 48 horas después de cita confirmada (según disponibilidad).
Estructura Técnica Recomendada (en tu proyecto)

Tabla solicitudes_formula (cabecera del proceso).
Tabla solicitud_adjuntos (PDF/fotos + metadatos).
Tabla solicitud_eventos (bitácora inmutable de trazabilidad).
Relación con citas, entregas, inventario, pacientes, usuarios.
Notificaciones por correo/WhatsApp para cada transición crítica.
Si quieres, en el siguiente paso te propongo el diseño exacto de tablas + estados + transiciones listo para migraciones Laravel y políticas por rol (cliente, despachador, admin).