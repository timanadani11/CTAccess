<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu código QR</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111;">
    <h1>Hola {{ $persona->Nombre }},</h1>
    <p>
        Gracias por registrarte. Adjuntamos tu código QR en formato PNG.
        Este QR codifica tu documento de identidad y podrás utilizarlo para tu identificación en el sistema.
    </p>

    @if($persona->qrCode)
        <p>También puedes visualizarlo aquí:</p>
        <p>
            <img src="{{ $persona->qrCode }}" alt="QR Persona" style="width:180px;height:180px;" />
        </p>
    @endif

    <p>
        Si no solicitaste este registro, por favor ignora este mensaje.
    </p>

    <p style="margin-top: 32px; color:#555;">
        — Equipo CTAccess
    </p>
</body>
</html>
