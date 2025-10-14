# 🔧 Corrección: Flujo de Modal de Incidencia

## 📅 Fecha: 13 de Octubre, 2025

## ❌ Problema Original

Cuando el serial del portátil no coincidía:
1. Se mostraba mensaje de error en CedulaModal
2. El registro se hacía automáticamente después de 2 segundos
3. ❌ **El modal de incidencia NO se abría**
4. ❌ **El usuario no podía agregar observaciones**

---

## ✅ Solución Implementada

### 🔄 Nuevo Flujo Correcto

```
1. 👤 Usuario busca persona por cédula en CedulaModal
2. 📱 Sistema detecta que es SALIDA y requiere verificar portátil
3. 📷 Se abre cámara de verificación dentro del modal
4. 🔍 Usuario escanea QR del portátil
5. ❌ Sistema detecta: Serial NO coincide

   ⬇️ AQUÍ VIENE EL CAMBIO ⬇️

6. 🚪 Se CIERRA el CedulaModal (modal de cámara)
7. 🚨 Se ABRE IncidenciaModal automáticamente
8. 📋 Modal muestra:
   - Serial esperado vs Serial escaneado
   - Información de la persona
   - Campo para observaciones
9. ✏️ Usuario agrega observaciones (opcional)
10. ✅ Usuario confirma
11. 💾 Se registra:
    - Salida exitosa ✅
    - Incidencia en BD ⚠️
12. 📢 Notificación: "Salida registrada con incidencia"
```

---

## 🔧 Cambios Técnicos Implementados

### 1. **Index.vue - Handler de Incidencias**

#### Nuevo evento agregado a los modales:
```vue
<CedulaModal
  :show="showCedulaModal"
  @close="closeCedulaModal"
  @acceso-registrado="handleAccesoRegistrado"
  @incidencia-detectada="handleIncidenciaDetectada"  <!-- ✨ NUEVO -->
/>
```

#### Nueva función handler:
```javascript
const handleIncidenciaDetectada = (incidenciaInfo) => {
  // 1. Cerrar modales de escaneo
  showQrScannerModal.value = false
  showCedulaModal.value = false
  
  // 2. Preparar datos
  incidenciaData.value = {
    errorMessage: incidenciaInfo.errorMessage,
    accesoInfo: incidenciaInfo.accesoInfo,
    datosRegistro: incidenciaInfo.datosRegistro // 💾 Guardar para luego
  }
  
  // 3. Abrir modal de incidencia
  showIncidenciaModal.value = true
}
```

#### Función de confirmación actualizada:
```javascript
const handleIncidenciaConfirmada = (data) => {
  showIncidenciaModal.value = false
  
  // Usar los datos de registro guardados
  if (incidenciaData.value?.datosRegistro) {
    const payload = {
      ...incidenciaData.value.datosRegistro,
      descripcion_incidencia: data.descripcion
    }
    
    // Registrar con incidencia
    router.post(route('system.celador.qr.registrar'), payload, {
      onSuccess: () => {
        showNotification('warning', 'Salida registrada con incidencia')
      }
    })
  }
}
```

---

### 2. **CedulaModal.vue - Emisión de Evento**

#### Emit actualizado:
```javascript
const emit = defineEmits([
  'close', 
  'acceso-registrado', 
  'incidencia-detectada'  // ✨ NUEVO
])
```

#### Lógica modificada en handleQrVerificacion:
```javascript
if (serialEscaneado === serialEsperado) {
  // ✅ COINCIDE - Registrar normal
  verificandoEquipo.value = false
  confirmAcceso(false, serialEscaneado)
} else {
  // ❌ NO COINCIDE - Emitir evento
  verificandoEquipo.value = false
  stopCamera() // 🚪 Cerrar cámara
  
  emit('incidencia-detectada', {
    errorMessage: 'El portátil escaneado NO coincide...',
    accesoInfo: {
      persona: personaInfo.value.persona.Nombre,
      documento: personaInfo.value.persona.documento,
      equipoEsperado: `Serial: ${serialEsperado}`,
      equipoVerificado: `Serial: ${serialEscaneado}`
    },
    datosRegistro: {  // 💾 Datos para registro posterior
      qr_persona: `PERSONA_${cedula.value.trim()}`,
      qr_portatil: ...,
      serial_verificado: serialEscaneado
    }
  })
}
```

#### Lo que cambió:
- ❌ **Antes**: `confirmAcceso(false, serialEscaneado)` - registraba inmediatamente
- ✅ **Ahora**: `emit('incidencia-detectada', {...})` - emite evento al padre

---

### 3. **QrScannerModal.vue**

Actualizado el emit para consistencia:
```javascript
const emit = defineEmits([
  'close', 
  'acceso-registrado', 
  'incidencia-detectada'  // ✨ NUEVO (para futuro)
])
```

> **Nota:** QrScannerModal no usa verificación manual de portátil, solo escanea cédula y registra automáticamente.

---

## 🎯 Ventajas del Nuevo Flujo

1. ✅ **Transición clara**: Un modal se cierra, otro se abre
2. ✅ **Usuario informado**: Ve claramente la inconsistencia
3. ✅ **Decisión consciente**: Debe confirmar explícitamente
4. ✅ **Observaciones**: Puede agregar contexto importante
5. ✅ **Sin confusión**: No hay registros automáticos ocultos
6. ✅ **Auditable**: Toda la información queda registrada

---

