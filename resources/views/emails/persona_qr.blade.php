<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu código QR</title>
    <style>
        .qr-container {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .qr-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; color:#111;">
    <h1>Hola {{ $persona->Nombre }},</h1>
    <p>
        Gracias por registrarte. Adjuntamos tu código QR personal y los códigos QR de tus portátiles en formato PNG.
        Podrás utilizarlos para tu identificación y registro de equipos en el sistema.
    </p>

    <!-- QR de la Persona -->
    <div class="qr-container">
        <div class="qr-title">📋 Tu QR Personal</div>
        <p>Este QR codifica tu documento de identidad:</p>
        @if($persona->qrCode)
            <p>
                <img src="{{ $persona->qrCode }}" alt="QR Persona" style="width:180px;height:180px; border: 2px solid #39A900; border-radius: 8px;" />
            </p>
        @endif
    </div>

    <!-- QR de Portátiles -->
    @if(isset($portatiles) && $portatiles->count() > 0)
        <div class="qr-container">
            <div class="qr-title">💻 QR de tus Portátiles</div>
            <p>Se han registrado {{ $portatiles->count() }} portátil(es):</p>
            @foreach($portatiles as $index => $portatil)
                <div style="margin: 15px 0; padding: 10px; background-color: #fff; border-left: 3px solid #50E5F9;">
                    <p style="margin: 5px 0;">
                        <strong>Portátil {{ $index + 1 }}:</strong><br>
                        <strong>Serial:</strong> {{ $portatil->serial }}<br>
                        @if($portatil->marca)
                            <strong>Marca:</strong> {{ $portatil->marca }}<br>
                        @endif
                        @if($portatil->modelo)
                            <strong>Modelo:</strong> {{ $portatil->modelo }}<br>
                        @endif
                    </p>
                    @if($portatil->qrCode)
                        <p>
                            <img src="{{ $portatil->qrCode }}" alt="QR Portátil {{ $index + 1 }}" style="width:180px;height:180px; border: 2px solid #50E5F9; border-radius: 8px;" />
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <p style="margin-top: 20px;">
        <strong>Nota:</strong> Los códigos QR también están adjuntos a este correo como archivos PNG que puedes descargar.
    </p>

    <p>
        Si no solicitaste este registro, por favor ignora este mensaje.
    </p>

    <p style="margin-top: 32px; color:#555;">
        — Equipo CTAccess
    </p>
</body>
</html>
