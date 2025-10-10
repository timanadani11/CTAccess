# 🧪 Prueba Rápida - Sistema de Accesos Mejorado

## ✅ Cambios Implementados

### Problema resuelto:
- ❌ **ANTES**: `portatil_id` y `vehiculo_id` quedaban NULL
- ✅ **AHORA**: Se llenan automáticamente al registrar entrada

---

## 🧪 Cómo Probar

### 1. Verificar Base de Datos Actual
```sql
-- Ver estado actual de accesos
SELECT 
    idAcceso,
    persona_id,
    portatil_id,      -- Antes: NULL, Ahora: con ID
    vehiculo_id,      -- Antes: NULL, Ahora: con ID
    fecha_entrada,
    estado
FROM accesos 
ORDER BY fecha_entrada DESC 
LIMIT 5;
```

### 2. Probar ENTRADA (Escaneo QR o Manual)

#### Opción A: Con QR
1. Ir a: `http://127.0.0.1:8000/system/celador/qr`
2. Click en **"Escanear QR"**
3. Escanear QR de una persona
4. Ver confirmación con portátil/vehículo detectados

#### Opción B: Manual (con cédula)
1. Ir a: `http://127.0.0.1:8000/system/celador/qr`
2. Click en **"Entrada Manual"**
3. Digitar cédula (ej: 123456789)
4. Click en **"Buscar Persona"**
5. Ver confirmación

### 3. Verificar que se guardó correctamente
```sql
-- Verificar el último acceso registrado
SELECT 
    a.*,
    p.Nombre,
    port.marca as portatil_marca,
    port.serial as portatil_serial,
    v.placa as vehiculo_placa
FROM accesos a
LEFT JOIN personas p ON a.persona_id = p.idPersona
LEFT JOIN portatiles port ON a.portatil_id = port.portatil_id
LEFT JOIN vehiculos v ON a.vehiculo_id = v.id
WHERE a.idAcceso = (SELECT MAX(idAcceso) FROM accesos);
```

**Resultado esperado:**
```
✅ portatil_id: NO NULL (tiene valor)
✅ vehiculo_id: NO NULL (tiene valor)
✅ Se ven los datos del portátil y vehículo
```

### 4. Probar SALIDA (con verificación)

1. **Escanear/Ingresar la misma persona**
   - El sistema detecta que tiene acceso activo
   - Muestra: "⚠️ SALIDA - Verificación requerida"

2. **Ver qué debe verificar:**
   ```
   📱 Debe escanear QR del portátil: [Serial]
   🚗 Debe escanear QR del vehículo: [Placa]
   ```

3. **Escanear QR del portátil** (debe coincidir)

4. **Escanear QR del vehículo** (debe coincidir)

5. **Ver confirmación de salida exitosa**

### 5. Probar INCIDENCIA (portátil diferente)

1. Escanear persona (que tiene acceso activo)
2. Escanear QR de un portátil DIFERENTE al que entró
3. Ver mensaje:
   ```
   ⚠️ INCIDENCIA DETECTADA
   ❌ El portátil NO coincide
   🚫 Salida bloqueada
   ```

---

## 📊 Consultas SQL Útiles

### Ver personas con sus portátiles y vehículos
```sql
SELECT 
    p.idPersona,
    p.Nombre,
    p.documento,
    port.portatil_id,
    port.marca,
    port.serial,
    v.id as vehiculo_id,
    v.placa
FROM personas p
LEFT JOIN portatiles port ON p.idPersona = port.persona_id
LEFT JOIN vehiculos v ON p.idPersona = v.persona_id;
```

### Ver accesos activos con detalles
```sql
SELECT 
    a.idAcceso,
    p.Nombre,
    a.fecha_entrada,
    port.serial as portatil,
    v.placa as vehiculo,
    a.estado
FROM accesos a
JOIN personas p ON a.persona_id = p.idPersona
LEFT JOIN portatiles port ON a.portatil_id = port.portatil_id
LEFT JOIN vehiculos v ON a.vehiculo_id = v.id
WHERE a.estado = 'activo'
ORDER BY a.fecha_entrada DESC;
```

### Ver incidencias recientes
```sql
SELECT 
    i.incidenciaId,
    p.Nombre as persona,
    i.descripcion,
    i.fecha,
    i.tipo
FROM incidencias i
JOIN accesos a ON i.acceso_id = a.idAcceso
JOIN personas p ON a.persona_id = p.idPersona
ORDER BY i.fecha DESC
LIMIT 10;
```

---

## 🎯 Escenarios de Prueba

### ✅ Caso 1: Persona CON portátil y vehículo
```
1. Buscar persona con ID = 5 (debe tener portátil y vehículo asociados)
2. Registrar entrada → Ver que portatil_id y vehiculo_id se llenan
3. Intentar salida → Ver que pide verificación
4. Escanear QRs correctos → Ver salida exitosa
```

### ✅ Caso 2: Persona SIN portátil ni vehículo
```
1. Buscar persona sin equipos asociados
2. Registrar entrada → portatil_id y vehiculo_id quedan NULL
3. Registrar salida → No pide verificaciones, salida inmediata
```

### ✅ Caso 3: Persona solo con portátil
```
1. Buscar persona con portátil pero sin vehículo
2. Entrada → portatil_id se llena, vehiculo_id NULL
3. Salida → Solo pide verificar portátil
```

### ⚠️ Caso 4: Incidencia - Portátil diferente
```
1. Persona entra con portátil A (portatil_id = 5)
2. Al salir, escanea portátil B (portatil_id = 12)
3. Sistema crea incidencia automáticamente
4. Salida bloqueada
```

---

## 📱 Prueba en Modo PWA (Móvil)

1. Abrir en Chrome móvil: `http://TU_IP:8000/system/celador/qr`
2. Agregar a pantalla de inicio
3. Abrir como app
4. Probar escaneo con cámara del dispositivo
5. Probar entrada manual con teclado numérico

---

## 🐛 Debugging

### Ver logs del backend:
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50
```

### Ver logs en tiempo real:
```bash
Get-Content storage/logs/laravel.log -Wait
```

### Verificar errores específicos:
```bash
Select-String -Path storage/logs/laravel.log -Pattern "Error al registrar acceso"
```

---

## ✅ Checklist de Pruebas

- [ ] Entrada por QR funciona
- [ ] Entrada manual por cédula funciona
- [ ] `portatil_id` se guarda automáticamente (si tiene)
- [ ] `vehiculo_id` se guarda automáticamente (si tiene)
- [ ] Salida detecta acceso activo
- [ ] Salida pide verificación de portátil (si entró con portátil)
- [ ] Salida pide verificación de vehículo (si entró con vehículo)
- [ ] Portátil correcto permite salida
- [ ] Portátil incorrecto crea incidencia
- [ ] Vehículo correcto permite salida
- [ ] Vehículo incorrecto crea incidencia
- [ ] Persona sin equipos entra y sale sin verificaciones
- [ ] Notificaciones visuales se muestran correctamente
- [ ] Modo PWA funciona en móvil

---

## 🚀 Siguiente Paso

Si todas las pruebas pasan, el sistema está listo para producción.

**Archivo de referencia**: `FLUJO_ACCESO_MEJORADO.md`
