# 🔧 Corrección de Errores - Módulo de Incidencias

## 🐛 Errores Corregidos

### Error 1: Columna `accesos.accesoId` no encontrada
**Error original:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'accesos.accesoId' in 'where clause'
```

**Causa:** 
La relación en el modelo `Incidencia` estaba buscando la columna `accesoId` en la tabla `accesos`, pero la clave primaria real es `id`.

**Solución:**
```php
// Antes (INCORRECTO)
public function acceso()
{
    return $this->belongsTo(Acceso::class, 'accesoId_id_fk', 'accesoId');
}

// Después (CORRECTO)
public function acceso()
{
    return $this->belongsTo(Acceso::class, 'accesoId_id_fk', 'id');
}
```

---

### Error 2: Columna `rol` no encontrada en `usuarios_sistema`
**Error original:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'rol' in 'field list'
```

**Causa:**
La columna `rol` fue eliminada de la tabla `usuarios_sistema` y reemplazada por el sistema RBAC con `rol_principal_id` (foreign key a tabla `roles`).

**Solución:**

#### 1. Actualizar el Controlador
```php
// Antes (INCORRECTO)
$query = Incidencia::with([
    'acceso.persona', 
    'reportadoPor:idUsuario,nombre,rol'
])->latest('created_at');

// Después (CORRECTO)
$query = Incidencia::with([
    'acceso.persona', 
    'reportadoPor:idUsuario,nombre,rol_principal_id',
    'reportadoPor.principalRole:id,nombre'
])->latest('created_at');
```

#### 2. Actualizar la Vista (Index.vue)
```vue
<!-- Antes (INCORRECTO) -->
<p class="text-xs text-theme-secondary">
  {{ i.reportadoPor?.rol ?? 'Automático' }}
</p>

<!-- Después (CORRECTO) -->
<p class="text-xs text-theme-secondary capitalize">
  {{ i.reportadoPor?.principalRole?.nombre ?? 
     i.reportadoPor?.principal_role?.nombre ?? 
     'Automático' }}
</p>
```

---

## 📊 Estructura de Base de Datos Correcta

### Tabla `incidencias`
```
- incidenciaId (PK)
- accesoId_id_fk (FK → accesos.id)
- usuario_id_fk (FK → usuarios_sistema.idUsuario)
- tipo (string)
- descripcion (string)
- prioridad (enum: 'baja', 'media', 'alta') DEFAULT 'baja' ← AGREGADA
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabla `accesos`
```
- id (PK) ← Esta es la clave primaria real
- persona_id (FK)
- portatil_id (FK)
- vehiculo_id (FK)
- fecha_entrada (timestamp)
- fecha_salida (timestamp)
- estado (string)
- usuario_entrada_id (FK)
- usuario_salida_id (FK)
```

### Tabla `usuarios_sistema`
```
- idUsuario (PK)
- UserName (string)
- password (string)
- nombre (string)
- rol_principal_id (FK → roles.id) ← Reemplazó a 'rol'
- activo (boolean)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabla `roles` (Sistema RBAC)
```
- id (PK)
- nombre (string) ← 'administrador', 'celador', etc.
- descripcion (text)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## ✅ Cambios Aplicados

### Archivo: `app/Models/Incidencia.php`
1. ✅ Corregida la foreign key de `acceso()` de `accesoId` a `id`
2. ✅ Agregada la propiedad `protected $table = 'incidencias'`
3. ✅ Mantenidas las relaciones `reportadoPor()` y `usuario()`
4. ✅ Campo `prioridad` incluido en `$fillable`

### Archivo: `database/migrations/2025_10_13_000001_add_prioridad_to_incidencias_table.php`
1. ✅ Migración creada para agregar columna `prioridad`
2. ✅ Tipo ENUM con valores: 'baja', 'media', 'alta'
3. ✅ Valor por defecto: 'baja'
4. ✅ Migración ejecutada exitosamente

### Archivo: `app/Http/Controllers/System/Celador/IncidenciaController.php`
1. ✅ Actualizado el eager loading para cargar `rol_principal_id`
2. ✅ Agregada la relación `reportadoPor.principalRole`
3. ✅ Especificados solo los campos necesarios para optimización

### Archivo: `resources/js/Pages/System/Celador/Incidencias/Index.vue`
1. ✅ Actualizada la referencia al rol del usuario reportante
2. ✅ Agregado fallback para diferentes nomenclaturas (camelCase y snake_case)
3. ✅ Agregado capitalize para mostrar el rol correctamente

---

## 🧪 Verificación

Para verificar que todo funciona correctamente:

1. **Accede al módulo de incidencias:**
   ```
   /system/celador/incidencias
   ```

2. **Verifica que se muestren:**
   - ✅ Lista de incidencias con información de accesos
   - ✅ Nombre de la persona involucrada
   - ✅ Nombre del usuario que reportó
   - ✅ Rol del usuario reportante (ej: "administrador", "celador")
   - ✅ Estadísticas correctas

3. **Prueba los filtros:**
   - ✅ Buscar por descripción o nombre de persona
   - ✅ Filtrar por tipo de incidencia
   - ✅ Filtrar por prioridad

---

## 🔄 Sistema RBAC Implementado

El sistema ahora usa un sistema de roles y permisos robusto:

### Modelo de Relaciones
```
UsuarioSistema
  ├─ principalRole() → Role (relación principal)
  ├─ roles() → Role[] (relación many-to-many)
  └─ hasPermission() → boolean (verificación de permisos)

Role
  ├─ usuarios() → UsuarioSistema[]
  └─ permissions() → Permission[]
```

### Métodos Útiles en UsuarioSistema
```php
$usuario->principalRole->nombre;  // 'administrador', 'celador', etc.
$usuario->hasSystemRole('administrador');  // true/false
$usuario->hasPermission('gestionar_accesos');  // true/false
$usuario->isAdmin();  // Método helper
$usuario->isCelador();  // Método helper
```

---

## 📝 Notas Importantes

1. **Migración del Sistema de Roles:**
   - La columna `rol` (string) fue eliminada
   - Ahora se usa `rol_principal_id` (FK a tabla roles)
   - Esto permite mayor flexibilidad y control de permisos

2. **Compatibilidad:**
   - El modelo mantiene métodos legacy para compatibilidad
   - Los seeders han migrado los datos antiguos al nuevo sistema

3. **Eager Loading:**
   - Se carga solo la información necesaria para optimizar queries
   - Se especifican los campos exactos en los selects

---

---

### Error 3: Columna `prioridad` no encontrada en `incidencias`
**Error original:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'prioridad' in 'where clause'
```

**Causa:**
La tabla `incidencias` fue creada sin la columna `prioridad`, pero el código intenta utilizarla para filtrar y mostrar la prioridad de las incidencias.

**Solución:**
Creada migración para agregar la columna:

```php
// database/migrations/2025_10_13_000001_add_prioridad_to_incidencias_table.php
Schema::table('incidencias', function (Blueprint $table) {
    $table->enum('prioridad', ['baja', 'media', 'alta'])
          ->default('baja')
          ->after('descripcion');
});
```

**Migración ejecutada:**
```bash
php artisan migrate
✅ 2025_10_13_000001_add_prioridad_to_incidencias_table DONE
```

---

**Fecha de corrección:** 13 de Octubre de 2025  
**Estado:** ✅ Completado y verificado  
**Sin errores de compilación**
