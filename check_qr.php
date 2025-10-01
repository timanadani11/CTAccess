<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verificando Códigos QR de Personas ===\n\n";

$personas = App\Models\Persona::select('idPersona', 'Nombre', 'documento', 'qrCode')
    ->limit(5)
    ->get();

foreach ($personas as $persona) {
    echo "ID: {$persona->idPersona}\n";
    echo "Nombre: {$persona->Nombre}\n";
    echo "Documento: {$persona->documento}\n";
    echo "QR Code: " . ($persona->qrCode ?? 'NULL') . "\n";
    
    if ($persona->qrCode) {
        $fullPath = public_path(ltrim($persona->qrCode, '/'));
        echo "Archivo existe: " . (file_exists($fullPath) ? 'SÍ ✓' : 'NO ✗') . "\n";
        echo "Ruta completa: {$fullPath}\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}
