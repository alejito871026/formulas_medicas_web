# Diccionario de Datos

## roles

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del rol |
| nombre | varchar | 50 | No | Nombre del rol: administrador, dispensador, paciente |
| descripcion | varchar | 150 | Si | Descripcion funcional del rol |

## users

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del usuario |
| role_id | bigint | 20 | Si | Rol asignado dentro del sistema |
| name | varchar | 255 | No | Nombre del usuario |
| email | varchar | 255 | No | Correo unico de autenticacion |
| password | varchar | 255 | No | Hash de contrasena |

## pacientes

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del paciente |
| user_id | bigint | 20 | Si | Usuario autenticable asociado |
| tipo_documento | varchar | 20 | No | Tipo de documento |
| numero_documento | varchar | 20 | No | Numero de identificacion unico |
| nombres | varchar | 80 | No | Nombres del paciente |
| apellidos | varchar | 80 | No | Apellidos del paciente |
| fecha_nacimiento | date | - | Si | Fecha de nacimiento |
| telefono | varchar | 20 | Si | Telefono de contacto |
| email | varchar | 120 | Si | Correo del paciente |
| direccion | varchar | 150 | Si | Direccion de residencia |
| eps | varchar | 120 | Si | EPS del paciente |
| municipio | varchar | 80 | Si | Municipio de residencia |

## formulas_medicas

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria de la formula |
| paciente_id | bigint | 20 | No | Paciente al que pertenece |
| numero_formula | varchar | 40 | No | Consecutivo o codigo de formula |
| fecha_formula | date | - | No | Fecha de emision |
| fecha_vencimiento | date | - | Si | Fecha limite de validez |
| medico_tratante | varchar | 120 | Si | Profesional que prescribe |
| estado | varchar | 30 | No | Estado general de la formula |
| observaciones | text | - | Si | Notas de validacion o novedad |

## medicamentos

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del medicamento |
| codigo | varchar | 30 | No | Codigo interno o CUM |
| nombre | varchar | 120 | No | Nombre del medicamento |
| principio_activo | varchar | 120 | Si | Principio activo |
| presentacion | varchar | 80 | No | Tableta, jarabe, ampolla, etc. |
| concentracion | varchar | 60 | Si | Concentracion formulada |
| unidad_medida | varchar | 30 | Si | mg, ml, unidades, etc. |
| requiere_formula | boolean | 1 | No | Indica si exige formula medica |
| observaciones | text | - | Si | Comentarios operativos |

## formula_medicamento

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del detalle |
| formula_medica_id | bigint | 20 | No | Formula asociada |
| medicamento_id | bigint | 20 | No | Medicamento prescrito |
| cantidad_formulada | int | 11 | No | Cantidad total solicitada |
| cantidad_entregada | int | 11 | No | Cantidad entregada acumulada |
| dosis | varchar | 80 | Si | Dosis formulada |
| frecuencia | varchar | 80 | Si | Frecuencia de uso |
| estado_item | varchar | 30 | No | Pendiente, parcial o entregado |

## inventarios

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria del registro de inventario |
| medicamento_id | bigint | 20 | No | Medicamento controlado |
| lote | varchar | 40 | No | Lote del laboratorio |
| stock_actual | int | 11 | No | Existencia disponible |
| stock_minimo | int | 11 | No | Minimo permitido para alerta |
| fecha_vencimiento | date | - | Si | Vencimiento del lote |
| ubicacion | varchar | 80 | Si | Ubicacion fisica en bodega o mostrador |

## entregas

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria de la entrega |
| formula_medicamento_id | bigint | 20 | No | Item de formula entregado |
| user_id | bigint | 20 | Si | Usuario que registra la entrega |
| fecha_entrega | date | - | No | Fecha de entrega o compromiso |
| cantidad_entregada | int | 11 | No | Cantidad entregada en el movimiento |
| estado_entrega | varchar | 30 | No | Pendiente, parcial, entregado, reprogramado |
| fecha_estimada | date | - | Si | Fecha promesa para pendientes |
| observaciones | text | - | Si | Comentarios del funcionario |

## citas

| Campo | Tipo | Longitud | Nulo | Descripcion |
| --- | --- | --- | --- | --- |
| id | bigint | 20 | No | Clave primaria de la cita |
| paciente_id | bigint | 20 | No | Paciente agendado |
| formula_medica_id | bigint | 20 | Si | Formula asociada a la visita |
| fecha_cita | date | - | No | Fecha programada |
| hora_cita | time | - | No | Hora asignada |
| motivo | varchar | 80 | No | Motivo de la cita |
| estado | varchar | 30 | No | Programada, atendida, cancelada |
| observaciones | text | - | Si | Observaciones operativas |