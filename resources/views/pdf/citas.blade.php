<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de citas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; }
        .meta { margin-top: 4px; color: #4b5563; }
        .filters { margin-top: 8px; font-size: 10px; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #0f172a; color: #ffffff; text-align: left; padding: 7px; font-size: 10px; }
        td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
        .empty { text-align: center; color: #6b7280; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Listado de citas</div>
        <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }}</div>
        <div class="meta">Usuario: {{ $usuario?->name ?? 'Sistema' }}</div>
        <div class="filters">
            Filtros - Estado: {{ $estado === 'todos' ? 'Todos' : ucfirst(str_replace('_', ' ', $estado)) }} |
            Motivo: {{ $motivo === 'todos' ? 'Todos' : ucfirst($motivo) }} |
            Busqueda: {{ trim((string) $busqueda) !== '' ? $busqueda : 'Sin busqueda' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Documento</th>
                <th>Formula</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $cita)
                <tr>
                    <td>{{ trim(($cita->paciente?->nombres ?? '') . ' ' . ($cita->paciente?->apellidos ?? '')) ?: 'N/A' }}</td>
                    <td>{{ $cita->paciente?->numero_documento ?? 'N/A' }}</td>
                    <td>{{ $cita->formulaMedica?->numero_formula ?? 'Sin formula' }}</td>
                    <td>{{ $cita->fecha_cita?->format('Y-m-d') ?? 'N/A' }}</td>
                    <td>{{ substr((string) $cita->hora_cita, 0, 5) ?: 'N/A' }}</td>
                    <td>{{ ucfirst($cita->motivo ?? 'N/A') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $cita->estado ?? 'N/A')) }}</td>
                    <td>{{ $cita->observaciones ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">No hay citas para mostrar con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Sistema de formulas medicas</div>
</body>
</html>
