# Módulo de Gestión de Permisos - CTAccess

## 📋 Resumen

Se ha implementado un módulo completo de gestión de permisos siguiendo el sistema RBAC (Role-Based Access Control) existente en la base de datos.

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

#### 1. **permisos**
```sql
- id (PK)
- nombre (unique)
- modulo (nullable)
- descripcion (nullable)
- created_at
- updated_at
```

#### 2. **roles**
```sql
- id (PK)
- nombre (unique)
- descripcion (nullable)
- created_at
- updated_at
```

#### 3. **rol_permiso** (Pivot)
```sql
- rol_id (FK → roles.id)
- permiso_id (FK → permisos.id)
- PRIMARY KEY (rol_id, permiso_id)
```

#### 4. **usuario_rol** (Pivot)
```sql
- usuario_id (FK → usuarios_sistema.idUsuario)
- rol_id (FK → roles.id)
- PRIMARY KEY (usuario_id, rol_id)
```

---

## 📁 Archivos Creados/Modificados

### Backend (Laravel)

#### 1. **Controlador**
📄 `app/Http/Controllers/System/Admin/PermissionsController.php`

**Métodos:**
- `index()` - Lista paginada de permisos con filtros
- `store()` - Crear nuevo permiso
- `update()` - Actualizar permiso existente
- `destroy()` - Eliminar permiso

**Características:**
- ✅ Búsqueda por nombre o descripción
- ✅ Filtro por módulo
- ✅ Paginación (15 por página)
- ✅ Carga de relaciones (roles)
- ✅ Validaciones completas
- ✅ Sincronización automática de roles

#### 2. **Modelo**
📄 `app/Models/Permission.php` (ya existía)

**Relaciones:**
```php
public function roles() // belongsToMany
```

#### 3. **Seeder**
📄 `database/seeders/PermissionSeeder.php`

**Permisos creados:**

| Módulo | Permisos |
|--------|----------|
| **Usuarios** | ver_usuarios, crear_usuarios, editar_usuarios, eliminar_usuarios |
| **Personas** | ver_personas, crear_personas, editar_personas, eliminar_personas |
| **Accesos** | ver_accesos, registrar_acceso, editar_accesos, eliminar_accesos |
| **Incidencias** | ver_incidencias, crear_incidencias, editar_incidencias, resolver_incidencias, eliminar_incidencias |
| **Reportes** | ver_historial, exportar_reportes |
| **Administración** | ver_permisos, gestionar_permisos, gestionar_roles |

**Asignación de permisos:**
- **Administrador**: Todos los permisos
- **Celador**: Permisos básicos (ver/crear personas, registrar accesos, ver incidencias)

### Frontend (Vue.js)

#### 4. **Vista Principal**
📄 `resources/js/Pages/System/Admin/Permissions/Index.vue`

**Características:**
- ✅ Tabla responsive con permisos
- ✅ Modal compacto para crear/editar
- ✅ Búsqueda en tiempo real
- ✅ Filtro por módulo
- ✅ Badges para roles asignados
- ✅ Iconos Lucide integrados
- ✅ Modo oscuro completo
- ✅ Paginación funcional

**Columnas de la tabla:**
1. Permiso (con icono)
2. Módulo (badge)
3. Descripción
4. Roles asignados (badges)
5. Acciones (Editar/Eliminar)

#### 5. **Rutas**
📄 `routes/web.php`

```php
Route::resource('permissions', PermissionsController::class)
    ->only(['index', 'store', 'update', 'destroy']);
```

**Rutas generadas:**
- `GET  /system/admin/permissions` → index
- `POST /system/admin/permissions` → store
- `PUT  /system/admin/permissions/{id}` → update
- `DELETE /system/admin/permissions/{id}` → destroy

#### 6. **Menú de Navegación**
📄 `config/menus.php`

Agregado ítem de menú:
```php
[
    'label' => 'Permisos',
    'icon'  => 'heroicon-m-lock-closed',
    'route' => 'system.admin.permissions.index',
    'can'   => 'manage-permissions',
]
```

---

## 🎨 Interfaz de Usuario

### Vista Principal

```
┌─────────────────────────────────────────────────────────────┐
│ Gestión de Permisos                      [+ Nuevo permiso]  │
│ Administra los permisos del sistema y asígnalos a roles     │
├─────────────────────────────────────────────────────────────┤
│ Buscar: [________________]  Módulo: [Todos los módulos ▼]  │
├─────────────────────────────────────────────────────────────┤
│ Permiso │ Módulo │ Descripción │ Roles │ Acciones          │
├─────────────────────────────────────────────────────────────┤
│ 🔑 ver_usuarios │ Usuarios │ Ver lista... │ admin │ Edit Del│
│ 🔑 crear_usuarios│ Usuarios │ Crear nuevos │ admin │ Edit Del│
│ 🔑 ver_accesos  │ Accesos  │ Ver registros│ admin │ Edit Del│
│                                    celador                   │
└─────────────────────────────────────────────────────────────┘
```

