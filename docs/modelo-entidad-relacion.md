# Modelo Entidad Relacion

Este MER surge del alcance descrito en el documento del proyecto: importacion de formulas medicas, consulta de disponibilidad, seguimiento de pendientes y agendamiento de visitas.

```mermaid
erDiagram
    ROLES ||--o{ USERS : asigna
    USERS ||--o| PACIENTES : autentica
    PACIENTES ||--o{ FORMULAS_MEDICAS : posee
    FORMULAS_MEDICAS ||--o{ FORMULA_MEDICAMENTO : detalla
    MEDICAMENTOS ||--o{ FORMULA_MEDICAMENTO : formula
    MEDICAMENTOS ||--o{ INVENTARIOS : controla
    FORMULA_MEDICAMENTO ||--o{ ENTREGAS : genera
    USERS ||--o{ ENTREGAS : registra
    PACIENTES ||--o{ CITAS : agenda
    FORMULAS_MEDICAS ||--o{ CITAS : origina

    ROLES {
        bigint id PK
        varchar nombre
        varchar descripcion
    }

    USERS {
        bigint id PK
        bigint role_id FK
        varchar name
        varchar email
        varchar password
    }

    PACIENTES {
        bigint id PK
        bigint user_id FK
        varchar tipo_documento
        varchar numero_documento
        varchar nombres
        varchar apellidos
        date fecha_nacimiento
        varchar telefono
        varchar email
        varchar direccion
        varchar eps
        varchar municipio
    }

    FORMULAS_MEDICAS {
        bigint id PK
        bigint paciente_id FK
        varchar numero_formula
        date fecha_formula
        date fecha_vencimiento
        varchar medico_tratante
        varchar estado
        text observaciones
    }

    MEDICAMENTOS {
        bigint id PK
        varchar codigo
        varchar nombre
        varchar principio_activo
        varchar presentacion
        varchar concentracion
        varchar unidad_medida
        boolean requiere_formula
        text observaciones
    }

    FORMULA_MEDICAMENTO {
        bigint id PK
        bigint formula_medica_id FK
        bigint medicamento_id FK
        int cantidad_formulada
        int cantidad_entregada
        varchar dosis
        varchar frecuencia
        varchar estado_item
    }

    INVENTARIOS {
        bigint id PK
        bigint medicamento_id FK
        varchar lote
        int stock_actual
        int stock_minimo
        date fecha_vencimiento
        varchar ubicacion
    }

    ENTREGAS {
        bigint id PK
        bigint formula_medicamento_id FK
        bigint user_id FK
        date fecha_entrega
        int cantidad_entregada
        varchar estado_entrega
        date fecha_estimada
        text observaciones
    }

    CITAS {
        bigint id PK
        bigint paciente_id FK
        bigint formula_medica_id FK
        date fecha_cita
        time hora_cita
        varchar motivo
        varchar estado
        text observaciones
    }
```

Sugerencia para el entregable:

1. Abrir MySQL Workbench.
2. Crear un nuevo modelo EER.
3. Replicar las tablas y relaciones del diagrama anterior.
4. Exportar la captura del modelo como imagen para el informe.