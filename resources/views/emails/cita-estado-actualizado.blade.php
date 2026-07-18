<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizacion de estado de cita</title>
</head>
<body>
    <p>Hola {{ $cita->paciente?->user?->name ?? 'cliente' }},</p>

    <p>
        El estado de tu cita del
        <strong>{{ optional($cita->fecha_cita)->format('d/m/Y') }}</strong>
        cambio de
        <strong>{{ $estadoAnterior }}</strong>
        a
        <strong>{{ $estadoNuevo }}</strong>.
    </p>

    <ul>
        <li>Motivo: {{ $cita->motivo }}</li>
        <li>Hora: {{ $cita->hora_cita }}</li>
        <li>Formula asociada: {{ $cita->formulaMedica?->numero_formula ?? 'No aplica' }}</li>
        <li>Actualizado por: {{ $actor?->name ?? 'sistema' }}</li>
    </ul>

    <p>Puedes revisar tus citas en el aplicativo.</p>
</body>
</html>
