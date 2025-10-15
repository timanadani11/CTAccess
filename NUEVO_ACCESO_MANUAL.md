# ✅ Nuevo Acceso Manual - Módulo Administrador/Celador

## 🎯 Funcionalidad Implementada

Ahora los **administradores** y **celadores** pueden crear accesos manualmente desde la interfaz web, similar a los otros módulos (Personas, Portátiles, Vehículos).

### ✨ Características:

1. ✅ **Botón "Nuevo Acceso"** en el header
2. ✅ **Modal con formulario completo**
3. ✅ **Selección de persona** (requerido)
4. ✅ **Selección de portátil** (opcional, carga automática según persona)
5. ✅ **Selección de vehículo** (opcional, carga automática según persona)
6. ✅ **Fecha y hora de entrada** (con datetime picker)
7. ✅ **Validaciones backend** (pertenencia de portátil/vehículo a la persona)
8. ✅ **Diseño responsive** y consistente con el resto del sistema

---

## 📸 Vista del Modal

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃   ➕  Nuevo Acceso                               ✕   ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃                                                       ┃
┃   👤 Persona *                                        ┃
┃   ┌─────────────────────────────────────────────┐   ┃
┃   │ Seleccionar persona...                   ▼  │   ┃
┃   │ • Juan Pérez - 1234567890 (ESTUDIANTE)      │   ┃
┃   │ • María García - 9876543210 (DOCENTE)       │   ┃
┃   └─────────────────────────────────────────────┘   ┃
┃                                                       ┃
┃   💻 Portátil (opcional)                              ┃
┃   ┌─────────────────────────────────────────────┐   ┃
┃   │ Sin portátil                             ▼  │   ┃
┃   │ • DELL LATITUDE - VJHYV6IV                   │   ┃
┃   └─────────────────────────────────────────────┘   ┃
┃   📝 Esta persona no tiene portátiles registrados    ┃
┃                                                       ┃
┃   🚗 Vehículo (opcional)                              ┃
┃   ┌─────────────────────────────────────────────┐   ┃
┃   │ Sin vehículo                             ▼  │   ┃
┃   │ • Automóvil - ABC123                         │   ┃
┃   └─────────────────────────────────────────────┘   ┃
┃                                                       ┃
┃   📅 Fecha y hora de entrada *                        ┃
┃   ┌─────────────────────────────────────────────┐   ┃
┃   │ 2025-10-14T19:30                         📅  │   ┃
┃   └─────────────────────────────────────────────┘   ┃
┃                                                       ┃
┃   ℹ️  Registro de acceso                              ┃
┃   El acceso se creará en estado activo. El           ┃
┃   portátil y vehículo son opcionales, solo se        ┃
┃   mostrarán los que pertenecen a la persona.         ┃
┃                                                       ┃
┃   ┌──────────────┐  ┌──────────────────────────┐   ┃
┃   │   Cancelar   │  │  💾 Registrar Acceso     │   ┃
┃   └──────────────┘  └──────────────────────────┘   ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
```

---

## 🔄 Flujo de Funcionamiento

### **1. Usuario hace click en "Nuevo Acceso"**
```
1. Se abre el modal
2. Se cargan todas las personas del sistema
3. Los campos de portátil y vehículo están deshabilitados
4. Fecha/hora se prellenae con la actual
```

### **2. Usuario selecciona una persona**
```
1. Se habilitan los campos de portátil y vehículo
2. Se hace petición AJAX para cargar portátiles de esa persona
3. Se hace petición AJAX para cargar vehículos de esa persona
4. Si no tiene portátiles/vehículos, se muestra mensaje informativo
```

### **3. Usuario selecciona portátil/vehículo (opcional)**
```
1. Solo se muestran los que pertenecen a la persona seleccionada
2. Puede dejar en blanco si no ingresa con equipos
```

### **4. Usuario hace click en "Registrar Acceso"**
```
1. Se validan los datos en el backend
2. Se verifica que portátil/vehículo pertenezcan a la persona
3. Se crea el acceso en estado "activo"
4. Se cierra el modal
5. Se actualiza la tabla automáticamente
6. Se muestra mensaje de éxito
```

---

## 📁 Archivos Modificados

### **1. Frontend (Vue)**
**Archivo**: `resources/js/Pages/System/Celador/Accesos/Index.vue`

**Cambios realizados**:
```diff
+ import Modal from '@/Components/Modal.vue'
+ import { useForm } from '@inertiajs/vue3'
+ import axios from 'axios'

