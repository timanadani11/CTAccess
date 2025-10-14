# Corrección de Carga de Datos - Portátiles y Vehículos

## Problema Identificado
Las tablas de Portátiles y Vehículos no mostraban datos porque:
1. Los modelos no tenían configuradas las claves primarias correctas
2. Las relaciones con Persona usaban nombres de columna incorrectos
3. Las vistas Vue referenciaban campos con nombres incorrectos
4. Los controladores seleccionaban columnas con nombres que no coincidían con la base de datos

## Estructura de Base de Datos

### Tabla `personas`
- **Clave primaria**: `idPersona`
- **Columnas principales**:
  - `idPersona` (PK)
  - `Nombre` (con mayúscula)
  - `documento`
  - `TipoPersona` (con mayúsculas)
  - `TipoDocumento`
  - `Correo`
  - `Telefono`
  - `Direccion`
  - `Empresa`
  - `observaciones`

### Tabla `portatiles`
- **Clave primaria**: `portatil_id`
- **Clave foránea**: `persona_id` → `personas.idPersona`
- **Columnas**:
  - `portatil_id` (PK)
  - `persona_id` (FK)
  - `serial`
  - `qrCode`
  - `marca`
  - `modelo`
  - `created_at`
  - `updated_at`

### Tabla `vehiculos`
- **Clave primaria**: `id` (estándar Laravel)
- **Clave foránea**: `persona_id` → `personas.idPersona`
- **Columnas**:
  - `id` (PK)
  - `persona_id` (FK)
  - `tipo`
  - `placa`
  - `qrCode`
  - `created_at`
  - `updated_at`

## Correcciones Realizadas

### 1. Modelo Portatil (`app/Models/Portatil.php`)

#### Antes:
```php
class Portatil extends Model
{
    protected $table = 'portatiles';
    
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
```

#### Después:
```php
class Portatil extends Model
{
    protected $table = 'portatiles';
    protected $primaryKey = 'portatil_id';  // ✅ Agregado
    
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'idPersona');  // ✅ Clave correcta
    }
}
```

### 2. PortatilesController (`app/Http/Controllers/System/Admin/PortatilesController.php`)

#### Método `data()` - Correcciones:
```php
// Antes:
$query = Portatil::with('persona:id,nombre,documento');
$q->where('nombre', 'like', "%{$search}%")

// Después:
$query = Portatil::with('persona:idPersona,Nombre,documento');  // ✅ Columnas correctas
$q->where('Nombre', 'like', "%{$search}%")  // ✅ Nombre con mayúscula
```

#### Método `personas()` - Correcciones:
```php
// Antes:
$personas = Persona::select('id', 'nombre', 'documento', 'tipo_persona')

// Después:
$personas = Persona::select('idPersona as id', 'Nombre as nombre', 'documento', 'TipoPersona as tipo_persona')  // ✅ Alias para compatibilidad
```

#### Métodos `store()` y `update()` - Correcciones:
```php
// Antes:
'persona_id' => ['required', 'exists:personas,id']

// Después:
'persona_id' => ['required', 'exists:personas,idPersona']  // ✅ Validación con clave correcta
```

#### Método `update()` - Validación unique:
```php
// Antes:
'serial' => ['required', 'string', 'max:255', 'unique:portatiles,serial,' . $portatil->id]

// Después:
'serial' => ['required', 'string', 'max:255', 'unique:portatiles,serial,' . $portatil->portatil_id . ',portatil_id']  // ✅ Usar clave primaria correcta
```

### 3. VehiculosController (`app/Http/Controllers/System/Admin/VehiculosController.php`)

#### Método `data()` - Correcciones:
```php
// Antes:
$query = Vehiculo::with('persona:id,nombre,documento');
$q->where('nombre', 'like', "%{$search}%")

// Después:
$query = Vehiculo::with('persona:idPersona,Nombre,documento');  // ✅ Columnas correctas
$q->where('Nombre', 'like', "%{$search}%")  // ✅ Nombre con mayúscula
```

#### Método `personas()` - Correcciones:
```php
// Antes:
$personas = Persona::select('id', 'nombre', 'documento', 'tipo_persona')

// Después:
$personas = Persona::select('idPersona as id', 'Nombre as nombre', 'documento', 'TipoPersona as tipo_persona')  // ✅ Alias para compatibilidad
```

#### Métodos `store()` y `update()` - Correcciones:
```php
// Antes:
'persona_id' => ['required', 'exists:personas,id']

// Después:
'persona_id' => ['required', 'exists:personas,idPersona']  // ✅ Validación con clave correcta
```

### 4. Vista Portatiles (`resources/js/Pages/System/Admin/Portatiles/Index.vue`)

#### En la tabla - Correcciones:
```vue
<!-- Antes: -->
<tr v-for="portatil in portatiles.data" :key="portatil.id">
  <div class="font-medium text-theme-primary">{{ portatil.persona.nombre }}</div>
</tr>

<!-- Después: -->
<tr v-for="portatil in portatiles.data" :key="portatil.portatil_id">  <!-- ✅ Clave primaria correcta -->
  <div class="font-medium text-theme-primary">{{ portatil.persona.Nombre }}</div>  <!-- ✅ Nombre con mayúscula -->
</tr>
```

