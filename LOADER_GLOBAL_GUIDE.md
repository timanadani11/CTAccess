# 🔄 Loader Global - CTAccess

## 📋 Descripción

Sistema de **loader global** implementado en todo el proyecto que muestra automáticamente un indicador de carga cuando:

✅ **Navegas entre páginas** (Inertia.js)  
✅ **Realizas peticiones HTTP** (Axios)  
✅ **Lo activas manualmente** (Composable)

---

## 🎨 Diseño del Loader

El loader utiliza un diseño **3D animado** con:
- 🔵 Color primario: `#00d9ff` (azul cian CTAccess)
- 🔵 Color secundario: `#00304D` (azul oscuro CTAccess)
- ⚡ Animación suave y moderna
- 🌑 Overlay semi-transparente con blur

**Fuente del diseño**: [Uiverse.io by kerolos23](https://uiverse.io)

---

## 🚀 Uso Automático

### 1. **Navegación con Inertia.js**

El loader se muestra **automáticamente** cuando:

```javascript
import { router } from '@inertiajs/vue3'

// Navegación estándar
router.visit('/system/celador/personas')

// Navegación con Link
<Link href="/system/admin/dashboard">Dashboard</Link>
```

### 2. **Peticiones con Axios**

El loader se muestra **automáticamente** cuando:

```javascript
// GET
const response = await axios.get('/api/personas')

// POST
await axios.post('/api/accesos', formData)

// PUT/PATCH/DELETE
await axios.put(`/api/personas/${id}`, data)
await axios.delete(`/api/personas/${id}`)
```

---

## 🎮 Uso Manual (Composable)

Para casos especiales donde necesites control manual:

### Importar el Composable

```javascript
import { useLoader } from '@/composables/useLoader'

const { show, hide, withLoader } = useLoader()
```

### Ejemplos de Uso

#### 1. **Mostrar/Ocultar Manualmente**

```javascript
// Mostrar loader
show()

// Hacer algo...
await procesarDatos()

// Ocultar loader
hide()
```

#### 2. **Envolver Función Asíncrona (Recomendado)**

```javascript
await withLoader(async () => {
  await operacionLarga()
  await otraOperacion()
})
```

#### 3. **Ejemplo Real: Subida de Archivos**

```javascript
const subirArchivo = async (file) => {
  await withLoader(async () => {
    const formData = new FormData()
    formData.append('archivo', file)
    
    await axios.post('/api/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    console.log('Archivo subido!')
  })
}
```

#### 4. **Ejemplo: Operación con Múltiples Pasos**

```javascript
const sincronizarDatos = async () => {
  show() // Mostrar loader
  
  try {
    await paso1()
    await paso2()
    await paso3()
    
    alert('Sincronización completada!')
  } catch (error) {
    console.error('Error:', error)
  } finally {
    hide() // Siempre ocultar
  }
}
```

---

## 🔧 Configuración Avanzada

### Personalizar Colores

Edita `resources/js/Components/GlobalLoader.vue`:

```css
.loader {
  color: #00d9ff; /* Color primario */
}

.loader:after {
  color: #00304D; /* Color secundario */
}
```

### Personalizar Duración del Overlay

```javascript
// En GlobalLoader.vue
setTimeout(() => {
  isLoading.value = false
}, 300) // Cambiar de 200ms a 300ms
```

### Desactivar para Peticiones Específicas

```javascript
// Opción 1: Usar fetch en lugar de axios
const response = await fetch('/api/datos')

// Opción 2: Configurar axios para una petición específica
const response = await axios.get('/api/datos', {
  headers: {
    'X-Skip-Loader': 'true' // Custom header (requiere modificación)
  }
})
```

---

## 📂 Archivos del Sistema

```
resources/js/
├── Components/
│   └── GlobalLoader.vue       # 🔥 Componente principal
├── composables/
│   └── useLoader.js            # 🎮 Composable para control manual
├── Layouts/
│   ├── System/SystemLayout.vue      # ✅ Incluido
│   ├── AuthenticatedLayout.vue      # ✅ Incluido
│   └── GuestLayout.vue              # ✅ Incluido
└── Pages/
    └── Home.vue                      # ✅ Incluido
```

---

## 🐛 Troubleshooting

### El loader no aparece

1. **Verifica que esté en el layout**:
```vue
<GlobalLoader />
```

2. **Revisa la consola del navegador**:
```bash
# Debería aparecer en las peticiones
X-CSRF-TOKEN: <token>
X-Requested-With: XMLHttpRequest
```

3. **Asegúrate de usar axios o Inertia**:
```javascript
// ❌ NO activará el loader
fetch('/api/datos')

// ✅ SÍ activará el loader
axios.get('/api/datos')
```

### El loader no desaparece

Verifica que no haya errores en las peticiones:

```javascript
try {
  await axios.get('/api/datos')
} catch (error) {
  // El loader se oculta automáticamente
  console.error(error)
}
```

---

## ✨ Mejores Prácticas

### ✅ **DO (Hacer)**

```javascript
// Usar axios para peticiones
await axios.post('/api/personas', data)

// Usar withLoader para operaciones largas
await withLoader(async () => {
  await procesarDatos()
})

// Usar try/catch para manejar errores
try {
  await axios.get('/api/datos')
} catch (error) {
  console.error(error)
}
```

### ❌ **DON'T (No hacer)**

```javascript
// No usar fetch directo (no activa loader)
await fetch('/api/datos')

// No olvidar hide() en operaciones manuales
show()
await procesarDatos()
// ❌ Falta hide()

// No usar múltiples show() sin hide()
show()
show() // ❌ Innecesario
```

---

## 🎯 Casos de Uso Reales

### 1. **Búsqueda de Persona por Cédula**

```javascript
const buscarPersona = async (cedula) => {
  try {
    // El loader se activa automáticamente
    const response = await axios.post('/system/celador/qr/buscar-persona', {
      qr_persona: `PERSONA_${cedula}`
    })
    
    personaInfo.value = response.data
  } catch (error) {
    console.error('Error:', error)
    // El loader se oculta automáticamente
  }
}
```

### 2. **Registro de Acceso**

```javascript
const registrarAcceso = async () => {
  // El loader se activa automáticamente con axios
  await axios.post('/system/celador/qr/registrar', {
    qr_persona: codigoQR
  })
  
  alert('Acceso registrado!')
  // El loader se oculta automáticamente
}
```

### 3. **Generación de PDF (Operación Larga)**

```javascript
const generarPDF = async () => {
  await withLoader(async () => {
    // Mostrar loader durante todo el proceso
    const response = await axios.get('/historial/pdf', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'historial.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()
  })
}
```

---

## 📊 Ventajas del Sistema

✅ **Automático**: No requiere código adicional en el 95% de los casos  
✅ **Consistente**: Mismo diseño en toda la aplicación  
✅ **Performante**: Se oculta automáticamente con delay mínimo  
✅ **UX mejorada**: El usuario siempre sabe cuando algo está cargando  
✅ **Flexible**: Control manual cuando lo necesites  

---

## 🔄 Actualización y Mantenimiento

### Actualizar Diseño del Loader

Edita `resources/js/Components/GlobalLoader.vue` y modifica los estilos CSS.

### Agregar a Nuevo Layout

```vue
<template>
  <div>
    <!-- Tu contenido -->
    
    <!-- Agregar al final -->
    <GlobalLoader />
  </div>
</template>
```

---

## 📚 Referencias

- [Inertia.js Events](https://inertiajs.com/events)
- [Axios Interceptors](https://axios-http.com/docs/interceptors)
- [Uiverse.io - Loaders](https://uiverse.io/loaders)

---

**Implementado el:** 12 de Octubre, 2025  
**Versión:** 1.0  
**Autor:** GitHub Copilot  
