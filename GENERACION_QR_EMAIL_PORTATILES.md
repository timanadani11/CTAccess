# 📧 Generación Automática de QR y Envío de Email para Portátiles

## ✨ Funcionalidad Implementada

Cuando se crea un **Nuevo Portátil** y se asigna a una persona, el sistema ahora:

1. ✅ **Genera automáticamente un código QR único** para el portátil
2. ✅ **Almacena la imagen QR** en `storage/public/qrcodes/`
3. ✅ **Envía un email** a la persona asociada con:
   - Información completa del portátil asignado
   - Código QR embebido en el email (HTML)
   - Archivo PNG del QR adjunto para descargar
   - Instrucciones de uso
   - Advertencias de seguridad

---

## 📁 Archivos Creados/Modificados

### 1. **Mailable para Portátiles**
**Archivo**: `app/Mail/PortatilAsignadoMailable.php`

```php
class PortatilAsignadoMailable extends Mailable
{
    // Envía email con información del portátil
    // Adjunta automáticamente el PNG del QR
    // Carga la relación con la persona
}
```

**Características**:
- ✅ Genera email con asunto "Portátil Asignado - CTAccess"
- ✅ Adjunta el archivo PNG del QR automáticamente
- ✅ Nombre del archivo: `qr_portatil_{serial}.png`

---

### 2. **Plantilla de Email Profesional**
**Archivo**: `resources/views/emails/portatil_asignado.blade.php`