#### Función `openEditModal()` - Correcciones:
```javascript
// Antes:
editingPortatilId.value = portatil.id

// Después:
editingPortatilId.value = portatil.portatil_id  // ✅ Usar clave primaria correcta
```

#### Función `confirmDelete()` - Correcciones:
```javascript
// Antes:
router.delete(route('system.admin.portatiles.destroy', portatil.id)

// Después:
router.delete(route('system.admin.portatiles.destroy', portatil.portatil_id)  // ✅ Usar clave primaria correcta
```

### 5. Vista Vehiculos (`resources/js/Pages/System/Admin/Vehiculos/Index.vue`)

#### En la tabla - Correcciones:
```vue
<!-- Antes: -->
<div class="font-medium text-theme-primary">{{ vehiculo.persona.nombre }}</div>

<!-- Después: -->
<div class="font-medium text-theme-primary">{{ vehiculo.persona.Nombre }}</div>  <!-- ✅ Nombre con mayúscula -->
```

## Resumen de Cambios

### Backend (PHP)
✅ **Modelos**:
- `Portatil`: Agregada `protected $primaryKey = 'portatil_id'`
- `Portatil`: Relación persona actualizada con clave foránea correcta
- `Vehiculo`: Ya tenía configuración correcta (clave primaria `id` estándar)

✅ **Controladores**:
- Queries con `with()` actualizados para usar columnas correctas de personas
- Validaciones `exists` actualizadas para usar `idPersona`
- Validaciones `unique` en update actualizadas con clave primaria correcta
- Métodos `personas()` devuelven datos con alias para compatibilidad

### Frontend (Vue)
✅ **Vistas**:
- Referencias a `portatil.id` cambiadas a `portatil.portatil_id`
- Referencias a `persona.nombre` cambiadas a `persona.Nombre`
- Funciones de edición y eliminación actualizadas con claves correctas

## Convención de Nombres en la Base de Datos

### Tabla `personas`
- Usa **PascalCase** para nombres de columna: `Nombre`, `TipoPersona`, `TipoDocumento`
- Excepciones en minúsculas: `documento`, `correo`, `observaciones`
- Clave primaria custom: `idPersona`

### Tabla `portatiles`
- Usa **camelCase** para nombres: `persona_id`, `qrCode`, `marca`, `modelo`
- Clave primaria custom: `portatil_id`

### Tabla `vehiculos`
- Usa **snake_case** estándar: `persona_id`, `tipo`, `placa`
- Clave primaria estándar Laravel: `id`

## Solución de Alias

Para mantener consistencia en el frontend y evitar cambios masivos, se usaron **alias SQL**:

```php
Persona::select('idPersona as id', 'Nombre as nombre', 'documento', 'TipoPersona as tipo_persona')
```

Esto permite:
- El backend recibe `idPersona` pero lo devuelve como `id`
- El frontend recibe `nombre` pero en la tabla se accede con `Nombre`
- Mejor compatibilidad sin cambiar toda la estructura

## Testing Realizado

✅ **Compilación**:
- Build exitoso: 12.76s
- Sin errores de TypeScript/JavaScript
- Tamaño total: 1199.79 KiB

✅ **Pendiente de verificar en browser**:
- [ ] Carga de datos en tabla de Portátiles
- [ ] Carga de datos en tabla de Vehículos
- [ ] Creación de nuevo portátil
- [ ] Creación de nuevo vehículo
- [ ] Edición de portátil
- [ ] Edición de vehículo
- [ ] Eliminación de portátil
- [ ] Eliminación de vehículo
- [ ] Búsqueda por persona
- [ ] Paginación

## Lecciones Aprendidas

1. **Siempre verificar nombres de columnas**: Las migraciones son la fuente de verdad
2. **Claves primarias custom**: Necesitan declararse en el modelo con `protected $primaryKey`
3. **Relaciones con claves foráneas custom**: Usar tercer parámetro en `belongsTo()`
4. **Convenciones mixtas**: Usar alias SQL para normalizar datos en API
5. **Eager loading**: Especificar columnas correctas en `with()`
6. **Validación exists**: Debe usar la columna correcta de la tabla referenciada
7. **Validación unique en update**: Debe especificar la clave primaria correcta

## Archivos Modificados

```
✓ app/Models/Portatil.php
✓ app/Http/Controllers/System/Admin/PortatilesController.php
✓ app/Http/Controllers/System/Admin/VehiculosController.php
✓ resources/js/Pages/System/Admin/Portatiles/Index.vue
✓ resources/js/Pages/System/Admin/Vehiculos/Index.vue
```

## Próximos Pasos

1. ✅ Verificar que los datos se carguen correctamente en el navegador
2. Probar todas las operaciones CRUD
3. Verificar búsqueda y filtros
4. Confirmar que no hay errores en consola
5. Validar paginación funciona correctamente