### Modal de Creación/Edición

```
┌─────────────────────────────────────┐
│ 🔑 Nuevo Permiso               [X]  │
├─────────────────────────────────────┤
│ 🔑 Nombre del permiso *             │
│ [ver_usuarios________________]      │
│                                     │
│ 📁 Módulo                           │
│ [Usuarios____________________]      │
│ Agrupa permisos por funcionalidad   │
│                                     │
│ 📄 Descripción                      │
│ [Ver lista de usuarios del......]  │
│                                     │
│ 🛡️ Asignar a roles                  │
│ ☑ administrador  ☐ celador          │
│ Los roles seleccionados tendrán...  │
├─────────────────────────────────────┤
│              [X Cancelar] [💾 Crear]│
└─────────────────────────────────────┘
```

---

## 🎯 Funcionalidades

### 1. **Listar Permisos**
- Tabla paginada con 15 permisos por página
- Muestra: nombre, módulo, descripción, roles asignados
- Orden: por módulo y luego por nombre

### 2. **Buscar Permisos**
- Búsqueda en tiempo real
- Busca en: nombre y descripción
- Mantiene otros filtros activos

### 3. **Filtrar por Módulo**
- Dropdown con módulos únicos
- Opción "Todos los módulos"
- Se actualiza automáticamente

### 4. **Crear Permiso**
- Modal compacto
- Campos: nombre*, módulo, descripción
- Asignación múltiple de roles
- Validación en tiempo real

### 5. **Editar Permiso**
- Abre modal con datos precargados
- Permite cambiar nombre (validado único)
- Actualiza módulo y descripción
- Reasigna roles

### 6. **Eliminar Permiso**
- Confirmación antes de eliminar
- Se desasocia automáticamente de roles
- Eliminación en cascada

### 7. **Asignar a Roles**
- Checkboxes con todos los roles
- Selección múltiple
- Sincronización automática en BD

---

## 🔐 Seguridad y Validaciones

### Backend (Laravel)

#### Validaciones de Creación
```php
'nombre' => 'required|string|max:255|unique:permisos'
'modulo' => 'nullable|string|max:255'
'descripcion' => 'nullable|string|max:500'
'roles' => 'array'
'roles.*' => 'integer|exists:roles,id'
```

#### Validaciones de Actualización
```php
'nombre' => 'required|string|max:255|unique:permisos,nombre,{id}'
// (ignora el ID actual para unicidad)
```

#### Middleware
- `auth:system` - Autenticación requerida
- `check.system.role:administrador` - Solo administradores

### Frontend (Vue)

- Validación de campo requerido (nombre)
- Mensajes de error en tiempo real
- Botones deshabilitados durante procesamiento
- Confirmación antes de eliminar

---

## 📊 Ejemplos de Permisos

### Módulo: Usuarios
```php
[
    'nombre' => 'ver_usuarios',
    'modulo' => 'Usuarios',
    'descripcion' => 'Ver lista de usuarios del sistema',
]
```

### Módulo: Accesos
```php
[
    'nombre' => 'registrar_acceso',
    'modulo' => 'Accesos',
    'descripcion' => 'Registrar entrada/salida de personas',
]
```

### Módulo: Incidencias
```php
[
    'nombre' => 'resolver_incidencias',
    'modulo' => 'Incidencias',
    'descripcion' => 'Marcar incidencias como resueltas',
]
```

---

## 🎨 Diseño Visual

### Badges de Módulo
```html
<span class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
    Usuarios
</span>
```

### Badges de Roles
```html
<span class="bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
    administrador
</span>
```

### Iconos Utilizados

| Elemento | Icono | Tamaño |
|----------|-------|--------|
| Permiso | `Key` | 14px |
| Módulo | `Folder` | 12px |
| Descripción | `FileText` | 12px |
| Roles | `Shield` | 12px |
| Crear | `Plus` | 16px |
| Editar | `Edit` | 12px |
| Eliminar | `Trash2` | 12px |
| Guardar | `Save` | 14px |

---

## 🚀 Cómo Usar

### 1. Acceder al Módulo
```
Dashboard → Permisos (en menú lateral)
URL: /system/admin/permissions
```

### 2. Crear un Permiso
1. Click en "Nuevo permiso"
2. Ingresar nombre único (Ej: `ver_reportes`)
3. Opcional: Agregar módulo (Ej: `Reportes`)
4. Opcional: Agregar descripción
5. Seleccionar roles que tendrán este permiso
6. Click en "Crear"

