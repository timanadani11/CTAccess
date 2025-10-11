# 🎯 GUÍA RÁPIDA DE INICIO - WebSockets Demo

## ⚡ Inicio Rápido (3 pasos)

### 1️⃣ Instalar dependencias (solo primera vez)

```powershell
# Backend
composer require laravel/reverb

# Frontend
npm install --save laravel-echo pusher-js
```

### 2️⃣ Iniciar servicios

**Opción A - Script automático (Recomendado):**
```powershell
.\start-websocket-demo.ps1
```

**Opción B - Manual (3 terminales):**

**Terminal 1:**
```powershell
php artisan serve
```

**Terminal 2:**
```powershell
php artisan reverb:start
```

**Terminal 3:**
```powershell
npm run dev
```

### 3️⃣ Abrir el navegador

```
http://localhost:8000/websocket-demo
```

---

## 🧪 Probar Funcionamiento

1. Abre la URL en el navegador
2. Rellena el formulario para crear una persona
3. **Abre la misma URL en otra pestaña/navegador**
4. Crea una persona en la primera pestaña
5. **¡Verás que la segunda pestaña se actualiza automáticamente!** ✨

---

## 📁 Archivos Creados

```
✅ app/Events/PersonaCreada.php
✅ app/Events/PersonaActualizada.php
✅ app/Http/Controllers/WebSocketDemoController.php
✅ resources/js/echo.js
✅ resources/views/websocket-demo/index.blade.php
✅ routes/web.php (rutas agregadas)
✅ routes/channels.php (canal 'personas')
✅ .env.reverb.example
✅ DEMO_WEBSOCKETS_REVERB.md
✅ start-websocket-demo.ps1
✅ INICIO_RAPIDO.md (este archivo)
```

---

## 🔍 Verificar Estado

### ✅ Servidor Laravel corriendo
```powershell
curl http://localhost:8000
```

### ✅ Servidor Reverb corriendo
Deberías ver en la terminal:
```
[2025-10-11 10:30:45] Server running on 127.0.0.1:8080
```

### ✅ Assets compilados
En la terminal de Vite deberías ver:
```
VITE v7.x.x  ready in XXX ms
➜  Local:   http://localhost:5173/
```

---

## 🐛 Problemas Comunes

### ❌ "Class 'Laravel\Reverb\...' not found"
**Solución:**
```powershell
composer require laravel/reverb
php artisan reverb:install
```

### ❌ "Echo is not defined"
**Solución:**
```powershell
npm install --save laravel-echo pusher-js
npm run dev
```

### ❌ Los eventos no se emiten
**Solución:**
Verifica que en `.env`:
```env
BROADCAST_DRIVER=reverb
```

Luego reinicia Reverb:
```powershell
php artisan reverb:start
```

### ❌ Error 500 al crear persona
**Solución:**
Verifica los logs:
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

---

## 📊 Arquitectura del Sistema

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   Browser   │ ◄─────► │    Laravel   │ ◄─────► │  Database   │
│  (Vue.js)   │         │ (Controller) │         │   (MySQL)   │
└─────────────┘         └──────────────┘         └─────────────┘
       ▲                        │
       │                        ▼
       │                ┌──────────────┐
       └────────────────┤    Reverb    │
          WebSocket     │   (WS:8080)  │
                        └──────────────┘
```

### Flujo de Datos:

1. **Usuario crea persona** → POST `/websocket-demo/personas`
2. **Laravel guarda en DB** → `Persona::create()`
3. **Se dispara evento** → `event(new PersonaCreada($persona))`
4. **Reverb emite por WS** → Canal `personas`
5. **Echo lo escucha** → `.listen('.persona.creada')`
6. **Vue actualiza UI** → `this.personas.unshift(data)`

---

## 📞 URLs del Sistema

| Servicio | URL | Estado |
|----------|-----|--------|
| Laravel Server | http://localhost:8000 | ✅ Debe estar corriendo |
| WebSocket Demo | http://localhost:8000/websocket-demo | 🎯 Acceso principal |
| Reverb WS | ws://127.0.0.1:8080 | 🔌 Backend WebSocket |
| Vite Dev | http://localhost:5173 | ⚡ Solo desarrollo |

---

## 🎨 Características del Demo

✨ **Interfaz moderna** con Tailwind CSS  
🔄 **Actualización automática** en tiempo real  
📡 **Log de eventos** visible en pantalla  
🟢 **Indicador de conexión** WebSocket  
✅ **Validación de formularios** con mensajes claros  
🎯 **Resaltado de nuevos registros** (3 segundos)  
📱 **Responsive design** para móviles  

---

## 📚 Documentación Completa

Para más detalles, consulta:
- `DEMO_WEBSOCKETS_REVERB.md` - Documentación técnica completa
- `.env.reverb.example` - Ejemplo de configuración
- Código fuente en `app/Events/` y `resources/views/websocket-demo/`

---

## ✅ Checklist Final

Antes de empezar, verifica:

- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] Node.js + NPM instalado
- [ ] MySQL corriendo
- [ ] `.env` configurado
- [ ] Tabla `personas` migrada (`php artisan migrate`)
- [ ] Laravel Reverb instalado (`composer require laravel/reverb`)
- [ ] Dependencias JS instaladas (`npm install`)

---

## 🎉 ¡Listo para Probar!

```powershell
# Ejecuta el script de inicio automático
.\start-websocket-demo.ps1
```

O sigue los pasos manuales arriba. ¡Disfruta del demo! 🚀

---

**Desarrollado por:** CTAccess Team  
**Tecnologías:** Laravel 12 + Reverb + Vue.js 3 + Tailwind CSS  
**Fecha:** Octubre 2025
