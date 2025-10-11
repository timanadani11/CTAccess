# 🚀 Guía de Inicio Rápido - CTAccess con WebSockets

## ⚡ Comandos para Iniciar el Sistema

### **Ejecuta estos 3 comandos en terminales SEPARADAS:**

#### Terminal 1 - Servidor Laravel
```powershell
php artisan serve
```
✅ Debe mostrar: `Server running on [http://127.0.0.1:8000]`

---

#### Terminal 2 - Servidor Reverb (WebSockets)
```powershell
php artisan reverb:start
```
✅ Debe mostrar: `Server running on 127.0.0.1:8080`

---

#### Terminal 3 - Compilador Vite
```powershell
npm run dev
```
✅ Debe mostrar: `VITE v7.x.x ready in XXX ms`

---

## 🔍 Verificar que Todo Funciona

### 1. Verificar Reverb (WebSocket)
```powershell
Get-NetTCPConnection -LocalPort 8080 -ErrorAction SilentlyContinue
```
✅ Debe mostrar: `LocalPort: 8080, State: Listen`

### 2. Abrir el Sistema
- **Home**: http://localhost:8000
- **Login Sistema**: http://localhost:8000/system/login

### 3. Consola del Navegador (F12)
Deberías ver:
```
🚀 Laravel Echo configurado con Reverb
📡 WebSocket Server: ws://localhost:8080
✅ Conectado al servidor WebSocket
```

---

## 🐛 Si hay Errores

### Error: "ERR_CONNECTION_REFUSED" en WebSocket

**Causa:** Reverb no está corriendo

**Solución:**
```powershell
php artisan reverb:start
```

---

### Error: "Port 8080 already in use"

**Solución 1 - Liberar el puerto:**
```powershell
$process = Get-NetTCPConnection -LocalPort 8080 | Select-Object -ExpandProperty OwningProcess
Stop-Process -Id $process -Force
```

**Solución 2 - Cambiar puerto:**
En `.env`:
```
REVERB_PORT=8081
VITE_REVERB_PORT=8081
```
Luego:
```powershell
php artisan config:clear
npm run dev  # Reiniciar Vite
php artisan reverb:start
```

---

### Error: Variables de entorno no se actualizan

**Solución:**
```powershell
# 1. Limpiar caché
php artisan config:clear

# 2. Reiniciar Vite (Ctrl+C y luego)
npm run dev

# 3. Recargar navegador (F5)
```

---

## 🧪 Probar WebSockets

1. Abre el **Home**: http://localhost:8000
2. En otra pestaña abre: http://localhost:8000/system/login
3. Inicia sesión como celador
4. Ve a **QR Scanner**
5. Registra un acceso
6. **¡Mira el Home!** → Debe actualizarse automáticamente

---

## 📊 Monitorear en Tiempo Real

### Ver logs de Laravel:
```powershell
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

### Ver jobs en background:
```powershell
Get-Job
```

### Detener todos los jobs:
```powershell
Get-Job | Stop-Job
Get-Job | Remove-Job
```

---

## ✅ Checklist Antes de Empezar

- [ ] Node.js y NPM instalados
- [ ] Composer instalado
- [ ] MySQL corriendo
- [ ] Puerto 8000 libre
- [ ] Puerto 8080 libre
- [ ] Variables en `.env` configuradas
- [ ] `php artisan migrate` ejecutado

---

## 🎯 Orden de Inicio Recomendado

```
1. Terminal → php artisan reverb:start
   (Espera ver: "Server running on 127.0.0.1:8080")

2. Terminal → php artisan serve
   (Espera ver: "Server running on [http://127.0.0.1:8000]")

3. Terminal → npm run dev
   (Espera ver: "VITE ready")

4. Navegador → http://localhost:8000
   (F12 → Console → Verifica conexión WebSocket)
```

---

## 💡 Tips

- **Siempre inicia Reverb primero** antes de abrir el navegador
- **Recarga la página (F5)** después de cambios en archivos JS
- **Limpia caché** si cambias variables de `.env`
- **Revisa la consola del navegador (F12)** para ver errores de WebSocket

---

**🚀 ¡Listo para usar CTAccess con WebSockets en Tiempo Real!**