### 3. Editar un Permiso
1. Click en "Editar" en la fila del permiso
2. Modificar campos necesarios
3. Agregar/quitar roles
4. Click en "Actualizar"

### 4. Buscar Permisos
- Escribir en el campo de búsqueda
- Los resultados se filtran automáticamente
- Busca en nombre y descripción

### 5. Filtrar por Módulo
- Seleccionar módulo del dropdown
- Ver solo permisos de ese módulo
- Combinar con búsqueda

### 6. Eliminar un Permiso
1. Click en "Eliminar"
2. Confirmar en el diálogo
3. El permiso se elimina de todos los roles

---

## 🔄 Relaciones y Sincronización

### Relación Permisos ↔ Roles
```php
// En Permission.php
public function roles() {
    return $this->belongsToMany(Role::class, 'rol_permiso', 'permiso_id', 'rol_id');
}

// En Role.php
public function permissions() {
    return $this->belongsToMany(Permission::class, 'rol_permiso', 'rol_id', 'permiso_id');
}
```

### Sincronización Automática
```php
// Al crear/editar permiso
$permiso->roles()->sync($validated['roles']);

// Sincroniza tabla pivot automáticamente:
// - Agrega nuevas relaciones
// - Elimina relaciones no seleccionadas
// - Mantiene existentes
```

---

## 📈 Estadísticas

### Permisos Creados por el Seeder
- **Total**: 22 permisos
- **Módulos**: 5 (Usuarios, Personas, Accesos, Incidencias, Reportes, Administración)
- **Asignados a Admin**: 22 (todos)
- **Asignados a Celador**: 7 (básicos)

### Módulos Disponibles
1. **Usuarios** - 4 permisos
2. **Personas** - 4 permisos
3. **Accesos** - 4 permisos
4. **Incidencias** - 5 permisos
5. **Reportes** - 2 permisos
6. **Administración** - 3 permisos

---

## ✅ Checklist de Implementación

- [x] Modelo Permission existente verificado
- [x] Controlador PermissionsController creado
- [x] Rutas RESTful configuradas
- [x] Vista Index.vue con modal creada
- [x] Integración de iconos Lucide
- [x] Modo oscuro completo implementado
- [x] Filtros de búsqueda y módulo
- [x] Paginación funcional
- [x] Validaciones backend y frontend
- [x] Seeder con permisos de ejemplo
- [x] Asignación automática a roles
- [x] Menú de navegación actualizado
- [x] Compilación exitosa
- [x] Seeder ejecutado correctamente

---

## 🔮 Mejoras Futuras Sugeridas

1. **Módulo de Roles**: Crear vista para gestionar roles
2. **Asignación masiva**: Asignar múltiples permisos a un rol
3. **Permisos compuestos**: Permisos que requieren otros permisos
4. **Historial de cambios**: Log de quién modifica permisos
5. **Importar/Exportar**: JSON de permisos para backup
6. **Permisos por usuario**: Override de permisos individuales
7. **Dashboard de permisos**: Matriz visual de roles/permisos
8. **Validación en uso**: Prevenir eliminar permisos en uso

---

## 📞 Endpoints API

```
GET    /system/admin/permissions            Lista de permisos
POST   /system/admin/permissions            Crear permiso
PUT    /system/admin/permissions/{id}       Actualizar permiso
DELETE /system/admin/permissions/{id}       Eliminar permiso
```

**Query Parameters:**
- `q` - Búsqueda por texto
- `modulo` - Filtro por módulo
- `page` - Número de página

---

## 🎓 Notas Técnicas

### Convención de Nombres
```
Formato: {accion}_{recurso}
Ejemplos:
- ver_usuarios
- crear_accesos
- editar_incidencias
- resolver_incidencias
- exportar_reportes
```

### Módulos Recomendados
- Usa nombres en singular o plural según el contexto
- Primera letra en mayúscula
- Agrupa funcionalidades relacionadas
- Ejemplos: Usuarios, Accesos, Incidencias, Reportes

### Sincronización vs Attach
```php
// sync() - Reemplaza todas las relaciones
$permiso->roles()->sync([1, 2, 3]);

// attach() - Agrega sin eliminar existentes
$permiso->roles()->attach([4, 5]);

// syncWithoutDetaching() - Agrega garantizando inclusión
$permiso->roles()->syncWithoutDetaching([1, 6]);
```

---

**Fecha de creación**: 14 de Octubre, 2025  
**Versión**: 1.0  
**Estado**: ✅ Completado y funcional  
**Desarrollador**: Sistema CTAccess
