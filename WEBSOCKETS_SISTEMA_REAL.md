# 🚀 WebSockets en Tiempo Real - Sistema de Accesos CTAccess

## 📋 Descripción

Sistema de actualización en tiempo real de accesos usando **Laravel 12 + Reverb**. Cuando un celador registra un acceso (entrada/salida), el Home se actualiza automáticamente sin necesidad de refrescar la página.

---

## ✨ Funcionalidades Implementadas

- ✅ **Eventos en Tiempo Real**: Al registrar un acceso, se emite un evento WebSocket
- ✅ **Home Actualizado**: La sección "Actividad Reciente" se actualiza automáticamente
- ✅ **Estadísticas en Vivo**: Los contadores de accesos se incrementan en tiempo real
- ✅ **Sin Refrescar**: Todo ocurre sin recargar la página
- ✅ **Múltiples Usuarios**: Todos los usuarios conectados ven las actualizaciones

---

## 🏗️ Arquitectura del Sistema

```
Celador Registra Acceso (QR Scanner)
            ↓
QrController::registrarAcceso()
            ↓
Acceso::registrarEntrada() / marcarSalida()
            ↓
event(new AccesoRegistrado($acceso))
            ↓
Laravel Broadcasting → Reverb (WS:8080)
            ↓
Echo.channel('accesos').listen('.acceso.registrado')
            ↓
Home.vue actualiza actividad reciente
            ↓
✨ Todos los usuarios ven el nuevo acceso
```

---

## 📂 Archivos Modificados/Creados

### **Backend**

1. **`app/Events/AccesoRegistrado.php`** (NUEVO)
   - Evento que se emite al registrar un acceso
   - Canal: `accesos` (público)
   - Datos: persona, hora, tipo (entrada/salida), estado

2. **`app/Http/Controllers/System/Celador/QrController.php`** (MODIFICADO)
   - Agregado: `use App\Events\AccesoRegistrado`
   - Agregado: `event(new AccesoRegistrado($acceso))` en 3 lugares:
     - Al registrar entrada
     - Al registrar salida exitosa
     - Al registrar salida con incidencia

3. **`routes/channels.php`** (MODIFICADO)
   - Canal público `accesos` definido

4. **`routes/api.php`** (MODIFICADO)
   - Endpoint: `GET /api/accesos/recientes`
   - Retorna últimos 10 accesos del día

### **Frontend**

5. **`resources/js/Pages/Home.vue`** (MODIFICADO)
   - Agregado: Escucha del canal `accesos`
   - Agregado: Event listener `.acceso.registrado`
   - Modificado: `fetchRecentActivity()` usa API real
   - Actualización automática de estadísticas

6. **`resources/js/app.js`** (MODIFICADO)
   - Agregado: `import './echo'` para cargar WebSockets

7. **`resources/js/echo.js`** (YA EXISTÍA)
   - Configuración de Laravel Echo con Reverb

---

## 🔧 Configuración Requerida

### **Variables de Entorno (`.env`)**

Tu archivo `.env` ya está configurado correctamente:

```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=local
REVERB_APP_KEY=testkey
REVERB_APP_SECRET=testsecret

REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 🚀 Cómo Ejecutar

### **Paso 1: Iniciar Servicios**

Necesitas **3 terminales** corriendo simultáneamente:

**Terminal 1 - Servidor Laravel:**
```powershell
php artisan serve
```

**Terminal 2 - Servidor Reverb (WebSocket):**
```powershell
php artisan reverb:start
```

**Terminal 3 - Compilador Vite:**
```powershell
npm run dev
```

### **Paso 2: Abrir el Sistema**

1. Abre el **Home**: `http://localhost:8000`
2. Abre el **Panel de Celador** (en otra pestaña o dispositivo): `http://localhost:8000/system/celador/qr`
3. Registra un acceso desde el panel del celador
4. **¡Mira el Home!** → Se actualiza automáticamente ✨

---

## 🧪 Cómo Probar

### **Test 1: Registro de Entrada**

1. Abre `http://localhost:8000` (Home)
2. Abre `http://localhost:8000/system/login` en otra pestaña
3. Inicia sesión como celador
4. Ve a la sección QR Scanner
5. Registra una entrada escaneando el QR de una persona
6. **Verifica:** El Home se actualiza automáticamente con el nuevo acceso

### **Test 2: Múltiples Usuarios**

1. Abre el Home en **2 navegadores diferentes** (o modo incógnito)
2. En uno de ellos, inicia sesión como celador y registra un acceso
3. **Verifica:** Ambos navegadores se actualizan automáticamente

### **Test 3: Estadísticas en Tiempo Real**

1. Abre el Home y observa las estadísticas (Accesos Hoy, Activos, etc.)
2. Registra un acceso desde el panel de celador
3. **Verifica:** Los números se actualizan sin refrescar

---

## 🔍 Debugging

### **Verificar que Reverb está corriendo**

En la terminal donde ejecutas `php artisan reverb:start` deberías ver:

```
  INFO  Server running on 127.0.0.1:8080.
```

### **Ver Eventos en el Navegador**

1. Abre el Home (`http://localhost:8000`)
2. Abre la consola del navegador (F12 → Console)
3. Deberías ver:
   ```
   🚀 Laravel Echo configurado con Reverb
   📡 WebSocket Server: http://127.0.0.1:8080
   ✅ Conectado al servidor WebSocket
   ```

4. Al registrar un acceso, deberías ver:
   ```
   🎉 Nuevo acceso registrado: {id: 123, persona: {...}, ...}
   ```

### **Ver Logs en Backend**

