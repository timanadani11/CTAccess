# 🧪 Comandos Útiles para Testing WebSockets

## 📋 Comandos de Verificación

### Verificar versión de Laravel
```powershell
php artisan --version
```

### Verificar que Reverb esté disponible
```powershell
php artisan list | Select-String "reverb"
```

### Verificar configuración actual
```powershell
php artisan config:show broadcasting
```

### Ver rutas del sistema
```powershell
php artisan route:list --name=websocket
```

---

## 🧹 Comandos de Limpieza

### Limpiar todas las cachés
```powershell
php artisan optimize:clear
```

### Limpiar caché de configuración
```powershell
php artisan config:clear
```

### Limpiar caché de rutas
```powershell
php artisan route:clear
```

### Limpiar caché de vistas
```powershell
php artisan view:clear
```

---

## 🔄 Reiniciar Servicios

### Reiniciar Reverb (si se quedó colgado)
```powershell
# 1. Encontrar el proceso
Get-Process php | Where-Object {$_.Path -like "*php.exe"} | Select-Object Id, ProcessName

# 2. Matar el proceso (reemplaza XXXX con el ID)
Stop-Process -Id XXXX -Force

# 3. Reiniciar
php artisan reverb:start
```

### Verificar si el puerto 8080 está en uso
```powershell
Get-NetTCPConnection -LocalPort 8080 -ErrorAction SilentlyContinue
```

### Liberar el puerto 8080 (si está ocupado)
```powershell
$processId = (Get-NetTCPConnection -LocalPort 8080).OwningProcess
Stop-Process -Id $processId -Force
```

---

## 📊 Comandos de Debugging

