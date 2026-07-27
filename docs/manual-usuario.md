# Manual de Usuario - Formulas Medicas Web

## 1. Proposito del manual

En este manual describo el uso funcional del aplicativo Formulas Medicas Web desde la perspectiva del usuario final y de los roles operativos del sistema. El objetivo es facilitar la adopcion de la herramienta y estandarizar el proceso de registro, consulta, agendamiento y entrega de medicamentos.

## 2. Alcance y perfiles de acceso

El sistema maneja control de acceso por rol:

- Paciente: registra y administra formulas medicas, agenda citas y consulta su informacion.
- Despachador: administra medicamentos, inventario y entregas.
- Administrativo: administra usuarios operativos, EPS, pacientes y tambien accede a los modulos funcionales.

## 3. Requisitos para usar el sistema

- Navegador web actualizado (Chrome, Edge o Firefox).
- Conexion a internet o red institucional.
- Credenciales de acceso activas.

## 4. Inicio de sesion

1. Ingreso a la URL del aplicativo.
2. Se muestra el formulario de autenticacion.
3. Diligencio correo y contrasena.
4. Selecciono la opcion Iniciar sesion.
5. Si la implementacion de seguridad lo solicita, realizo validacion OTP en la pantalla de verificacion.
6. El sistema redirige al panel principal (dashboard) segun mis permisos.

Validaciones esperadas:

- Si las credenciales son incorrectas, el sistema muestra mensaje de error.
- Si la cuenta no tiene permisos para una opcion, el acceso se bloquea por rol.

## 5. Gestion de usuarios (aplica para rol administrativo)

Dentro del modulo administrativo puedo gestionar usuarios operativos del proceso (despachadores) y entidades relacionadas.

### 5.1 Gestion de despachadores

- Crear despachador.
- Editar datos del despachador.
- Activar o desactivar estado del usuario.
- Eliminar registro cuando aplique politica interna.

### 5.2 Gestion de pacientes

- Crear paciente individual.
- Editar datos del paciente.
- Activar o desactivar paciente.
- Eliminar paciente.
- Importar pacientes desde archivo CSV/Excel usando plantilla del sistema.

### 5.3 Gestion de EPS

- Crear EPS.
- Editar EPS.
- Activar o desactivar EPS.
- Eliminar EPS.

## 6. Uso de modulos principales

## 6.1 Modulo Formulas Medicas (paciente y administrativo)

Flujo principal:

1. Ingreso al modulo Formulas Medicas.
2. Registro una nueva formula con datos clinicos requeridos.
3. Agrego o actualizo informacion de la formula.
4. Realizo consulta del estado de cada formula.
5. Si aplica, edito o elimino el registro segun permisos.

Resultado esperado:

- Cada formula queda asociada al paciente.
- El estado permite seguimiento del proceso.

## 6.2 Modulo Citas (paciente y administrativo)

Flujo principal:

1. Ingreso al modulo Citas.
2. Registro una cita nueva con fecha, hora y motivo.
3. Consulto el listado de citas y su estado.
4. Edito o elimino citas cuando sea necesario.

Resultado esperado:

- Queda trazabilidad de las citas asociadas a paciente y formula.

## 6.3 Modulo Medicamentos (despachador y administrativo)

Flujo principal:

1. Ingreso al modulo Medicamentos.
2. Registro medicamentos nuevos.
3. Actualizo presentacion, concentracion y datos operativos.
4. Consulto catalogo para soporte de despacho.

## 6.4 Modulo Inventario (despachador y administrativo)

Flujo principal:

1. Ingreso al modulo Inventario.
2. Registro lotes y stock disponible.
3. Actualizo cantidades y fecha de vencimiento.
4. Consulto disponibilidad para el proceso de entrega.

## 6.5 Modulo Entregas (despachador y administrativo)

Flujo principal:

1. Ingreso al modulo Entregas.
2. Registro entregas parciales o totales.
3. Actualizo estado de entrega.
4. Consulto historico de entregas.

## 7. Generacion de reportes

El aplicativo permite generar reportes en PDF para los siguientes modulos:

- Reporte de citas (listado en PDF).
- Reporte de entregas (listado en PDF).

Flujo recomendado para generar reporte:

1. Ingreso al modulo correspondiente.
2. Aplico filtros de consulta cuando aplique.
3. Selecciono la opcion de exportar a PDF.
4. Descargo el archivo generado para revision o archivo institucional.

## 8. Consultas y procesos relevantes

Consultas operativas frecuentes:

- Consulta de formulas medicas por paciente y estado.
- Consulta de agenda de citas.
- Consulta de stock por medicamento.
- Consulta de estado de entregas (pendiente, parcial, entregado).

Procesos relevantes del sistema:

- Control de acceso por rol para proteger cada modulo.
- Trazabilidad de formulas, citas y entregas.
- Notificaciones por correo ante cambios de estado (segun configuracion del entorno).

## 9. Cierre de sesion

Para finalizar la sesion de forma segura:

1. Ubico la opcion Cerrar sesion en la interfaz.
2. Confirmo la salida.
3. El sistema redirige nuevamente al inicio de sesion.

Recomendacion:

- Siempre cierro sesion al terminar, especialmente en equipos compartidos.

## 10. Capturas de pantalla (anexo sugerido)

Para el documento final recomiendo incluir como minimo las siguientes evidencias visuales:

1. Pantalla de inicio de sesion.
2. Pantalla de verificacion OTP (si se encuentra habilitada).
3. Dashboard por rol.
4. Gestion de despachadores o pacientes.
5. Registro y consulta de formulas medicas.
6. Registro y consulta de citas.
7. Registro y consulta de inventario.
8. Registro y consulta de entregas.
9. Generacion de reporte PDF de citas.
10. Generacion de reporte PDF de entregas.
11. Cierre de sesion.

Formato recomendado para anexar capturas:

- Nombre sugerido de carpeta: docs/capturas/manual-usuario/
- Nomenclatura sugerida: 01-login.png, 02-otp.png, 03-dashboard.png, ...
- En el documento final, cada captura debe ir con pie de figura y breve explicacion funcional.

## 11. Recomendaciones de uso

- Mantener actualizados los datos maestros (medicamentos, EPS y pacientes).
- Validar la fecha de vencimiento de formulas e inventario antes de confirmar entregas.
- Usar filtros y reportes PDF para trazabilidad y soporte de auditoria.
- Reportar incidencias funcionales al administrador del sistema con evidencia de pantalla y hora del evento.