```powershell
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

Busca líneas como:
```
[2025-10-11 10:30:45] local.INFO: Broadcasting [App\Events\AccesoRegistrado]
```

### **Problemas Comunes**

**❌ "Echo is not defined"**
- **Solución:** Asegúrate de que `npm run dev` esté corriendo
- Verifica que `resources/js/echo.js` exista
- Verifica que `resources/js/app.js` tenga `import './echo'`

**❌ "No se conecta al WebSocket"**
- **Solución:** Verifica que Reverb esté corriendo: `php artisan reverb:start`
- Verifica el puerto en `.env`: `REVERB_PORT=8080`
- Asegúrate de que el puerto 8080 no esté ocupado

**❌ "Los eventos no se reciben"**
- **Solución:** Limpia la caché: `php artisan config:clear`
- Reinicia Reverb: `Ctrl+C` y luego `php artisan reverb:start`
- Verifica que el canal esté definido en `routes/channels.php`

---

## 📊 Datos del Evento

Cuando se emite el evento `AccesoRegistrado`, se envían estos datos:

```javascript
{
  id: 123,                    // ID del acceso
  persona: {
    id: 456,                  // ID de la persona
    nombre: "Juan Pérez",     // Nombre completo
    documento: "123456789",   // Documento
    tipo: "Empleado"          // Tipo de persona
  },
  hora_entrada: "08:30:15",   // Hora de entrada
  hora_salida: "17:45:20",    // Hora de salida (null si es entrada)
  estado: "activo",           // Estado del acceso
  tipo_acceso: "entrada",     // "entrada" o "salida"
  timestamp: "2025-10-11T08:30:15.000000Z" // Timestamp ISO 8601
}
```

---

## 🎨 Visualización en Home

### **Sección "Actividad Reciente"**

Muestra los últimos 10 accesos del día con:
- 🟢 **Punto verde**: Entrada
- 🔴 **Punto rojo**: Salida
- Nombre de la persona
- Tiempo relativo (ej: "2m", "1h", "3d")

### **Actualización Automática**

Cuando se registra un nuevo acceso:
1. Se agrega al inicio de la lista
2. Aparece con animación sutil
3. Se mantiene solo los últimos 10
4. Las estadísticas se actualizan

---

## 🔐 Seguridad

### **Canal Público**

Actualmente el canal `accesos` es **público**, lo que significa que cualquier usuario puede escuchar los eventos sin autenticación.

### **Migrar a Canal Privado (Opcional)**

Si deseas que solo usuarios autenticados vean los accesos:

**1. Modificar `routes/channels.php`:**
```php
Broadcast::channel('accesos', function ($user) {
    return $user !== null; // Solo usuarios autenticados
});
```

**2. Modificar `Home.vue`:**
```javascript
// Cambiar de:
window.Echo.channel('accesos')

// A:
window.Echo.private('accesos')
```

---

## 📈 Rendimiento

### **Optimizaciones Aplicadas**

- ✅ Solo se cargan los últimos 10 accesos
- ✅ Solo se emiten datos necesarios (no todo el modelo)
- ✅ Uso de `with()` para evitar N+1 queries
- ✅ Límite de 10 accesos en el frontend

### **Escalabilidad**

- **Hasta 100 usuarios conectados**: Sin problemas con la configuración actual
- **100-500 usuarios**: Considera usar Redis para broadcasting
- **500+ usuarios**: Implementa load balancing de Reverb

---

## 🎯 Casos de Uso Reales

### **1. Monitoreo de Seguridad**

El personal de seguridad puede tener abierto el Home en una pantalla y ver en tiempo real todos los accesos sin hacer nada.

### **2. Dashboard Centralizado**

Múltiples monitores pueden mostrar el Home simultáneamente, todos sincronizados en tiempo real.

### **3. Alertas Visuales**

Se pueden agregar notificaciones visuales/sonoras cuando se registre un acceso de tipo específico (ej: Visitante).

---

## 🚀 Próximos Pasos Sugeridos

### **1. Notificaciones de Incidencias**

Crear evento `IncidenciaRegistrada` para alertas en tiempo real.

### **2. Estado de Celadores**

Mostrar qué celadores están activos en su turno.

### **3. Gráficos en Tiempo Real**

Actualizar gráficos de actividad semanal con cada nuevo acceso.

### **4. Filtros Dinámicos**

Permitir filtrar por tipo de persona, área, etc.

---

## ✅ Checklist de Verificación

- [x] Evento `AccesoRegistrado` creado
- [x] QrController emite eventos
- [x] Canal `accesos` definido
- [x] API `/api/accesos/recientes` funcional
- [x] Home.vue escucha eventos
- [x] Echo.js configurado
- [x] Variables `.env` correctas
- [x] Demo eliminado

---

## 📞 Soporte

### **Ver Estado de Conexión**

En la consola del navegador (F12):
```javascript
// Verificar si Echo está disponible
window.Echo

// Verificar conexiones activas
window.Echo.connector.pusher.connection.state
```

### **Forzar Reconexión**

```javascript
window.Echo.connector.pusher.disconnect()
window.Echo.connector.pusher.connect()
```

---

## 🎉 Conclusión

Tu sistema ahora tiene **WebSockets en tiempo real** completamente funcional con tu base de datos real y tus modelos existentes. 

**Para probar:**
1. Inicia los 3 servicios (Laravel, Reverb, Vite)
2. Abre el Home
3. Registra un acceso desde el panel de celador
4. **¡Mira la magia! ✨**

---

**🚀 Sistema:** CTAccess - Control de Acceso en Tiempo Real  
**📅 Fecha:** 11 de Octubre, 2025  
**💻 Stack:** Laravel 12 + Reverb + Inertia.js + Vue.js 3
