<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizacion de estado de entrega</title>
</head>
<body>
    <p>Hola {{ $entrega->formulaItem?->formulaMedica?->paciente?->user?->name ?? 'paciente' }},</p>

    <p>
        El estado de una entrega asociada a tu formula
        <strong>{{ $entrega->formulaItem?->formulaMedica?->numero_formula ?? 'N/A' }}</strong>
        cambio de
        <strong>{{ $estadoAnterior }}</strong>
        a
        <strong>{{ $estadoNuevo }}</strong>.
    </p>

    <ul>
        <li>Medicamento: {{ $entrega->formulaItem?->medicamento?->nombre ?? 'No disponible' }}</li>
        <li>Cantidad entregada: {{ $entrega->cantidad_entregada }}</li>
        <li>Fecha de entrega: {{ optional($entrega->fecha_entrega)->format('d/m/Y') }}</li>
        <li>Actualizado por: {{ $actor?->name ?? 'sistema' }}</li>
    </ul>

    <p>Puedes revisar el estado de tus entregas en el aplicativo.</p>
</body>
</html>
