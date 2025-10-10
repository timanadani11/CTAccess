# 💻 Ejemplos de Código - Cambios Implementados

## 📋 Tabla de Contenidos
1. [Backend - PHP](#backend---php)
2. [Frontend - Vue.js](#frontend---vuejs)
3. [Base de Datos - SQL](#base-de-datos---sql)
4. [Flujo Completo](#flujo-completo)

---

## Backend - PHP

### 1️⃣ Método `procesarEntrada()` - ANTES vs AHORA

#### ❌ ANTES (No guardaba IDs)
```php
private function procesarEntrada($persona, $request, $usuario)
{
    $portatilId = null;
    $vehiculoId = null;

    // Solo usaba si escaneabas QR del portátil
    if ($request->qr_portatil) {
        $portatil = $this->buscarPortatilPorQr($request->qr_portatil);
        $portatilId = $portatil->portatil_id;
    }

    // Registrar entrada
    $acceso = Acceso::registrarEntrada(
        $persona->idPersona,
        $portatilId,  // ⚠️ Siempre NULL si no escaneabas
        $vehiculoId,  // ⚠️ Siempre NULL si no escaneabas
        $usuario->idUsuario
    );
}
```

#### ✅ AHORA (Detección automática)
```php
private function procesarEntrada($persona, $request, $usuario)
{
    // 🔥 CARGAR RELACIONES DE LA PERSONA
    $persona->load(['portatiles', 'vehiculos']);
    
    $portatilId = null;
    $vehiculoId = null;

    // 🔥 OBTENER AUTOMÁTICAMENTE si tiene portátil
    if ($persona->portatiles->isNotEmpty()) {
        $portatil = $persona->portatiles->first(); // Portátil principal
        $portatilId = $portatil->portatil_id;
        // ✅ Ahora SÍ se guarda el ID
    }

    // 🔥 OBTENER AUTOMÁTICAMENTE si tiene vehículo
    if ($persona->vehiculos->isNotEmpty()) {
        $vehiculo = $persona->vehiculos->first(); // Vehículo principal
        $vehiculoId = $vehiculo->id;
        // ✅ Ahora SÍ se guarda el ID
    }

    // Registrar entrada con los datos obtenidos
    $acceso = Acceso::registrarEntrada(
        $persona->idPersona,
        $portatilId,  // ✅ Con valor si tiene portátil
        $vehiculoId,  // ✅ Con valor si tiene vehículo
        $usuario->idUsuario
    );

    // Respuesta mejorada
    return back()->with('success', [
        'tipo' => 'entrada',
        'mensaje' => 'Entrada registrada exitosamente',
        'persona' => $persona->Nombre,
        'documento' => $persona->documento,
        'hora' => $acceso->fecha_entrada->format('H:i:s'),
        'portatil' => $portatilId ? 'Sí' : 'No',
        'vehiculo' => $vehiculoId ? 'Sí' : 'No',
        // 🔥 Nueva info detallada
        'portatil_info' => $portatilId ? 
            $persona->portatiles->first()->marca . ' ' . 
            $persona->portatiles->first()->modelo : null,
        'vehiculo_info' => $vehiculoId ? 
            $persona->vehiculos->first()->tipo . ' - ' . 
            $persona->vehiculos->first()->placa : null
    ]);
}
```

---

### 2️⃣ Método `procesarSalida()` - ANTES vs AHORA

#### ❌ ANTES (Verificación opcional)
```php
private function procesarSalida($persona, $accesoActivo, $request, $usuario)
{
    $errores = [];

    // Verificaba solo si escaneaste QR
    if ($accesoActivo->portatil_id) {
        if (!$request->qr_portatil) {
            $errores[] = 'Falta portátil'; // ⚠️ Mensaje genérico
        }
    }

    // Registraba salida igual
    $accesoActivo->marcarSalida($usuario->idUsuario);
}
```

#### ✅ AHORA (Verificación OBLIGATORIA)
```php
private function procesarSalida($persona, $accesoActivo, $request, $usuario)
{
    $errores = [];
    $requiereVerificacion = false;

    // 🔥 VERIFICACIÓN OBLIGATORIA DE PORTÁTIL
    if ($accesoActivo->portatil_id) {
        $requiereVerificacion = true;
        
        if (!$request->qr_portatil) {
            // ⚠️ NO escaneó - BLOQUEADO
            $errores[] = 'Debe escanear el QR del portátil con el que entró (Serial: ' . 
                $accesoActivo->portatil->serial . ')';
        } else {
            $portatil = $this->buscarPortatilPorQr($request->qr_portatil);
            
            if ($portatil->portatil_id != $accesoActivo->portatil_id) {
                // ⚠️ Portátil DIFERENTE - INCIDENCIA
                $errores[] = 'El portátil escaneado NO coincide. ' .
                    'Entró con: ' . $accesoActivo->portatil->serial . ', ' .
                    'Escaneó: ' . $portatil->serial;
            }
        }
    }

    // 🔥 VERIFICACIÓN OBLIGATORIA DE VEHÍCULO
    if ($accesoActivo->vehiculo_id) {
        $requiereVerificacion = true;
        
        if (!$request->qr_vehiculo) {
            $errores[] = 'Debe escanear el QR del vehículo con el que entró (Placa: ' . 
                $accesoActivo->vehiculo->placa . ')';
        } else {
            $vehiculo = $this->buscarVehiculoPorQr($request->qr_vehiculo);
            
            if ($vehiculo->id != $accesoActivo->vehiculo_id) {
                $errores[] = 'El vehículo escaneado NO coincide. ' .
                    'Entró con: ' . $accesoActivo->vehiculo->placa . ', ' .
                    'Escaneó: ' . $vehiculo->placa;
            }
        }
    }

    // 🔥 SI HAY ERRORES → INCIDENCIA + BLOQUEO
    if (!empty($errores)) {
        $descripcion = 'Inconsistencias en salida: ' . implode(' | ', $errores);
        $incidencia = $accesoActivo->marcarIncidencia($descripcion, $usuario->idUsuario);
        
        return back()->with('warning', [
            'tipo' => 'incidencia',
            'mensaje' => '⚠️ INCIDENCIA DETECTADA - Salida bloqueada',
            'persona' => $persona->Nombre,
            'documento' => $persona->documento,
            'errores' => $errores,
            'incidencia_id' => $incidencia->incidenciaId,
            'acceso_id' => $accesoActivo->idAcceso,
            'requiere_autorizacion' => true
        ]);
    }

    // ✅ TODO CORRECTO - Registrar salida
    $accesoActivo->marcarSalida($usuario->idUsuario);

    return back()->with('success', [
        'tipo' => 'salida',
        'mensaje' => '✅ Salida registrada exitosamente',
        'persona' => $persona->Nombre,
        'documento' => $persona->documento,
        'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i:s'),
        'hora_salida' => $accesoActivo->fecha_salida->format('H:i:s'),
        'duracion' => $accesoActivo->duracion,
        'verificaciones_ok' => $requiereVerificacion
    ]);
}
```

---

### 3️⃣ Método `formatearRespuestaPersona()` - Respuesta completa

```php
private function formatearRespuestaPersona($persona)
{
    // Verificar si tiene acceso activo
    $accesoActivo = $persona->getAccesoActivo();
    
    // 🔥 CARGAR RELACIONES
    $persona->load(['portatiles', 'vehiculos']);
    
    // 🔥 INFO PORTÁTIL ASOCIADO
    $portatilInfo = null;
    if ($persona->portatiles->isNotEmpty()) {
        $portatil = $persona->portatiles->first();
        $portatilInfo = [
            'portatil_id' => $portatil->portatil_id,
            'marca' => $portatil->marca,
            'modelo' => $portatil->modelo,
            'serial' => $portatil->serial,
            'descripcion' => $portatil->marca . ' ' . $portatil->modelo . 
                ' (Serial: ' . $portatil->serial . ')'
        ];
    }
    
    // 🔥 INFO VEHÍCULO ASOCIADO
    $vehiculoInfo = null;
    if ($persona->vehiculos->isNotEmpty()) {
        $vehiculo = $persona->vehiculos->first();
        $vehiculoInfo = [
            'id' => $vehiculo->id,
            'tipo' => $vehiculo->tipo,
            'placa' => $vehiculo->placa,
            'descripcion' => $vehiculo->tipo . ' - Placa: ' . $vehiculo->placa
        ];
    }
    
    // 🔥 CONSTRUIR RESPUESTA COMPLETA
    $response = [
        'persona' => [
            'idPersona' => $persona->idPersona,
            'Nombre' => $persona->Nombre,
            'documento' => $persona->documento,
            'TipoPersona' => $persona->TipoPersona,
            'correo' => $persona->correo
        ],
        'tiene_acceso_activo' => $accesoActivo ? true : false,
        'es_entrada' => !$accesoActivo,
        'es_salida' => $accesoActivo ? true : false,
        'portatil_asociado' => $portatilInfo,
        'vehiculo_asociado' => $vehiculoInfo,
        'tiene_portatil' => $portatilInfo !== null,
        'tiene_vehiculo' => $vehiculoInfo !== null,
        'mensaje_accion' => $accesoActivo ? 'SALIDA detectada' : 'ENTRADA detectada'
    ];
    
    // 🔥 SI ES SALIDA, agregar datos del acceso activo
    if ($accesoActivo) {
        $accesoActivo->load(['portatil', 'vehiculo']);
        
        $response['acceso_activo'] = [
            'idAcceso' => $accesoActivo->idAcceso,
            'fecha_entrada' => $accesoActivo->fecha_entrada->format('Y-m-d H:i:s'),
            'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i'),
            'portatil_entrada' => $accesoActivo->portatil ? [
                'portatil_id' => $accesoActivo->portatil->portatil_id,
                'marca' => $accesoActivo->portatil->marca,
                'modelo' => $accesoActivo->portatil->modelo,
                'serial' => $accesoActivo->portatil->serial,
                'descripcion' => $accesoActivo->portatil->marca . ' ' . 
                    $accesoActivo->portatil->modelo . ' (Serial: ' . 
                    $accesoActivo->portatil->serial . ')'
            ] : null,
            'vehiculo_entrada' => $accesoActivo->vehiculo ? [
                'id' => $accesoActivo->vehiculo->id,
                'tipo' => $accesoActivo->vehiculo->tipo,
                'placa' => $accesoActivo->vehiculo->placa,
                'descripcion' => $accesoActivo->vehiculo->tipo . ' - Placa: ' . 
                    $accesoActivo->vehiculo->placa
            ] : null,
            'requiere_verificacion_portatil' => $accesoActivo->portatil_id !== null,
            'requiere_verificacion_vehiculo' => $accesoActivo->vehiculo_id !== null
        ];
    }
    
    return response()->json($response);
}
```

---

## Frontend - Vue.js

### 4️⃣ Método `buscarPersona()` - ANTES vs AHORA

#### ❌ ANTES
```javascript
const buscarPersona = async (qrPersona) => {
  const response = await fetch(route('system.celador.qr.buscar-persona'), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': page.props.csrf_token
    },
    body: JSON.stringify({ qr_persona: qrPersona })
  })
  
  const result = await response.json()
  
  if (response.ok) {
    personaInfo.value = result
    showPersonaInfo.value = true
    scannedCodes.value.persona = qrPersona
    
    // Procesaba directamente
    if (registroInstantaneo.value) {
      await procesarAcceso()
    }
  }
}
```

#### ✅ AHORA (Lógica inteligente)
```javascript
const buscarPersona = async (qrPersona) => {
  try {
    const response = await fetch(route('system.celador.qr.buscar-persona'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify({ qr_persona: qrPersona })
    })
    
    const result = await response.json()
    
    if (response.ok) {
      personaInfo.value = result
      showPersonaInfo.value = true
      scannedCodes.value.persona = qrPersona
      
      // 🔥 DETERMINAR ACCIÓN
      const esEntrada = result.es_entrada
      const esSalida = result.es_salida
      
      // 🔥 MENSAJE PARA CELADOR
      let mensaje = `${result.persona.Nombre} - ${result.mensaje_accion}`
      
      // 🔥 SI ES ENTRADA - Mostrar equipos detectados
      if (esEntrada) {
        const elementos = []
        if (result.tiene_portatil) {
          elementos.push(`✓ Portátil: ${result.portatil_asociado.marca} ${result.portatil_asociado.modelo}`)
        }
        if (result.tiene_vehiculo) {
          elementos.push(`✓ Vehículo: ${result.vehiculo_asociado.placa}`)
        }
        
        if (elementos.length > 0) {
          showNotification('info', `${mensaje}\n${elementos.join('\n')}`)
        }
      }
      
      // 🔥 SI ES SALIDA - Verificar si necesita escanear equipos
      if (esSalida && result.acceso_activo) {
        const requiereVerificaciones = []
        
        if (result.acceso_activo.requiere_verificacion_portatil) {
          requiereVerificaciones.push(
            `📱 Debe escanear QR del portátil: ${result.acceso_activo.portatil_entrada.serial}`
          )
        }
        
        if (result.acceso_activo.requiere_verificacion_vehiculo) {
          requiereVerificaciones.push(
            `🚗 Debe escanear QR del vehículo: ${result.acceso_activo.vehiculo_entrada.placa}`
          )
        }
        
        if (requiereVerificaciones.length > 0) {
          showNotification('warning', 
            `SALIDA - Verificación requerida:\n${requiereVerificaciones.join('\n')}`
          )
          // ⚠️ NO procesar automáticamente - DEBE escanear equipos
          showConfirmModal.value = true
          return
        }
      }
      
      // ✅ Procesar si está en modo instantáneo Y no requiere verificaciones
      if (registroInstantaneo.value && (!esSalida || !result.acceso_activo?.requiere_verificacion_portatil)) {
        await procesarAcceso()
      } else {
        showConfirmModal.value = true
      }
    } else {
      throw new Error(result.message || 'Persona no encontrada')
    }
  } catch (error) {
    console.error('Error al buscar persona:', error)
    showNotification('error', error.message || 'Persona no encontrada')
  }
}
```

---

## Base de Datos - SQL

### 5️⃣ Consultas Útiles

#### Ver accesos con equipos
```sql
SELECT 
    a.idAcceso,
    a.fecha_entrada,
    a.fecha_salida,
    a.estado,
    p.Nombre as persona,
    p.documento,
    -- 🔥 Ahora estas columnas tienen valores
    a.portatil_id,
    port.serial as portatil_serial,
    port.marca as portatil_marca,
    a.vehiculo_id,
    v.placa as vehiculo_placa,
    v.tipo as vehiculo_tipo
FROM accesos a
JOIN personas p ON a.persona_id = p.idPersona
LEFT JOIN portatiles port ON a.portatil_id = port.portatil_id
LEFT JOIN vehiculos v ON a.vehiculo_id = v.id
ORDER BY a.fecha_entrada DESC
LIMIT 10;
```

#### Ver personas con sus equipos
```sql
SELECT 
    p.idPersona,
    p.Nombre,
    p.documento,
    -- Portátil
    port.portatil_id,
    port.marca,
    port.modelo,
    port.serial,
    -- Vehículo
    v.id as vehiculo_id,
    v.tipo,
    v.placa
FROM personas p
LEFT JOIN portatiles port ON p.idPersona = port.persona_id
LEFT JOIN vehiculos v ON p.idPersona = v.persona_id
WHERE p.TipoPersona = 'Estudiante'
ORDER BY p.Nombre;
```

#### Ver incidencias por equipos
```sql
SELECT 
    i.incidenciaId,
    i.fecha,
    i.descripcion,
    i.tipo,
    p.Nombre,
    a.fecha_entrada,
    port.serial as portatil_esperado,
    v.placa as vehiculo_esperado
FROM incidencias i
JOIN accesos a ON i.acceso_id = a.idAcceso
JOIN personas p ON a.persona_id = p.idPersona
LEFT JOIN portatiles port ON a.portatil_id = port.portatil_id
LEFT JOIN vehiculos v ON a.vehiculo_id = v.id
WHERE i.tipo = 'salida'
ORDER BY i.fecha DESC;
```

---

## Flujo Completo

### 6️⃣ Ejemplo Real - Entrada

```javascript
// 1️⃣ Usuario: Escanea QR o digita cédula
handleQrScanned({ type: 'persona', data: 'PERSONA_123456789' })

// 2️⃣ Frontend: Busca persona
buscarPersona('PERSONA_123456789')

// 3️⃣ Backend: Respuesta
{
  "persona": {
    "Nombre": "Juan Pérez",
    "documento": "123456789"
  },
  "es_entrada": true,
  "tiene_portatil": true,
  "portatil_asociado": {
    "portatil_id": 12,
    "marca": "Dell",
    "modelo": "Latitude 5420",
    "serial": "ABC123XYZ"
  },
  "tiene_vehiculo": true,
  "vehiculo_asociado": {
    "id": 8,
    "tipo": "Automóvil",
    "placa": "XYZ789"
  }
}

// 4️⃣ Frontend: Muestra notificación
showNotification('info', `
  Juan Pérez - ENTRADA detectada
  ✓ Portátil: Dell Latitude 5420
  ✓ Vehículo: XYZ789
`)

// 5️⃣ Backend: Registra entrada
Acceso::create([
  'persona_id' => 5,
  'portatil_id' => 12,  // ✅ Guardado automáticamente
  'vehiculo_id' => 8,   // ✅ Guardado automáticamente
  'fecha_entrada' => '2025-10-07 23:47:18',
  'estado' => 'activo'
])

// 6️⃣ Base de datos:
/*
idAcceso: 3
persona_id: 5
portatil_id: 12      ← ✅ Con valor
vehiculo_id: 8       ← ✅ Con valor
fecha_entrada: 2025-10-07 23:47:18
estado: activo
*/
```

### 7️⃣ Ejemplo Real - Salida con Verificación

```javascript
// 1️⃣ Usuario: Escanea QR de la misma persona
handleQrScanned({ type: 'persona', data: 'PERSONA_123456789' })

// 2️⃣ Backend: Respuesta (detecta acceso activo)
{
  "persona": { "Nombre": "Juan Pérez" },
  "es_salida": true,
  "acceso_activo": {
    "requiere_verificacion_portatil": true,
    "portatil_entrada": {
      "serial": "ABC123XYZ",
      "descripcion": "Dell Latitude 5420 (Serial: ABC123XYZ)"
    },
    "requiere_verificacion_vehiculo": true,
    "vehiculo_entrada": {
      "placa": "XYZ789",
      "descripcion": "Automóvil - Placa: XYZ789"
    }
  }
}

// 3️⃣ Frontend: Muestra alerta
showNotification('warning', `
  SALIDA - Verificación requerida:
  📱 Debe escanear QR del portátil: ABC123XYZ
  🚗 Debe escanear QR del vehículo: XYZ789
`)

// 4️⃣ Usuario: Escanea QR del portátil
handleQrScanned({ type: 'portatil', data: 'PORTATIL_ABC123XYZ' })

// 5️⃣ Usuario: Escanea QR del vehículo
handleQrScanned({ type: 'vehiculo', data: 'VEHICULO_XYZ789' })

// 6️⃣ Frontend: Procesa salida
procesarAcceso()

// 7️⃣ Backend: Verifica coincidencias
if ($portatil->portatil_id == $accesoActivo->portatil_id &&
    $vehiculo->id == $accesoActivo->vehiculo_id) {
    // ✅ TODO CORRECTO
    $accesoActivo->marcarSalida($usuario->idUsuario)
}

// 8️⃣ Base de datos actualizada:
/*
idAcceso: 3
persona_id: 5
portatil_id: 12
vehiculo_id: 8
fecha_entrada: 2025-10-07 23:47:18
fecha_salida: 2025-10-08 17:30:00  ← ✅ Salida registrada
estado: finalizado
*/
```

---

## 🎓 Conceptos Clave

### Relaciones Eloquent
```php
// Persona tiene muchos portátiles
$persona->portatiles->first()

// Persona tiene muchos vehículos
$persona->vehiculos->first()

// Acceso pertenece a persona, portátil, vehículo
$acceso->persona
$acceso->portatil
$acceso->vehiculo
```

### Eager Loading (Optimización)
```php
// Cargar relaciones de una vez (evita N+1)
$persona->load(['portatiles', 'vehiculos']);
```

### Respuesta JSON estructurada
```php
return response()->json([
    'dato1' => 'valor1',
    'dato2' => ['sub1' => 'valor'],
    'dato3' => $objeto->toArray()
]);
```

---

**Fin de ejemplos** ✅
