# 🔄 Flujo de Acceso Mejorado - Control Automático de Portátiles y Vehículos

## 📋 Resumen de Mejoras Implementadas

### ✅ Problema Resuelto
Anteriormente, el sistema registraba accesos **SIN** guardar los IDs de portátil y vehículo asociados a la persona. Ahora el sistema funciona correctamente con detección automática y verificación en salida.

---

## 🚪 FLUJO DE ENTRADA (Persona ingresa)

### 1️⃣ Celador escanea QR de persona O ingresa cédula manualmente
```
Opciones:
✓ Botón "Escanear QR" → Escanea QR físico de la persona
✓ Botón "Entrada Manual" → Digita número de cédula
```

### 2️⃣ Sistema busca a la persona y detecta AUTOMÁTICAMENTE:
```php
// Backend busca relaciones automáticamente
$persona->load(['portatiles', 'vehiculos']);

// Si tiene portátil asociado → Obtiene el ID automáticamente
$portatilId = $persona->portatiles->first()?->portatil_id;

// Si tiene vehículo asociado → Obtiene el ID automáticamente  
$vehiculoId = $persona->vehiculos->first()?->id;
```

### 3️⃣ Sistema registra entrada CON los datos automáticos:
```php
Acceso::create([
    'persona_id' => $persona->idPersona,
    'portatil_id' => $portatilId,    // ✅ Se guarda automáticamente
    'vehiculo_id' => $vehiculoId,    // ✅ Se guarda automáticamente
    'fecha_entrada' => now(),
    'estado' => 'activo'
]);
```

### 4️⃣ Celador ve confirmación:
```
✅ Entrada registrada exitosamente
👤 Juan Pérez - Documento: 123456789
⏰ Hora: 08:30:15
💻 Portátil: Sí (Dell Latitude - Serial: ABC123)
🚗 Vehículo: Sí (Automóvil - Placa: XYZ789)
```

---

## 🚪 FLUJO DE SALIDA (Persona sale)

### 1️⃣ Celador escanea QR de persona O ingresa cédula
```
Igual que entrada - Escanea QR o digita cédula
```

### 2️⃣ Sistema detecta que tiene acceso activo (ES SALIDA)
```php
$accesoActivo = $persona->getAccesoActivo();

if ($accesoActivo) {
    // ES SALIDA - Verificar portátil y vehículo
}
```

### 3️⃣ Sistema REQUIERE verificación de portátil/vehículo:

#### 🔍 Si entró CON PORTÁTIL:
```
⚠️ VERIFICACIÓN REQUERIDA
📱 Debe escanear QR del portátil que registró en entrada
   Serial esperado: ABC123
```

**Celador DEBE:**
- Escanear QR del portátil físico
- El sistema verifica: `portatil_escaneado.id == acceso_activo.portatil_id`

#### 🔍 Si entró CON VEHÍCULO:
```
⚠️ VERIFICACIÓN REQUERIDA  
🚗 Debe escanear QR del vehículo que registró en entrada
   Placa esperada: XYZ789
```

**Celador DEBE:**
- Escanear QR del vehículo físico
- El sistema verifica: `vehiculo_escaneado.id == acceso_activo.vehiculo_id`

### 4️⃣ Verificación exitosa:
```
✅ Salida registrada exitosamente
👤 Juan Pérez
⏰ Entrada: 08:30 | Salida: 17:45
⏱️ Duración: 9h 15m
✅ Verificaciones OK
```

### 5️⃣ Verificación fallida (INCIDENCIA):
```
⚠️ INCIDENCIA DETECTADA - Salida bloqueada

Errores detectados:
❌ El portátil escaneado NO coincide
   - Entró con: ABC123
   - Escaneó: XYZ999

🚫 La salida NO se registró
📋 Se creó incidencia #45
🔔 Requiere autorización del supervisor
```

---

## 🎯 Casos de Uso Detallados

### Caso 1: Persona sin portátil ni vehículo
```
ENTRADA:
✓ Escanea QR persona → Entrada registrada
  portatil_id = NULL
  vehiculo_id = NULL

SALIDA:  
✓ Escanea QR persona → Salida registrada inmediatamente
  (No requiere verificaciones adicionales)
```