### Ver logs en tiempo real
```powershell
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

### Ver últimos 100 logs
```powershell
Get-Content storage/logs/laravel.log -Tail 100
```

### Buscar errores en logs
```powershell
Get-Content storage/logs/laravel.log | Select-String "ERROR"
```

### Filtrar logs de broadcasting
```powershell
Get-Content storage/logs/laravel.log | Select-String "broadcast"
```

---

## 🗄️ Comandos de Base de Datos

### Verificar conexión a la base de datos
```powershell
php artisan db:show
```

### Ver estructura de la tabla personas
```powershell
php artisan db:table personas
```

### Contar personas en la base de datos
```powershell
php artisan tinker --execute="echo Persona::count();"
```

### Ver últimas 5 personas creadas
```powershell
php artisan tinker --execute="Persona::latest()->take(5)->get()->each(fn($p) => echo $p->Nombre . PHP_EOL);"
```

---

## 🧪 Testing Manual con cURL

### Crear una persona (POST)
```powershell
$headers = @{
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

$body = @{
    documento = "999888777"
    nombre = "Test Usuario"
    tipo = "Visitante"
    correo = "test@example.com"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/websocket-demo/personas" -Method Post -Headers $headers -Body $body
```

### Listar personas (GET)
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/websocket-demo/personas" -Method Get
```

---

## 🔌 Testing WebSocket con PowerShell

### Test básico de conexión HTTP
```powershell
Test-NetConnection -ComputerName localhost -Port 8000
```

### Test de conexión WebSocket (puerto 8080)
```powershell
Test-NetConnection -ComputerName localhost -Port 8080
```

### Verificar si Reverb responde
```powershell
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -Method Get -TimeoutSec 2
    Write-Host "✅ Reverb está respondiendo" -ForegroundColor Green
} catch {
    Write-Host "❌ Reverb no responde" -ForegroundColor Red
}
```

---

## 📦 Comandos de NPM

### Verificar dependencias instaladas
```powershell
npm list laravel-echo
npm list pusher-js
```

### Reinstalar dependencias
```powershell
npm install
```

### Limpiar cache de NPM y reinstalar
```powershell
npm cache clean --force
Remove-Item node_modules -Recurse -Force
npm install
```

### Build de producción
```powershell
npm run build
```

---

## 🚀 Script de Inicio Completo (Manual)

```powershell
# Paso 1: Limpiar cachés
Write-Host "🧹 Limpiando cachés..." -ForegroundColor Yellow
php artisan optimize:clear

# Paso 2: Iniciar Laravel Server
Write-Host "📡 Iniciando Laravel Server..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan serve"
Start-Sleep -Seconds 2

# Paso 3: Iniciar Reverb
Write-Host "🔌 Iniciando Reverb..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan reverb:start"
Start-Sleep -Seconds 2

# Paso 4: Iniciar Vite
Write-Host "⚡ Iniciando Vite..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; npm run dev"
Start-Sleep -Seconds 3

# Paso 5: Abrir navegador
Write-Host "🌐 Abriendo navegador..." -ForegroundColor Yellow
Start-Process "http://localhost:8000/websocket-demo"

Write-Host "✅ Sistema iniciado correctamente" -ForegroundColor Green
```

---

## 🐛 Comandos de Resolución de Problemas

### Problema: "Port 8000 already in use"
```powershell
# Encontrar el proceso
$process = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess

# Matar el proceso
if ($process) {
    Stop-Process -Id $process -Force
    Write-Host "✅ Puerto 8000 liberado" -ForegroundColor Green
}

# Reiniciar
php artisan serve --port=8001
```

### Problema: "CSRF token mismatch"
```powershell
# Limpiar sesiones
php artisan session:clear
php artisan cache:clear

# Reiniciar navegador
```

### Problema: "Class not found"
```powershell
# Regenerar autoload
composer dump-autoload

# Limpiar cachés
php artisan optimize:clear
```

### Problema: "Connection refused [tcp://localhost:3306]"
```powershell
# Verificar MySQL
Get-Service -Name "MySQL*"

# Si está detenido, iniciarlo
Start-Service -Name "MySQL80"
```

---

## 📸 Capturar Estado del Sistema

### Información completa del sistema
```powershell
Write-Host "=== INFORMACIÓN DEL SISTEMA ===" -ForegroundColor Cyan
Write-Host ""

Write-Host "Laravel Version:" -ForegroundColor Yellow
php artisan --version

Write-Host ""
Write-Host "PHP Version:" -ForegroundColor Yellow
php --version

Write-Host ""
Write-Host "Node Version:" -ForegroundColor Yellow
node --version

Write-Host ""
Write-Host "NPM Version:" -ForegroundColor Yellow
npm --version

Write-Host ""
Write-Host "Puertos en uso:" -ForegroundColor Yellow
Get-NetTCPConnection -LocalPort 8000,8080,3306,5173 -ErrorAction SilentlyContinue | Select-Object LocalPort, State

Write-Host ""
Write-Host "Variables REVERB en .env:" -ForegroundColor Yellow
Get-Content .env | Select-String "REVERB"

Write-Host ""
Write-Host "Personas en DB:" -ForegroundColor Yellow
php artisan tinker --execute="echo 'Total: ' . \App\Models\Persona::count();"
```

---

## 🔍 Monitoreo en Tiempo Real

### Ver eventos de broadcasting en tiempo real
```powershell
# Terminal 1: Ver logs
Get-Content storage/logs/laravel.log -Wait -Tail 20 | Select-String "broadcast|event"

# Terminal 2: Crear persona de prueba
Invoke-RestMethod -Uri "http://localhost:8000/websocket-demo/personas" `
    -Method Post `
    -Headers @{"Content-Type"="application/json"} `
    -Body '{"documento":"TEST123","nombre":"Test User","tipo":"Visitante","correo":"test@test.com"}'
```

---

## 🎯 Testing de Rendimiento

### Test de carga simple (crear 10 personas)
```powershell
1..10 | ForEach-Object {
    $body = @{
        documento = "TEST$_"
        nombre = "Usuario Test $_"
        tipo = "Visitante"
        correo = "test$_@example.com"
    } | ConvertTo-Json
    
    Invoke-RestMethod -Uri "http://localhost:8000/websocket-demo/personas" `
        -Method Post `
        -Headers @{"Content-Type"="application/json"} `
        -Body $body
    
    Write-Host "✅ Persona $_ creada"
    Start-Sleep -Milliseconds 500
}
```

---

## 📊 Estadísticas

### Contar eventos enviados (revisar logs)
```powershell
$logs = Get-Content storage/logs/laravel.log
$personasCreadas = ($logs | Select-String "PersonaCreada").Count
$personasActualizadas = ($logs | Select-String "PersonaActualizada").Count

Write-Host "📊 Estadísticas de Eventos" -ForegroundColor Cyan
Write-Host "  Personas Creadas: $personasCreadas"
Write-Host "  Personas Actualizadas: $personasActualizadas"
```

---

## 🎓 Comandos Útiles de Artisan

### Listar todos los eventos registrados
```powershell
php artisan event:list
```

### Ver todos los canales de broadcasting
```powershell
php artisan route:list --name=broadcasting
```

### Generar clave de aplicación (si falta)
```powershell
php artisan key:generate
```

### Ejecutar migraciones
```powershell
php artisan migrate
```

### Ver estado de migraciones
```powershell
php artisan migrate:status
```

---

## 📝 Notas Importantes

1. **Siempre ejecuta `php artisan config:clear` después de cambiar `.env`**
2. **Reinicia Reverb si cambias eventos o canales**
3. **Reinicia Vite si cambias archivos JS**
4. **Usa `Ctrl+C` para detener servicios en PowerShell**
5. **Revisa los logs ante cualquier error**

---

## 🚨 Comandos de Emergencia

### Matar todos los procesos PHP
```powershell
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
```

### Matar todos los procesos Node
```powershell
Get-Process node -ErrorAction SilentlyContinue | Stop-Process -Force
```

### Reset completo del proyecto
```powershell
# 1. Matar procesos
Get-Process php,node -ErrorAction SilentlyContinue | Stop-Process -Force

# 2. Limpiar cachés
php artisan optimize:clear

# 3. Reinstalar dependencias
composer install
npm install

# 4. Ejecutar migraciones
php artisan migrate:fresh

# 5. Reiniciar servicios
.\start-websocket-demo.ps1
```

---

**💡 Tip:** Guarda estos comandos en un lugar accesible para debugging rápido.