**Características del diseño**:
- ✅ **Responsive** y compatible con todos los clientes de email
- ✅ **Colores corporativos** SENA (verde #39A900)
- ✅ **Header atractivo** con degradado
- ✅ **Información detallada** del portátil (serial, marca, modelo, fecha)
- ✅ **Código QR grande** (250x250px) con borde y sombra
- ✅ **Instrucciones claras** de uso
- ✅ **Alerta de seguridad** resaltada
- ✅ **Footer profesional** con branding

**Contenido incluido**:
- 📋 Información del portátil (serial, marca, modelo, fecha)
- 🔐 Código QR único visualizado
- 📌 Instrucciones paso a paso
- ⚠️ Advertencia de responsabilidad
- 💻 Diseño profesional con iconos

---

### 3. **Controlador Actualizado**
**Archivo**: `app/Http/Controllers/System/Admin/PortatilesController.php`

#### **Método `store()` mejorado**:
```php
public function store(Request $request)
{
    // 1. Validar datos
    $validated = $request->validate([...]);
    
    // 2. Generar QR automáticamente
    $qrPath = $this->generateQrForPortatil($validated['serial']);
    $validated['qrCode'] = $qrPath;
    
    // 3. Crear portátil con QR
    $portatil = Portatil::create($validated);
    
    // 4. Enviar email a la persona
    if ($persona && $persona->correo) {
        Mail::to($persona->correo)->send(new PortatilAsignadoMailable($portatil));
    }
    
    return back()->with('success', 'Portátil registrado y QR enviado por email');
}
```

#### **Nuevo método `generateQrForPortatil()`**:
```php
protected function generateQrForPortatil(string $serial): string
{
    // Formato: PORTATIL_{serial}
    $qrContent = 'PORTATIL_' . $serial;
    
    // Generar QR usando API externa
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrContent);
    
    // Descargar y guardar en storage/public/qrcodes/
    Storage::disk('public')->put($path, $response->body());
    
    // Retornar URL pública /storage/qrcodes/portatil_...png
    return Storage::url($path);
}
```

---

## 🔄 Flujo Completo

### Cuando el administrador crea un portátil:

```
1. 👤 Admin abre modal "Nuevo Portátil"
         ↓
2. 📝 Completa el formulario:
   - Selecciona Persona
   - Ingresa Serial: VJHYV6IV
   - Ingresa Marca: DELL
   - Ingresa Modelo: LATITUDE
         ↓
3. 💾 Click en "Guardar"
         ↓
4. ⚙️ Backend procesa:
   a) Valida datos
   b) Genera QR: "PORTATIL_VJHYV6IV"
   c) Descarga imagen QR (300x300px)
   d) Guarda en: storage/public/qrcodes/portatil_vjhyv6iv_abc123.png
   e) Crea registro en BD con qrCode = "/storage/qrcodes/portatil_vjhyv6iv_abc123.png"
         ↓
5. 📧 Envía email a juan@empresa.com:
   - Asunto: "Portátil Asignado - CTAccess"
   - HTML hermoso con toda la info
   - QR embebido en el cuerpo del email
   - PNG adjunto: qr_portatil_VJHYV6IV.png
         ↓
6. ✅ Muestra mensaje: "Portátil registrado exitosamente y QR enviado por email"
```

---

## 📧 Ejemplo del Email Recibido

### Asunto:
```
Portátil Asignado - CTAccess
```

### Contenido:
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
💻 Portátil Asignado
Sistema de Control de Acceso
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Hola Juan Pérez Test,

Se te ha asignado un nuevo portátil en el sistema CTAccess.
A continuación encontrarás toda la información relevante y 
tu código QR único para este equipo.

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ 📋 Información del Portátil      ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ 🔢 Serial:  VJHYV6IV             ┃
┃ 🏷️ Marca:   DELL                 ┃
┃ 📱 Modelo:  LATITUDE             ┃
┃ 📅 Asignado: 14/10/2025 19:19    ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃   🔐 Tu Código QR Único          ┃
┃                                  ┃
┃   [Imagen QR 250x250px]          ┃
┃                                  ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

📌 Instrucciones de Uso:
• Descarga el código QR adjunto o guarda esta imagen
• Puedes imprimirlo y pegarlo en tu portátil
• O guarda la imagen en tu teléfono móvil
• Presenta este código al celador cuando ingreses o salgas
• El sistema verificará automáticamente que el portátil está asignado a ti

⚠️ Importante:
Este portátil está registrado a tu nombre. Eres responsable 
de su uso y custodia. Reporta cualquier pérdida o daño 
inmediatamente al área de sistemas.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CTAccess v2.0 • SENA
Sistema de Control de Acceso
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📎 Adjunto: qr_portatil_VJHYV6IV.png
```

---

## 🧪 Cómo Probar

### 1. **Asegúrate que el correo de la persona esté configurado**
```
Juan Pérez Test debe tener un email válido en su perfil
```

### 2. **Crear un nuevo portátil**:
```
1. Ir a: Sistema → Administrador → Gestión de Portátiles
2. Click en "Nuevo Portátil"
3. Seleccionar: Juan Pérez Test
4. Ingresar Serial: VJHYV6IV
5. Marca: DELL
6. Modelo: LATITUDE
7. Click "Guardar"
```

### 3. **Verificar**:
```
✅ Modal se cierra
✅ Mensaje de éxito aparece
✅ Portátil aparece en la lista con QR generado
✅ Email llega al correo de la persona
✅ Email contiene toda la información
✅ Archivo PNG está adjunto
```

---

## 🔍 Verificación en Base de Datos

```sql
-- Ver el portátil creado con QR
SELECT 
    portatil_id,
    persona_id,
    serial,
    qrCode,
    marca,
    modelo,
    created_at
FROM portatiles
WHERE serial = 'VJHYV6IV';

-- Resultado esperado:
-- qrCode: /storage/qrcodes/portatil_vjhyv6iv_abc123.png
```

---

## 📂 Estructura de Archivos QR

```
storage/
└── app/
    └── public/
        └── qrcodes/
            ├── persona_1234567890_xyz789.png     ← QR de personas
            ├── portatil_vjhyv6iv_abc123.png      ← QR de portátiles  ✨ NUEVO
            ├── portatil_serial123_def456.png     ← Más portátiles
            └── ...

public/
└── storage/ → symlink a storage/app/public/
```

**Acceso público**: `http://127.0.0.1:8000/storage/qrcodes/portatil_vjhyv6iv_abc123.png`

---

## ⚙️ Configuración de Email (si no funciona)

Verifica el archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="CTAccess SENA"
```

### Para Gmail:
1. Activar "Acceso de apps menos seguras" (no recomendado), o
2. Generar "Contraseña de aplicación" en Google Account Security

### Para desarrollo local:
Usa **Mailtrap** o **MailHog** para probar emails sin enviarlos realmente.

---

## 🐛 Troubleshooting

### **Problema**: El email no llega
**Solución**:
1. Verificar que la persona tenga email configurado
2. Revisar configuración MAIL en `.env`
3. Ver logs: `storage/logs/laravel.log`
4. Ejecutar: `php artisan config:clear`

### **Problema**: El QR no se genera
**Solución**:
1. Verificar conexión a internet (usa API externa)
2. Verificar permisos de `storage/app/public/qrcodes/`
3. Ver logs del sistema
4. Probar manualmente: `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=PORTATIL_TEST`

### **Problema**: La imagen del QR no se muestra en el email
**Solución**:
1. Verificar que existe el archivo en `storage/app/public/qrcodes/`
2. Verificar symlink: `php artisan storage:link`
3. Algunos clientes de email bloquean imágenes externas

---

## 📊 Formato del Código QR

### Para Personas:
```
PERSONA_{documento}
Ejemplo: PERSONA_1234567890
```

### Para Portátiles:
```
PORTATIL_{serial}
Ejemplo: PORTATIL_VJHYV6IV
```

### Para Vehículos (futuro):
```
VEHICULO_{placa}
Ejemplo: VEHICULO_ABC123
```

---

## ✅ Ventajas de esta Implementación

1. ✅ **Automático**: No requiere intervención manual
2. ✅ **Completo**: Genera QR + envía email en un solo paso
3. ✅ **Profesional**: Email con diseño corporativo
4. ✅ **Funcional**: PNG adjunto listo para imprimir
5. ✅ **Robusto**: Maneja errores sin fallar la creación
6. ✅ **Trazable**: Logs de errores para debugging
7. ✅ **Escalable**: Fácil de extender a vehículos
8. ✅ **User-friendly**: Instrucciones claras en el email

---

## 🚀 Próximas Mejoras (Opcional)

### 1. **Regenerar QR**
Agregar botón para regenerar QR de un portátil existente

### 2. **Email de Actualización**
Enviar email cuando se modifica la asignación de un portátil

### 3. **Email de Desasignación**
Notificar cuando se elimina o reasigna un portátil

### 4. **QR para Vehículos**
Implementar lo mismo para vehículos

### 5. **Descarga Masiva de QR**
Descargar todos los QR de una persona en un ZIP

### 6. **Impresión Directa**
Vista optimizada para imprimir QR con información del equipo

---

## 📝 Logs del Sistema

### Email enviado exitosamente:
```
[2025-10-14 19:19:23] local.INFO: Email de portátil asignado enviado a juan@empresa.com
```

### Error al enviar email (no crítico):
```
[2025-10-14 19:19:23] local.WARNING: No se pudo enviar el email de asignación de portátil: Connection timeout
```

### Error generando QR:
```
[2025-10-14 19:19:23] local.ERROR: Error generando QR para portátil: Failed to connect to API
```

---

## 🎉 Resultado Final

Cuando creas un portátil:

1. ✅ Se guarda en la base de datos
2. ✅ Se genera QR automáticamente
3. ✅ Se almacena imagen PNG
4. ✅ Se envía email hermoso con toda la info
5. ✅ La persona puede imprimir o guardar el QR
6. ✅ El celador puede escanear el QR para registrar accesos

**Todo automatizado en un solo click!** 🚀

---

**Fecha de implementación**: 14/10/2025  
**Versión**: CTAccess v2.0  
**Estado**: ✅ Completamente funcional
