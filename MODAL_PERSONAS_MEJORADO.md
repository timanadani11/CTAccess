# Modal de Personas Mejorado ✨

## Cambios Implementados

### 🎯 Objetivo
Simplificar el modal de creación/edición de personas para que:
- Use solo los campos reales de la base de datos
- Genere códigos QR automáticamente
- Envíe el QR por correo electrónico
- Sea compacto y optimizado para PWA

---

## 📋 Campos del Formulario

### Campos Requeridos (*)
1. **Nombre Completo** - `nombre`
2. **Documento de Identidad** - `documento`
3. **Tipo de Persona** - `tipoPersona`

### Campos Opcionales
4. **Correo Electrónico** - `correo` (se enviará QR si se proporciona)

### Tipos de Persona Disponibles
- Aprendiz
- Instructor
- Empleado
- Contratista
- Visitante

---

## 🔧 Backend - Cambios en el Controlador

**Archivo**: `app/Http/Controllers/System/Admin/PersonasController.php`

### Funcionalidades Implementadas:

#### 1. **Método `store()`** - Crear Nueva Persona
```php
- Valida 4 campos (3 requeridos + correo opcional)
- Genera QR automáticamente usando el documento
- Almacena imagen QR en storage/public/qrcodes/
- Envía correo con QR adjunto si se proporciona email
- Formato QR: "PERSONA_[documento]"
```

#### 2. **Método `update()`** - Actualizar Persona
```php
- Valida los mismos campos
- Regenera QR si el documento cambia
- Mantiene el QR existente si el documento no cambia
```

#### 3. **Método `generateAndStoreQr()`** - Generar Código QR
```php
- Usa API externa: https://api.qrserver.com/
- Genera imagen PNG de 300x300px
- Almacena en storage/public/qrcodes/
- Retorna URL pública: /storage/qrcodes/...png
```

---

## 🎨 Frontend - Cambios en el Modal

**Archivo**: `resources/js/Pages/System/Admin/Personas.vue`

### Características del Modal:

#### Diseño Compacto PWA-Ready
- ✅ Modal más pequeño (`max-width="md"`)
- ✅ Espaciado reducido (`space-y-3`)
- ✅ Touch-manipulation en todos los controles
- ✅ Iconos visuales en cada campo
- ✅ Placeholders descriptivos
- ✅ InputMode optimizado (numeric, email)

#### Campos del Formulario:
```vue
1. Nombre Completo
   - Placeholder: "Ej: Juan Pérez García"
   - Ícono: user

2. Documento de Identidad
   - Placeholder: "Ej: 12345678"
   - InputMode: numeric (teclado numérico en móvil)
   - Ícono: credit-card

3. Tipo de Persona
   - Select con 5 opciones
   - Ícono: users

4. Correo Electrónico
   - Placeholder: "correo@ejemplo.com"
   - InputMode: email (teclado email en móvil)
   - Ícono: mail
   - Nota informativa: "Se enviará un QR por correo si se proporciona"
```

#### Estados del Formulario:
- **Validación**: Los 3 primeros campos son requeridos
- **Processing**: Muestra "Guardando..." con spinner
- **Errores**: Se muestran debajo de cada campo en rojo
- **Disabled**: Botón guardar deshabilitado si faltan campos requeridos

---

## 📧 Sistema de Correo

### Email Template
**Archivo**: `resources/views/emails/persona_qr.blade.php`

El correo incluye:
- Saludo personalizado con el nombre
- QR de la persona embebido en HTML
- QR adjunto como archivo PNG descargable
- Diseño responsive y profesional

### Clase Mailable
**Archivo**: `app/Mail/PersonaQrMailable.php`

- Adjunta automáticamente el archivo QR PNG
- Carga la relación de portátiles (para futuro uso)
- Asunto: "Tu código QR de registro"

---

## 🧪 Cómo Probar

### 1. Crear una Nueva Persona

```bash
# 1. Ir al sistema como administrador
http://tu-dominio/system/admin/personas

# 2. Clic en botón "Nueva Persona"

# 3. Llenar el formulario:
   - Nombre: Juan Pérez García
   - Documento: 12345678
   - Tipo: Aprendiz
   - Correo: tu-email@ejemplo.com

# 4. Clic en "Guardar"
```

### 2. Verificar el QR Generado

```bash
# El QR se almacena en:
storage/app/public/qrcodes/persona_persona_12345678_XXXXX.png

# URL pública:
/storage/qrcodes/persona_persona_12345678_XXXXX.png

# Escanea el QR con tu teléfono, debe mostrar:
PERSONA_12345678
```

### 3. Verificar el Correo

```bash
# Si configuraste SMTP real:
- Revisa tu bandeja de entrada
- Debe llegar "Tu código QR de registro"
- Con el QR embebido y adjunto como PNG

# Si usas MAIL_MAILER=log:
tail -f storage/logs/laravel.log

# O ejecuta el comando de prueba:
php artisan test:email --persona-id=1
```

### 4. Probar Edición

```bash
# 1. En la tabla de personas, clic en botón "Editar"
# 2. Modifica el nombre o correo (sin cambiar documento)
# 3. Guardar
# 4. Verifica que el QR se mantiene igual

# 5. Ahora cambia el documento
# 6. Guardar
# 7. Verifica que se generó un nuevo QR
```

---

## 🔍 Depuración

### Ver logs del sistema:
```bash
tail -f storage/logs/laravel.log
```

### Limpiar caché:
```bash
php artisan optimize:clear
```

### Verificar QRs almacenados:
```bash
ls -la storage/app/public/qrcodes/
```

### Crear enlace simbólico (si no existe):
```bash
php artisan storage:link
```

---

## ✅ Checklist de Funcionalidades

- [x] Modal compacto y responsive
- [x] Solo campos necesarios de la BD
- [x] Generación automática de QR
- [x] QR almacenado en storage/public
- [x] Envío de correo con QR adjunto
- [x] Validación de campos requeridos
- [x] Manejo de errores
- [x] Estados de loading
- [x] Touch-friendly para PWA
- [x] Iconos visuales
- [x] Placeholders descriptivos
- [x] InputMode optimizado

---

## 🎯 Diferencias con el Sistema Original

### Antes (PersonaController.php):
- Formulario complejo con portátiles y vehículos
- Múltiples pasos (wizard)
- Más campos opcionales

### Ahora (Admin/PersonasController.php):
- ✨ Modal simple de un solo paso
- ✨ Solo persona (sin portátiles ni vehículos)
- ✨ 4 campos esenciales
- ✨ Más rápido y directo
- ✨ Perfecto para uso administrativo

---

## 📱 Optimizaciones PWA

1. **Touch-manipulation**: Mejor respuesta táctil
2. **InputMode**: Teclados optimizados en móvil
3. **Active states**: Feedback visual al tocar
4. **Compact spacing**: Mejor uso del espacio
5. **Placeholders**: Guías contextuales
6. **Icons**: Identificación visual rápida

---

## 🚀 Próximos Pasos Sugeridos

1. Agregar filtro de búsqueda en tiempo real
2. Exportar lista de personas a Excel/PDF
3. Bulk actions (editar/eliminar múltiples)
4. Preview del QR antes de guardar
5. Reenvío de correo con QR
6. Estadísticas de personas por tipo

---

## 📞 Soporte

Si encuentras algún problema:
1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica la configuración de correo en `.env`
3. Asegúrate que el link simbólico existe: `php artisan storage:link`
4. Limpia la caché: `php artisan optimize:clear`

---

**Creado**: 2025-01-14  
**Versión**: 2.0 Compacto PWA  
**Estado**: ✅ Funcional y Optimizado