+ // Estado del modal
+ const showModal = ref(false)
+ const personas = ref([])
+ const portatiles = ref([])
+ const vehiculos = ref([])

+ // Formulario
+ const form = useForm({
+   persona_id: '',
+   portatil_id: '',
+   vehiculo_id: '',
+   fecha_entrada: new Date().toISOString().slice(0, 16),
+ })

+ // Funciones
+ const openCreateModal = async () => { ... }
+ const loadPersonas = async () => { ... }
+ const loadPortatilesPersona = async (personaId) => { ... }
+ const loadVehiculosPersona = async (personaId) => { ... }
+ const submit = () => { ... }

+ // Watch para cargar datos cuando cambia persona
+ watch(() => form.persona_id, (newPersonaId) => { ... })

+ // Botón en el header
+ <button @click="openCreateModal">Nuevo Acceso</button>

+ // Modal con formulario completo
+ <Modal :show="showModal" @close="closeModal">...</Modal>
```

---

### **2. Backend (Controller)**
**Archivo**: `app/Http/Controllers/System/Celador/AccesoController.php`

**Nuevos imports**:
```php
use App\Models\Persona;
use App\Models\Portatil;
use App\Models\Vehiculo;
```

**Nuevos métodos**:

#### `store()` - Crear nuevo acceso
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'persona_id' => ['required', 'exists:personas,idPersona'],
        'portatil_id' => ['nullable', 'exists:portatiles,portatil_id'],
        'vehiculo_id' => ['nullable', 'exists:vehiculos,vehiculo_id'],
        'fecha_entrada' => ['required', 'date'],
    ]);

    // Validar pertenencia de portátil/vehículo
    if (!empty($validated['portatil_id'])) {
        $portatil = Portatil::find($validated['portatil_id']);
        if ($portatil && $portatil->persona_id != $validated['persona_id']) {
            return back()->withErrors(['portatil_id' => 'El portátil no pertenece a esta persona.']);
        }
    }

    if (!empty($validated['vehiculo_id'])) {
        $vehiculo = Vehiculo::find($validated['vehiculo_id']);
        if ($vehiculo && $vehiculo->persona_id != $validated['persona_id']) {
            return back()->withErrors(['vehiculo_id' => 'El vehículo no pertenece a esta persona.']);
        }
    }

    Acceso::create([
        'persona_id' => $validated['persona_id'],
        'portatil_id' => $validated['portatil_id'] ?? null,
        'vehiculo_id' => $validated['vehiculo_id'] ?? null,
        'fecha_entrada' => $validated['fecha_entrada'],
        'estado' => 'activo',
    ]);

    return back()->with('success', 'Acceso registrado exitosamente.');
}
```

#### `getPortatiles()` - Obtener portátiles de una persona
```php
public function getPortatiles($personaId)
{
    $portatiles = Portatil::where('persona_id', $personaId)
        ->select('portatil_id', 'serial', 'marca', 'modelo')
        ->get();

    return response()->json(['portatiles' => $portatiles]);
}
```

#### `getVehiculos()` - Obtener vehículos de una persona
```php
public function getVehiculos($personaId)
{
    $vehiculos = Vehiculo::where('persona_id', $personaId)
        ->select('vehiculo_id', 'tipo', 'placa')
        ->get();

    return response()->json(['vehiculos' => $vehiculos]);
}
```

---

### **3. Rutas**
**Archivo**: `routes/web.php`

**Rutas agregadas** (dentro del grupo `celador`):
```php
// Accesos
Route::get('/accesos', [CeladorAccesoController::class, 'index'])->name('accesos.index');
Route::post('/accesos', [CeladorAccesoController::class, 'store'])->name('accesos.store');
Route::get('/accesos/portatiles/{persona}', [CeladorAccesoController::class, 'getPortatiles'])->name('accesos.portatiles');
Route::get('/accesos/vehiculos/{persona}', [CeladorAccesoController::class, 'getVehiculos'])->name('accesos.vehiculos');
```

---

## 🎨 Características del Modal

### **Diseño Responsive:**
- ✅ Máximo ancho: `2xl` (adapta a pantalla)
- ✅ Scroll interno si es necesario
- ✅ Touch-friendly en móviles

### **Campos del Formulario:**

#### 1. **Persona** (requerido)
- Select con todas las personas del sistema
- Formato: `Nombre - Documento (Tipo)`
- Ejemplo: `Juan Pérez - 1234567890 (ESTUDIANTE)`

