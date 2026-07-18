<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Codigo de verificacion</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Tu codigo de verificacion es: <strong>{{ $otp }}</strong></p>
    <p>Este codigo es valido por 5 minutos.</p>
    <p>Si no solicitaste este codigo, ignora este mensaje.</p>
</body>
</html>