### Caso 2: Persona con portátil solamente
```
ENTRADA:
✓ Escanea QR persona → Entrada registrada
  portatil_id = 5 (automático)
  vehiculo_id = NULL

SALIDA:
1. Escanea QR persona → Sistema detecta portátil en entrada
2. ⚠️ "Debe escanear QR del portátil: Dell ABC123"
3. Escanea QR portátil → Sistema verifica coincidencia
4. ✅ Salida registrada
```

### Caso 3: Persona con portátil Y vehículo
```
ENTRADA:
✓ Escanea QR persona → Entrada registrada
  portatil_id = 5 (automático)
  vehiculo_id = 8 (automático)

SALIDA:
1. Escanea QR persona → Sistema detecta portátil y vehículo
2. ⚠️ "Debe escanear QR del portátil Y del vehículo"
3. Escanea QR portátil → ✓ Verificado
4. Escanea QR vehículo → ✓ Verificado  
5. ✅ Salida registrada
```

### Caso 4: Incidencia - Portátil diferente
```
ENTRADA:
✓ Portátil ABC123 registrado (portatil_id = 5)

SALIDA:
1. Escanea QR persona
2. Escanea QR portátil XYZ999 (portatil_id = 12)
3. ❌ Sistema detecta inconsistencia
4. 🚫 Salida BLOQUEADA
5. 📋 Incidencia registrada automáticamente
6. 🔔 Notificación al supervisor
```

---

## 🔧 Archivos Modificados

### Backend (PHP)
```
app/Http/Controllers/System/Celador/QrController.php
├── procesarEntrada()      → Obtiene portátil/vehículo automático
├── procesarSalida()       → Verifica coincidencia obligatoria
└── formatearRespuestaPersona() → Envía info completa al frontend
```

### Frontend (Vue.js)
```
resources/js/Pages/System/Celador/Qr/Index.vue
└── buscarPersona()        → Muestra alertas de verificación

resources/js/Components/QrScanner.vue
└── handleQrScanned()      → Procesa QR escaneados

resources/js/Components/CedulaModal.vue
└── handleCedulaSubmit()   → Entrada manual con mismo formato
```

---

## 📊 Base de Datos

### Tabla `accesos` - Ahora se llenan correctamente:
```sql
CREATE TABLE accesos (
    idAcceso INT PRIMARY KEY,
    persona_id INT NOT NULL,           -- ✅ Siempre se llena
    portatil_id INT NULL,              -- ✅ Se llena si tiene portátil
    vehiculo_id INT NULL,              -- ✅ Se llena si tiene vehículo
    fecha_entrada DATETIME NOT NULL,
    fecha_salida DATETIME NULL,
    usuario_entrada_id INT NOT NULL,
    usuario_salida_id INT NULL,
    estado ENUM('activo', 'finalizado')
);
```

### Ejemplo de registro completo:
```
idAcceso: 3
persona_id: 5
portatil_id: 12        ← ✅ YA NO queda NULL
vehiculo_id: 8         ← ✅ YA NO queda NULL  
fecha_entrada: 2025-10-07 23:47:18
fecha_salida: NULL
estado: activo
```

---

## 🎨 Interfaz para el Celador

### Mensajes visuales en entrada:
```
✅ Entrada registrada exitosamente

👤 María González - Doc: 987654321
⏰ 08:30:15

Elementos detectados:
💻 Portátil: HP EliteBook (Serial: HP789)
🚗 Vehículo: Motocicleta - Placa: ABC123
```

### Mensajes visuales en salida (requiere verificación):
```
⚠️ SALIDA - Verificación requerida

👤 María González tiene acceso activo desde 08:30

Debe escanear:
📱 Portátil: HP EliteBook (Serial: HP789)
🚗 Vehículo: Motocicleta - Placa: ABC123

[Botón: Escanear QR Portátil]
[Botón: Escanear QR Vehículo]
```

---

## 🚀 Ventajas del Nuevo Sistema

1. **✅ Automático**: No requiere escanear portátil/vehículo en ENTRADA
2. **🔒 Seguro**: Verifica obligatoriamente en SALIDA  
3. **📋 Trazable**: Registra incidencias automáticamente
4. **⚡ Rápido**: Entrada en 1 solo paso (escanear persona)
5. **🎯 Preciso**: Evita errores humanos en registro manual
6. **📱 PWA Ready**: Funciona en dispositivos móviles

---

## 📞 Soporte

Si tienes dudas sobre el nuevo flujo, contacta al administrador del sistema.

**Última actualización**: 2025-10-07