#### 2. **Portátil** (opcional)
- Select dinámico (se carga al seleccionar persona)
- Solo muestra portátiles de la persona seleccionada
- Formato: `Marca Modelo - Serial`
- Ejemplo: `DELL LATITUDE - VJHYV6IV`
- Mensaje si no tiene: "Esta persona no tiene portátiles registrados"

#### 3. **Vehículo** (opcional)
- Select dinámico (se carga al seleccionar persona)
- Solo muestra vehículos de la persona seleccionada
- Formato: `Tipo - Placa`
- Ejemplo: `Automóvil - ABC123`
- Mensaje si no tiene: "Esta persona no tiene vehículos registrados"

#### 4. **Fecha y Hora de Entrada** (requerido)
- Input type `datetime-local`
- Prellenado con fecha/hora actual
- Permite modificar para registros retroactivos

### **Mensajes Informativos:**
```
ℹ️ Registro de acceso
El acceso se creará en estado activo. El portátil y 
vehículo son opcionales, solo se mostrarán los que 
pertenecen a la persona seleccionada.
```

---

## ✅ Validaciones Implementadas

### **Frontend (Vue):**
- ✅ Persona: requerido
- ✅ Portátil: opcional, solo habilitado si hay persona seleccionada
- ✅ Vehículo: opcional, solo habilitado si hay persona seleccionada
- ✅ Fecha entrada: requerido

### **Backend (PHP):**
- ✅ `persona_id`: requerido, debe existir en tabla personas
- ✅ `portatil_id`: opcional, debe existir en tabla portatiles
- ✅ `vehiculo_id`: opcional, debe existir en tabla vehiculos
- ✅ `fecha_entrada`: requerido, debe ser fecha válida
- ✅ **Validación extra**: Portátil debe pertenecer a la persona
- ✅ **Validación extra**: Vehículo debe pertenecer a la persona

---

## 🧪 Cómo Probar

### **Paso 1**: Ir a la página de accesos
```
http://127.0.0.1:8000/system/celador/accesos
```

### **Paso 2**: Click en "Nuevo Acceso"
```
✅ Se abre el modal
✅ Se muestra el formulario
✅ Fecha/hora prellenada
```

### **Paso 3**: Seleccionar persona
```
Ejemplo: Juan Pérez - 1234567890 (ESTUDIANTE)
✅ Se cargan sus portátiles (si tiene)
✅ Se cargan sus vehículos (si tiene)
```

### **Paso 4**: Seleccionar portátil/vehículo (opcional)
```
Portátil: DELL LATITUDE - VJHYV6IV
Vehículo: (dejar en blanco si no tiene)
```

### **Paso 5**: Ajustar fecha/hora (si es necesario)
```
Por defecto viene la actual: 2025-10-14T19:30
```

### **Paso 6**: Click en "Registrar Acceso"
```
✅ Se valida el formulario
✅ Se crea el acceso
✅ Se cierra el modal
✅ La tabla se actualiza automáticamente
✅ Aparece el nuevo registro con estado "Activo"
```

---

## 📊 Ejemplo de Uso

### **Escenario**: Registrar acceso manual de Juan Pérez

**Datos ingresados:**
```
Persona: Juan Pérez - 1234567890 (ESTUDIANTE)
Portátil: DELL LATITUDE - VJHYV6IV
Vehículo: (ninguno)
Fecha entrada: 2025-10-14 19:30
```

**Resultado en la tabla:**
```
┌────────────────┬──────────────┬─────────────┬──────────────────┬─────────┬──────────┬─────────┬──────────┐
│ Persona        │ Documento    │ Tipo        │ Entrada          │ Salida  │ Duración │ Estado  │ Recursos │
├────────────────┼──────────────┼─────────────┼──────────────────┼─────────┼──────────┼─────────┼──────────┤
│ J  Juan Pérez  │ 1234567890   │ ESTUDIANTE  │ 14 oct, 07:30 PM │ —       │ 15m      │ Activo  │ 💻       │
│    juan@...    │              │             │                  │         │          │         │          │
└────────────────┴──────────────┴─────────────┴──────────────────┴─────────┴──────────┴─────────┴──────────┘
```

---

## 🔐 Seguridad

### **Validaciones de Pertenencia:**

Si intentas asignar un portátil que no pertenece a la persona:
```
❌ Error: "El portátil no pertenece a esta persona."
```