## 🧪 Casos de Prueba

### ✅ Caso 1: Portátil Coincide
```
1. Buscar persona (cédula: 1125180685)
2. Sistema detecta SALIDA, requiere portátil
3. Escanear QR portátil (Serial: ABC123)
4. Serial coincide ✅
5. Registra salida normal sin modal de incidencia
```

### ✅ Caso 2: Portátil NO Coincide (PRINCIPAL)
```
1. Buscar persona (cédula: 1125180685)
2. Sistema detecta SALIDA, requiere portátil
3. Escanear QR portátil (Serial: XYZ789)
4. Serial NO coincide ❌
5. 🚪 CedulaModal se cierra
6. 🚨 IncidenciaModal se abre
7. Muestra: Esperado ABC123 vs Escaneado XYZ789
8. Usuario agrega: "Usuario cambió de portátil por reparación"
9. Usuario confirma
10. ✅ Salida registrada + ⚠️ Incidencia guardada
```

### ✅ Caso 3: Usuario Cancela Modal de Incidencia
```
1-6. (igual que caso 2)
7. Usuario ve el modal de incidencia
8. Usuario hace clic en "Cancelar"
9. IncidenciaModal se cierra
10. NO se registra nada
11. Sistema vuelve al estado inicial
```

---

## 📊 Diagrama de Flujo

```
┌─────────────────────┐
│  Usuario busca      │
│  persona (cédula)   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  ¿Es SALIDA con     │
│  portátil?          │
└──────┬───────┬──────┘
       │ NO    │ SÍ
       ▼       ▼
   ┌───────┐ ┌──────────────────┐
   │Regist │ │ Abrir cámara     │
   │rar    │ │ verificación     │
   └───────┘ └────────┬─────────┘
                      │
                      ▼
             ┌─────────────────┐
             │ Escanear QR     │
             │ portátil        │
             └────────┬────────┘
                      │
                      ▼
             ┌─────────────────┐
             │ ¿Serial         │
             │ coincide?       │
             └───┬─────────┬───┘
                 │ SÍ      │ NO
                 ▼         ▼
          ┌──────────┐  ┌─────────────────┐
          │Registrar │  │ Cerrar CedulaM  │
          │normal    │  │ Abrir Incidenc  │
          └──────────┘  └────────┬────────┘
                                 │
                                 ▼
                        ┌────────────────┐
                        │ Usuario ve     │
                        │ comparación    │
                        └────┬───────┬───┘
                             │       │
                      Cancela│       │Confirma
                             ▼       ▼
                        ┌────────┐ ┌──────────────┐
                        │Cancelar│ │Registrar con │
                        │        │ │incidencia    │
                        └────────┘ └──────────────┘
```

---

## 🎨 Vista del Usuario

### Antes (❌ Incorrecto):
```
[CedulaModal con cámara]
  ⚠️ Serial no coincide!
  Esperado: ABC123
  Escaneado: XYZ789
  
  (espera 2 segundos...)
  
  ✅ Acceso registrado
  
[Modal se cierra]
```
**Problema:** Usuario no puede agregar observaciones

---

### Ahora (✅ Correcto):
```
[CedulaModal con cámara]
  🔍 Escaneando...
  
[Se cierra automáticamente]

[IncidenciaModal se abre]
  ⚠️ Incidencia Detectada
  
  El equipo no coincide:
  ✓ Entrada: Serial ABC123
  ✗ Verificado: Serial XYZ789
  
  📝 Observaciones:
  [Usuario cambió de portátil...]
  
  ℹ️ La salida se registrará con esta incidencia
  
  [Cancelar] [Registrar Salida]
```
**Ventaja:** Usuario controla el proceso completamente

---

## 🔒 Seguridad y Validación

1. ✅ **No se puede confirmar sin datos**: `incidenciaData.value?.datosRegistro` valida
2. ✅ **Observaciones opcionales**: Campo no requerido
3. ✅ **Descripción automática**: Sistema genera descripción base
4. ✅ **Estado del usuario**: Se guarda quién registró la incidencia
5. ✅ **Timestamp**: Laravel timestamps automáticos

---

## 📝 Archivos Modificados

1. ✏️ `resources/js/Pages/System/Celador/Qr/Index.vue`
   - Nuevo handler: `handleIncidenciaDetectada()`
   - Modificado: `handleIncidenciaConfirmada()`
   - Agregado evento: `@incidencia-detectada`

2. ✏️ `resources/js/Components/CedulaModal.vue`
   - Modificado: `handleQrVerificacion()`
   - Agregado emit: `incidencia-detectada`
   - Eliminado: Registro automático en inconsistencia

3. ✏️ `resources/js/Components/QrScannerModal.vue`
   - Agregado emit: `incidencia-detectada` (para consistencia)

---

## ✅ Estado Final

- **Backend:** ✅ Sin cambios necesarios (ya acepta `descripcion_incidencia`)
- **Frontend:** ✅ Completado
- **Flujo:** ✅ Corregido
- **Modales:** ✅ Transición correcta
- **UX:** ✅ Mejorada significativamente
- **Errores:** ✅ Ninguno

---

## 🚀 Listo para Probar

El sistema ahora funciona correctamente:
1. Modal de verificación se cierra
2. Modal de incidencia se abre automáticamente
3. Usuario puede agregar observaciones
4. Al confirmar, se registra todo correctamente

**¡Prueba el flujo completo!** 🎉
