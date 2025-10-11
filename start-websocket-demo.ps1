# ============================================
# 🚀 Script de Inicio Rápido - WebSockets Demo
# ============================================
# 
# Este script inicia todos los servicios necesarios
# para el demo de WebSockets con Laravel Reverb
#
# Autor: CTAccess Team
# Fecha: Octubre 2025
# ============================================

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  🚀 INICIANDO DEMO WEBSOCKETS + REVERB   " -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en el directorio correcto
if (-Not (Test-Path "artisan")) {
    Write-Host "❌ Error: No se encontró el archivo 'artisan'" -ForegroundColor Red
    Write-Host "   Asegúrate de ejecutar este script desde la raíz del proyecto Laravel" -ForegroundColor Yellow
    exit 1
}

Write-Host "📋 Verificando configuración..." -ForegroundColor Yellow
Write-Host ""

# Verificar .env
if (-Not (Test-Path ".env")) {
    Write-Host "❌ Error: No se encontró el archivo .env" -ForegroundColor Red
    Write-Host "   Copia .env.example a .env y configura las variables" -ForegroundColor Yellow
    exit 1
}

# Verificar variables de Reverb en .env
$envContent = Get-Content .env -Raw
if ($envContent -notmatch "REVERB_APP_KEY") {
    Write-Host "⚠️  Advertencia: Variables REVERB no encontradas en .env" -ForegroundColor Yellow
    Write-Host "   El sistema usará valores por defecto" -ForegroundColor Yellow
}

Write-Host "✅ Configuración verificada" -ForegroundColor Green
Write-Host ""

# Limpiar caché
Write-Host "🧹 Limpiando caché de Laravel..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
Write-Host "✅ Caché limpiada" -ForegroundColor Green
Write-Host ""

# Mostrar instrucciones
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  📝 INSTRUCCIONES DE INICIO" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Abre 3 terminales PowerShell y ejecuta:" -ForegroundColor White
Write-Host ""
Write-Host "Terminal 1 (Servidor Laravel):" -ForegroundColor Yellow
Write-Host "  php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "Terminal 2 (Servidor Reverb):" -ForegroundColor Yellow
Write-Host "  php artisan reverb:start" -ForegroundColor White
Write-Host ""
Write-Host "Terminal 3 (Vite - Assets):" -ForegroundColor Yellow
Write-Host "  npm run dev" -ForegroundColor White
Write-Host ""
Write-Host "Luego abre tu navegador en:" -ForegroundColor Yellow
Write-Host "  http://localhost:8000/websocket-demo" -ForegroundColor Cyan
Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Preguntar si quiere iniciar automáticamente
$respuesta = Read-Host "¿Deseas que inicie automáticamente los servicios? (s/n)"

if ($respuesta -eq "s" -or $respuesta -eq "S") {
    Write-Host ""
    Write-Host "🚀 Iniciando servicios..." -ForegroundColor Green
    Write-Host ""
    
    # Iniciar Laravel Server
    Write-Host "📡 Iniciando servidor Laravel en http://localhost:8000..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan serve"
    Start-Sleep -Seconds 2
    
    # Iniciar Reverb
    Write-Host "🔌 Iniciando servidor Reverb en ws://127.0.0.1:8080..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan reverb:start"
    Start-Sleep -Seconds 2
    
    # Iniciar Vite
    Write-Host "⚡ Iniciando compilador Vite..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; npm run dev"
    Start-Sleep -Seconds 3
    
    Write-Host ""
    Write-Host "✅ Servicios iniciados exitosamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "🌐 Abriendo navegador en 5 segundos..." -ForegroundColor Cyan
    Start-Sleep -Seconds 5
    
    # Abrir navegador
    Start-Process "http://localhost:8000/websocket-demo"
    
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host "  ✨ SISTEMA INICIADO CORRECTAMENTE" -ForegroundColor Green
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Para detener los servicios, cierra las terminales que se abrieron" -ForegroundColor Yellow
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "👍 OK, inicia los servicios manualmente siguiendo las instrucciones" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "Presiona cualquier tecla para salir..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