Si intentas asignar un vehículo que no pertenece a la persona:
```
❌ Error: "El vehículo no pertenece a esta persona."
```

---

## 🎯 Casos de Uso

### **1. Acceso solo de persona (sin equipos)**
```
Persona: María García
Portátil: (ninguno)
Vehículo: (ninguno)
✅ Acceso registrado correctamente
```

### **2. Acceso con portátil**
```
Persona: Juan Pérez
Portátil: DELL LATITUDE - VJHYV6IV
Vehículo: (ninguno)
✅ Acceso con portátil registrado
```

### **3. Acceso con vehículo**
```
Persona: Pedro López
Portátil: (ninguno)
Vehículo: Automóvil - XYZ789
✅ Acceso con vehículo registrado
```

### **4. Acceso con portátil y vehículo**
```
Persona: Juan Pérez
Portátil: DELL LATITUDE - VJHYV6IV
Vehículo: Automóvil - ABC123
✅ Acceso completo registrado
```

### **5. Registro retroactivo**
```
Persona: María García
Fecha: 2025-10-14 08:00 (hora de la mañana)
✅ Permite registrar accesos pasados
```

---

## 🎨 Estados Visuales

### **Botón "Nuevo Acceso":**
- Color: Verde SENA (#39A900)
- Hover: Verde más oscuro (#2d7f00)
- Icono: ➕ Plus
- Responsive: Texto completo en desktop, solo "Nuevo" en móvil

### **Modal:**
- Fondo semi-transparente
- Animación de entrada/salida suave
- Máximo ancho: 2xl (~672px)
- Padding: 24px

### **Formulario:**
- Inputs con border redondeado
- Focus ring verde SENA
- Placeholders descriptivos
- Disabled states para campos dependientes

---

## ✨ Mejoras Implementadas

1. ✅ **Carga dinámica**: Portátiles/vehículos se cargan según persona
2. ✅ **Validación en tiempo real**: Watch de Vue detecta cambios
3. ✅ **Estados de carga**: Loading indicators mientras carga datos
4. ✅ **Mensajes informativos**: Guía al usuario en cada paso
5. ✅ **Responsive**: Funciona en móvil, tablet y desktop
6. ✅ **Accesibilidad**: Labels descriptivos con iconos
7. ✅ **Feedback visual**: Botón disabled mientras procesa
8. ✅ **Auto-refresh**: Tabla se actualiza sin recargar página

---

## 🐛 Manejo de Errores

### **Si no hay personas en el sistema:**
```javascript
Select muestra: "Seleccionar persona..."
(vacío)
```

### **Si la persona no tiene portátiles:**
```
Mensaje: "Esta persona no tiene portátiles registrados"
Select: "Sin portátil"
```

### **Si la persona no tiene vehículos:**
```
Mensaje: "Esta persona no tiene vehículos registrados"
Select: "Sin vehículo"
```

### **Si hay error en la validación:**
```
✅ Mensaje de error específico debajo del campo
✅ Modal permanece abierto
✅ Datos ingresados se conservan
```

---

## 📝 Resumen de Cambios

### **Frontend:**
- ✅ Botón "Nuevo Acceso" en header
- ✅ Modal con formulario completo
- ✅ Carga dinámica de portátiles/vehículos
- ✅ Validaciones y feedback visual

### **Backend:**
- ✅ Método `store()` para crear acceso
- ✅ Método `getPortatiles()` para API
- ✅ Método `getVehiculos()` para API
- ✅ Validaciones de pertenencia

### **Rutas:**
- ✅ POST `/system/celador/accesos` → store
- ✅ GET `/system/celador/accesos/portatiles/{persona}` → getPortatiles
- ✅ GET `/system/celador/accesos/vehiculos/{persona}` → getVehiculos

---

## 🎉 Resultado Final

**Antes**:
- ❌ Solo se podían ver accesos
- ❌ Había que usar el QR o terminal para registrar

**Ahora**:
- ✅ **Botón "Nuevo Acceso" visible**
- ✅ **Modal profesional y funcional**
- ✅ **Carga dinámica de datos**
- ✅ **Validaciones robustas**
- ✅ **Experiencia consistente con otros módulos**
- ✅ **Admin puede hacer TODO desde la interfaz**

---

**Fecha de implementación**: 14/10/2025  
**Estado**: ✅ **COMPLETAMENTE FUNCIONAL**  
**Versión**: CTAccess v2.0
