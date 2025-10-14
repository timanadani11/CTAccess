# Modal de Personas Implementado

## Descripción General
Se ha implementado un modal moderno y compacto para la gestión de personas en el panel de administración, siguiendo el mismo patrón de diseño establecido en los módulos de Usuarios y Permisos.

## Cambios Realizados

### 1. Backend - Controlador
**Archivo**: `app/Http/Controllers/System/Admin/PersonasController.php`

Se creó el controlador con tres métodos principales:

- **store()**: Crear nueva persona
  - Valida 9 campos (4 requeridos: Nombre, TipoDocumento, documento, TipoPersona)
  - Validación única en campo `documento`
  - Retorna con mensaje de éxito

- **update()**: Actualizar persona existente
  - Mismas validaciones que store
  - Ignora el documento actual en validación única
  - Retorna con mensaje de éxito

- **destroy()**: Eliminar persona
  - Elimina el registro
  - Retorna con mensaje de éxito

### 2. Backend - Rutas
**Archivo**: `routes/web.php`

Se agregaron las siguientes rutas dentro del grupo de administración:

```php
// Gestión de Personas
Route::post('/personas', [PersonasController::class, 'store'])->name('system.admin.personas.store');
Route::put('/personas/{persona}', [PersonasController::class, 'update'])->name('system.admin.personas.update');
Route::delete('/personas/{persona}', [PersonasController::class, 'destroy'])->name('system.admin.personas.destroy');
```

### 3. Frontend - Componente Vue
**Archivo**: `resources/js/Pages/System/Admin/Personas.vue`

#### Cambios en el Script:
- Importado `Modal`, `Icon` y `useForm`
- Agregado estado del modal: `showModal`, `isEditing`, `editingPersonaId`
- Creado `form` con useForm para los 9 campos de persona
- Agregadas opciones de `tiposDocumento` y `tiposPersona`
- Implementadas funciones:
  - `openCreateModal()`: Abre modal vacío
  - `openEditModal(persona)`: Abre modal con datos de la persona
  - `closeModal()`: Cierra y limpia el modal
  - `canSave`: Computed para validar campos requeridos
  - `submit()`: Envía formulario (store o update según modo)
  - `confirmDelete()`: Elimina persona con confirmación

#### Cambios en la Plantilla:
- Botón "Nueva Persona" actualizado:
  - Cambió de enlace `<a href="/personas/create">` a `<button @click="openCreateModal">`
  - Usa icono de Lucide: `user-plus`

- Botones de acción en la tabla simplificados:
  - Botón "Editar" con icono `pencil` llama a `openEditModal()`
  - Botón "Eliminar" con icono `trash` llama a `confirmDelete()`
  - Eliminado botón "Ver detalles"

- Modal agregado con las siguientes características:
  - Tamaño: `max-width="lg"` (compacto)
  - Título dinámico según modo (crear/editar)
  - 9 campos organizados en grid responsive:
    1. **Nombre** (text, required)
    2. **Tipo Documento** (select, required)
    3. **Documento** (text, required)
    4. **Tipo Persona** (select, required)
    5. **Correo** (email, optional)
    6. **Teléfono** (text, optional)
    7. **Dirección** (text, optional)
    8. **Empresa** (text, optional)
    9. **Observaciones** (textarea, optional)

## Características del Modal

### Diseño Compacto
- Padding: `p-6`
- Espaciado entre campos: `space-y-3`
- Inputs con `py-1.5` (altura reducida)
- Labels con `text-xs` (texto pequeño)
- Iconos de 12-18px

### Modo Oscuro Completo
- Fondo: `dark:bg-sage-700`
- Texto: `dark:text-sage-100`
- Bordes: `dark:border-sage-600`
- Todos los elementos soportan modo oscuro

### Iconos Lucide
Todos los campos tienen iconos identificativos:
- `user`: Nombre
- `file-text`: Tipo Documento
- `badge`: Documento
- `users`: Tipo Persona
- `mail`: Correo
- `phone`: Teléfono
- `map-pin`: Dirección
- `building`: Empresa
- `save`: Botón guardar

### Validación
- Validación en frontend: Botón guardar deshabilitado si faltan campos requeridos
- Validación en backend: Mensajes de error específicos por campo
- Muestra errores debajo de cada campo con estilo rojo

### Experiencia de Usuario
- Modal se abre/cierra suavemente
- Formulario se resetea al cerrar
- Estados de carga (botón "Guardando...")
- Confirmación antes de eliminar
- Recarga automática de lista tras cambios
- `preserveScroll: true` para mantener posición

## Tipos Predefinidos

### Tipos de Documento
- DNI
- Pasaporte
- Cédula
- RUC
- Carnet de Extranjería

### Tipos de Persona
- Estudiante
- Docente
- Administrativo
- Visitante
- Proveedor
- Contratista
- Aprendiz

## Flujo de Trabajo

### Crear Persona
1. Usuario hace clic en "Nueva Persona"
2. Se abre modal vacío
3. Usuario completa campos (mínimo los 4 requeridos)
4. Hace clic en "Guardar"
5. Se valida y envía al backend
6. Modal se cierra y lista se recarga
7. Mensaje de éxito aparece

### Editar Persona
1. Usuario hace clic en botón de editar (icono lápiz)
2. Modal se abre con datos precargados
3. Usuario modifica campos deseados
4. Hace clic en "Actualizar"
5. Se valida y envía al backend
6. Modal se cierra y lista se recarga
7. Mensaje de éxito aparece

### Eliminar Persona
1. Usuario hace clic en botón de eliminar (icono basura)
2. Aparece confirmación: "¿Estás seguro de eliminar a [nombre]?"
3. Si confirma, se elimina del backend
4. Lista se recarga
5. Mensaje de éxito aparece

## Compilación
✅ Compilado exitosamente sin errores
- Tamaño total: ~1175 KiB
- Tiempo de build: 12.44s
- PWA generado correctamente

## Consistencia con Otros Módulos
Este modal sigue exactamente el mismo patrón que:
- **Módulo de Usuarios**: Modal compacto con iconos Lucide
- **Módulo de Permisos**: Modal con validación y estados de carga

Esto asegura una experiencia de usuario consistente en todo el panel de administración.

## Beneficios
1. **UX mejorada**: No hay redirección a páginas separadas
2. **Rapidez**: Crear/editar personas es más rápido
3. **Consistencia**: Mismo patrón visual en todo el sistema
4. **Accesibilidad**: Iconos intuitivos y modo oscuro completo
5. **Validación clara**: Errores específicos por campo
6. **Responsive**: Funciona en móviles y escritorio
