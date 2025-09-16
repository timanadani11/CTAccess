# CTAccess - Backend de Personas (Guía en Español)

Esta guía resume los cambios realizados en el backend (Laravel) para el módulo de control de acceso con `Personas`, `Portátiles` y `Vehículos`, e incluye ejemplos de cómo integrarlo fácilmente con un frontend en Vue 3 + Tailwind.

## ¿Qué se implementó?

- Endpoints RESTful para `personas` con CRUD completo.
- Asociación opcional de `portátiles` y `vehículos` al crear/actualizar una persona.
- Validaciones de campos (documento/placa/QR únicos) a nivel de request y base de datos.
- Respuestas JSON claras y estables usando API Resources.
- Organización por capas: Controladores, Form Requests (validación) y Servicios (lógica de negocio transaccional).

### Archivos relevantes

- Rutas API: `routes/api.php`
- Controlador: `app/Http/Controllers/PersonaController.php`
- Servicio: `app/Services/PersonaService.php`
- Validaciones: `app/Http/Requests/StorePersonaRequest.php`, `app/Http/Requests/UpdatePersonaRequest.php`
- Resources (JSON): `app/Http/Resources/PersonaResource.php`, `PortatilResource.php`, `VehiculoResource.php`
- Modelos: `app/Models/Persona.php`, `Portatil.php`, `Vehiculo.php`
- Migración de unicidad: `database/migrations/2025_09_15_201000_add_unique_indexes_to_core_tables.php`

## Endpoints

Base: `/api/v1/personas` (Laravel agrega prefijo `/api` automáticamente a `routes/api.php`)

- GET `/api/v1/personas?with_relations=1&per_page=15` — Lista paginada, con relaciones opcionales.
- GET `/api/v1/personas/{idPersona}` — Detalle incluyendo `portatiles` y `vehiculos`.
- POST `/api/v1/personas` — Crear persona (con relaciones opcionales).
- PUT/PATCH `/api/v1/personas/{idPersona}` — Actualizar persona (parcial/total, con relaciones).
- DELETE `/api/v1/personas/{idPersona}` — Eliminar persona y sus relaciones.

## Validaciones clave

- `personas.documento`: único (puede ser null).
- `portatiles.qrCode`: único.
- `vehiculos.placa`: único.

Además:
- `nombre` y `tipoPersona` son requeridos al crear.
- En update, los campos son opcionales (parciales), se mantienen los valores existentes si no se envían.

## Formato de respuesta JSON

Las respuestas usan API Resources para un contrato estable:

- Persona:
```json
{
  "id": 1,
  "documento": "12345678",
  "nombre": "Juan Pérez",
  "tipoPersona": "Empleado",
  "foto": "https://.../juan.jpg",
  "createdAt": "2025-09-15T01:23:45.000000Z",
  "updatedAt": "2025-09-15T01:23:45.000000Z",
  "portatiles": [ { "id": 5, "qrCode": "QR-ABC-001", "marca": "Dell", "modelo": "Latitude 7420" } ],
  "vehiculos": [ { "id": 10, "tipo": "Auto", "placa": "ABC-123" } ]
}
```

Las colecciones (index) incluyen paginación estándar de Laravel y `status: "success"` en `additional`.

## Ejemplos de payloads para el frontend

- Crear persona con relaciones:
```json
{
  "documento": "12345678",
  "nombre": "Juan Pérez",
  "tipoPersona": "Empleado",
  "foto": "https://mi-cdn/fotos/juan.jpg",
  "portatiles": [
    { "qrCode": "QR-ABC-001", "marca": "Dell", "modelo": "Latitude 7420" },
    { "qrCode": "QR-XYZ-002", "marca": "HP", "modelo": "ProBook 450" }
  ],
  "vehiculos": [
    { "tipo": "Auto", "placa": "ABC-123" },
    { "tipo": "Moto", "placa": "XYZ-987" }
  ]
}
```

- Actualizar persona (parcial) con alta/edición de relaciones:
```json
{
  "nombre": "Juan P. Actualizado",
  "portatiles": [
    { "id": 5, "marca": "Lenovo", "modelo": "T14 Gen 3" },
    { "qrCode": "QR-NEW-003", "marca": "Apple", "modelo": "MacBook Air M2" }
  ],
  "vehiculos": [
    { "id": 10, "placa": "DEF-456" },
    { "tipo": "Camioneta", "placa": "GHI-789" }
  ]
}
```

## Integración rápida con Vue 3 + Tailwind

A continuación ejemplos simples usando Axios.

### Instalar Axios (si no lo tienes)

```bash
npm i axios
```

### Listar personas en una vista (tabla Tailwind)

```vue
<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Personas</h1>
    <table class="min-w-full bg-white shadow rounded">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3">ID</th>
          <th class="p-3">Documento</th>
          <th class="p-3">Nombre</th>
          <th class="p-3">Tipo</th>
          <th class="p-3">Portátiles</th>
          <th class="p-3">Vehículos</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in personas" :key="p.id" class="border-t">
          <td class="p-3">{{ p.id }}</td>
          <td class="p-3">{{ p.documento ?? '—' }}</td>
          <td class="p-3">{{ p.nombre }}</td>
          <td class="p-3">{{ p.tipoPersona }}</td>
          <td class="p-3">{{ p.portatiles?.length ?? 0 }}</td>
          <td class="p-3">{{ p.vehiculos?.length ?? 0 }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const personas = ref([])

onMounted(async () => {
  const { data } = await axios.get('/api/v1/personas', {
    params: { with_relations: 1, per_page: 20 }
  })
  // data.data contiene el array paginado
  personas.value = data.data
})
</script>
```

### Crear persona desde un formulario simple

```vue
<script setup>
import axios from 'axios'
import { reactive } from 'vue'

const form = reactive({
  documento: '',
  nombre: '',
  tipoPersona: '',
  foto: '',
  portatiles: [],
  vehiculos: []
})

async function crearPersona() {
  try {
    const { data } = await axios.post('/api/v1/personas', form)
    // Manejar éxito (toasts, redirección, etc.)
    console.log('Creado', data)
  } catch (e) {
    // Manejar errores de validación (422)
    console.error(e?.response?.data)
  }
}
</script>
```

### Ver detalle de persona

```js
import axios from 'axios'

async function obtenerPersona(idPersona) {
  const { data } = await axios.get(`/api/v1/personas/${idPersona}`)
  return data
}
```

## Puesta en marcha

1. Ejecutar migraciones (aplican índices únicos):
   ```bash
   php artisan migrate
   ```
2. Servir la aplicación:
   ```bash
   php artisan serve
   ```
3. Consumir endpoints desde el frontend en `http://localhost:8000/api/v1/...`

## Notas

- Las relaciones se gestionan desde `PersonaService` con transacciones para mantener consistencia.
- El modelo `Persona` usa `idPersona` como clave primaria y para el route model binding.
- Si necesitas CRUD separado para `portátiles` o `vehículos`, se puede añadir fácilmente siguiendo el mismo patrón (Controller + Requests + Resources).
