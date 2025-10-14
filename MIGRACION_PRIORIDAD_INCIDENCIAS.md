# 🔧 Migración: Agregar Columna Prioridad a Incidencias

## 📋 Descripción

Esta migración agrega la columna `prioridad` a la tabla `incidencias` para permitir clasificar las incidencias según su nivel de urgencia.

---

## 🎯 Problema

**Error encontrado:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'prioridad' in 'where clause'
```

El módulo de incidencias intentaba usar la columna `prioridad` que no existía en la base de datos.

---

## ✨ Solución Implementada

### Migración Creada

**Archivo:** `database/migrations/2025_10_13_000001_add_prioridad_to_incidencias_table.php`

```php
Schema::table('incidencias', function (Blueprint $table) {
    $table->enum('prioridad', ['baja', 'media', 'alta'])
          ->default('baja')
          ->after('descripcion');
});
```

### Características

- **Tipo de columna:** ENUM
- **Valores permitidos:** 
  - `'baja'` - Prioridad baja (verde #39A900)
  - `'media'` - Prioridad media (amarillo #FDC300)
  - `'alta'` - Prioridad alta (rojo)
- **Valor por defecto:** `'baja'`
- **Posición:** Después de la columna `descripcion`

---

## 🚀 Ejecución

```bash
php artisan migrate
```

**Resultado:**
```
✅ 2025_10_13_000001_add_prioridad_to_incidencias_table  328.30ms DONE
```

---

## 📊 Estructura de la Tabla Actualizada

### Antes
```sql
CREATE TABLE incidencias (
    incidenciaId BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    accesoId_id_fk BIGINT UNSIGNED NOT NULL,
    usuario_id_fk BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (accesoId_id_fk) REFERENCES accesos(id),
    FOREIGN KEY (usuario_id_fk) REFERENCES usuarios_sistema(idUsuario)
);
```

### Después
```sql
CREATE TABLE incidencias (
    incidenciaId BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    accesoId_id_fk BIGINT UNSIGNED NOT NULL,
    usuario_id_fk BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    prioridad ENUM('baja', 'media', 'alta') DEFAULT 'baja', -- ✨ NUEVA COLUMNA
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (accesoId_id_fk) REFERENCES accesos(id),
    FOREIGN KEY (usuario_id_fk) REFERENCES usuarios_sistema(idUsuario)
);
```

---

## 🎨 Uso en la Interfaz

### Filtros
Los usuarios pueden filtrar incidencias por prioridad:
```vue
<select v-model="prioridad">
  <option value="">Todas las prioridades</option>
  <option value="alta">🔴 Alta</option>
  <option value="media">🟡 Media</option>
  <option value="baja">🟢 Baja</option>
</select>
```

### Visualización
Badges con colores según prioridad:
- **Alta:** Fondo rojo con icono de alerta
- **Media:** Fondo amarillo (#FDC300) con icono de advertencia
- **Baja:** Fondo verde (#39A900) con icono de información

### Estadísticas
Se muestra una tarjeta especial para incidencias de alta prioridad:
```
┌─────────────────────────┐
│  🔴 Prioridad Alta      │
│  15                     │
│  Requieren atención     │
└─────────────────────────┘
```

---

## 🔄 Reverting (si es necesario)

Para revertir esta migración:
```bash
php artisan migrate:rollback --step=1
```

Esto eliminará la columna `prioridad` de la tabla `incidencias`.

---

## 📝 Notas Importantes

1. **Valor por defecto:** Todas las incidencias existentes tendrán prioridad 'baja' automáticamente
2. **Validación:** Laravel validará automáticamente que solo se usen los valores permitidos
3. **Compatibilidad:** No afecta las incidencias existentes, solo agrega funcionalidad

---

## ✅ Verificación

Para verificar que la columna fue agregada correctamente:

```sql
DESCRIBE incidencias;
```

Deberías ver:
```
+------------------+--------------------------------------+------+-----+---------+----------------+
| Field            | Type                                 | Null | Key | Default | Extra          |
+------------------+--------------------------------------+------+-----+---------+----------------+
| incidenciaId     | bigint unsigned                      | NO   | PRI | NULL    | auto_increment |
| accesoId_id_fk   | bigint unsigned                      | NO   | MUL | NULL    |                |
| usuario_id_fk    | bigint unsigned                      | NO   | MUL | NULL    |                |
| tipo             | varchar(255)                         | NO   |     | NULL    |                |
| descripcion      | varchar(255)                         | NO   |     | NULL    |                |
| prioridad        | enum('baja','media','alta')          | NO   |     | baja    | ← NUEVA        |
| created_at       | timestamp                            | YES  |     | NULL    |                |
| updated_at       | timestamp                            | YES  |     | NULL    |                |
+------------------+--------------------------------------+------+-----+---------+----------------+
```

---

## 🧪 Testing

### Crear incidencia con prioridad
```php
Incidencia::create([
    'accesoId_id_fk' => 1,
    'usuario_id_fk' => 2,
    'tipo' => 'seguridad',
    'descripcion' => 'Acceso no autorizado detectado',
    'prioridad' => 'alta'  // ✅ Ahora funciona
]);
```

### Filtrar por prioridad
```php
$incidenciasAltas = Incidencia::where('prioridad', 'alta')->get();
$estadisticas = [
    'alta' => Incidencia::where('prioridad', 'alta')->count(),
    'media' => Incidencia::where('prioridad', 'media')->count(),
    'baja' => Incidencia::where('prioridad', 'baja')->count(),
];
```

---

**Fecha de creación:** 13 de Octubre de 2025  
**Estado:** ✅ Ejecutada exitosamente  
**Autor:** Sistema CTAccess  
**Versión de Laravel:** 11.x
