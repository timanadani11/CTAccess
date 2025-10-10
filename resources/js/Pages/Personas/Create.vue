<template>
  <Head title="Nueva Persona - CTAccess" />
  
  <div class="min-h-screen bg-theme-primary text-theme-primary">
    <!-- Toggle de tema fijo -->
    <div class="absolute top-4 right-4 z-10">
      <button @click="toggleTheme" class="p-2 rounded-lg bg-theme-card border border-theme-primary hover:bg-theme-secondary transition-all duration-200 shadow-theme-sm">
        <Icon :name="isDark ? 'sun' : 'moon'" :size="20" class="text-theme-secondary" />
      </button>
    </div>

    <!-- Layout responsive: horizontal en desktop, vertical en móvil -->
    <div class="flex flex-col lg:flex-row min-h-screen">

      <!-- Panel izquierdo: Logo y progreso (desktop) / Header (móvil) -->
      <div class="lg:w-80 xl:w-96 lg:min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 lg:sticky lg:top-0 lg:max-h-screen">
        <!-- Logo -->
        <div class="text-center mb-4 sm:mb-6 lg:mb-8">
          <div class="mx-auto h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24 rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-theme-lg" style="background: linear-gradient(135deg, #39A900, #50E5F9);">
            <Icon name="user-plus" :size="32" class="text-white sm:hidden" />
            <Icon name="user-plus" :size="40" class="text-white hidden sm:block lg:hidden" />
            <Icon name="user-plus" :size="48" class="text-white hidden lg:block" />
          </div>
          <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-theme-primary mb-2">Nueva Persona</h1>
          <p class="text-theme-secondary text-xs sm:text-sm lg:text-base px-2">{{ getStepDescription() }}</p>
        </div>

        <!-- Indicador de progreso -->
        <div class="w-full max-w-xs lg:max-w-sm">
          <!-- Progreso horizontal en móvil y tablet -->
          <div class="lg:hidden">
            <div class="flex items-center justify-center space-x-1 sm:space-x-2 mb-3 sm:mb-4">
              <div v-for="step in totalSteps" :key="step" class="flex items-center">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-medium transition-all duration-200" :class="step <= currentStep ? 'bg-green-500 text-white' : 'bg-theme-secondary text-theme-muted border border-theme-primary'">
                  <Icon v-if="step < currentStep" name="check" :size="14" class="sm:hidden" />
                  <Icon v-if="step < currentStep" name="check" :size="16" class="hidden sm:block" />
                  <span v-else>{{ step }}</span>
                </div>
                <div v-if="step < totalSteps" class="w-4 sm:w-6 h-0.5 mx-0.5 sm:mx-1 transition-all duration-200" :class="step < currentStep ? 'bg-green-500' : 'bg-theme-secondary'"></div>
              </div>
            </div>
            <div class="text-center text-xs text-theme-muted">Paso {{ currentStep }} de {{ totalSteps }}</div>
          </div>

          <!-- Progreso vertical en desktop -->
          <div class="hidden lg:block">
            <div class="space-y-3 xl:space-y-4">
              <div v-for="step in totalSteps" :key="step" class="flex items-center space-x-3 xl:space-x-4">
                <div class="w-9 h-9 xl:w-10 xl:h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-200 flex-shrink-0" :class="step <= currentStep ? 'bg-green-500 text-white' : 'bg-theme-secondary text-theme-muted border border-theme-primary'">
                  <Icon v-if="step < currentStep" name="check" :size="18" />
                  <span v-else>{{ step }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm xl:text-base font-medium text-theme-primary truncate" :class="step === currentStep ? 'text-green-600' : ''">
                    {{ getStepTitle(step) }}
                  </div>
                  <div class="text-xs text-theme-muted truncate">{{ getStepSubtitle(step) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel derecho: Formulario -->
      <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 xl:p-12">
        <div class="w-full max-w-md lg:max-w-2xl xl:max-w-3xl 2xl:max-w-4xl">

      <!-- Formulario paso a paso -->
      <div class="bg-theme-card backdrop-blur-lg rounded-2xl shadow-theme-lg p-5 sm:p-6 lg:p-8 xl:p-10 border border-theme-primary">
        
        <!-- Paso 1: Información Personal -->
        <div v-if="currentStep === 1" class="space-y-4 sm:space-y-5">

          <div class="space-y-4 sm:space-y-5">
            <div>
              <label for="nombre" class="block text-sm font-medium text-theme-primary mb-2">Nombre Completo *</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="user" :size="20" class="text-theme-muted" />
                </div>
                <input id="nombre" v-model="form.nombre" type="text" required class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500" placeholder="Ej: Juan Pérez García" />
              </div>
              <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-500">{{ form.errors.nombre }}</div>
            </div>

            <div>
              <label for="documento" class="block text-sm font-medium text-theme-primary mb-2">Documento de Identidad</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="credit-card" :size="20" class="text-theme-muted" />
                </div>
                <input id="documento" v-model="form.documento" type="text" class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500" placeholder="Ej: 12345678" />
              </div>
            </div>

            <div>
              <label for="tipoPersona" class="block text-sm font-medium text-theme-primary mb-2">Tipo de Persona *</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="users" :size="20" class="text-theme-muted" />
                </div>
                <select id="tipoPersona" v-model="form.tipoPersona" required class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500">
                  <option value="">Seleccionar tipo</option>
                  <option value="Estudiante">👨‍🎓 Estudiante</option>
                  <option value="Docente">👨‍🏫 Docente</option>
                  <option value="Administrativo">👨‍💼 Administrativo</option>
                  <option value="Visitante">👤 Visitante</option>
                  <option value="Contratista">🔧 Contratista</option>
                </select>
              </div>
            </div>

            <div>
              <label for="correo" class="block text-sm font-medium text-theme-primary mb-2">Correo Electrónico</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="mail" :size="20" class="text-theme-muted" />
                </div>
                <input id="correo" v-model="form.correo" type="email" class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500" placeholder="correo@ejemplo.com" />
              </div>
              <p class="mt-2 text-xs text-theme-muted">📧 Se enviará un QR por correo si se proporciona</p>
            </div>
          </div>
        </div>

        <!-- Paso 2: Portátiles -->
        <div v-if="currentStep === 2" class="space-y-4">

          <div v-if="form.portatiles.length === 0" class="text-center py-8">
            <Icon name="laptop" :size="64" class="mx-auto text-theme-muted mb-4" />
            <p class="text-theme-muted mb-4">No hay portátiles agregados</p>
            <button type="button" @click="addPortatil" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-medium transition-all duration-200 shadow-theme-md hover:shadow-theme-lg" style="background: linear-gradient(135deg, #50E5F9, #00B4D8);">
              <Icon name="plus" :size="16" class="mr-2" />Agregar Primer Portátil
            </button>
          </div>

          <div v-else class="space-y-4">
            <div v-for="(portatil, index) in form.portatiles" :key="`portatil-${index}`" class="border border-theme-primary rounded-lg p-4 bg-theme-secondary">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium text-theme-primary flex items-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 text-white text-xs font-bold rounded-full mr-2" style="background: #50E5F9;">{{ index + 1 }}</span>
                  Portátil {{ index + 1 }}
                </h4>
                <button type="button" @click="removePortatil(index)" class="text-red-500 hover:text-red-700 transition-colors">
                  <Icon name="trash" :size="16" />
                </button>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-theme-primary mb-1">Serial *</label>
                  <input v-model="portatil.serial" type="text" required class="w-full px-3 py-2 border border-theme-primary rounded-lg bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-blue-500" placeholder="Ej: ABC123456" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-theme-primary mb-1">Marca *</label>
                    <input v-model="portatil.marca" type="text" required class="w-full px-3 py-2 border border-theme-primary rounded-lg bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-blue-500" placeholder="Dell, HP..." />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-theme-primary mb-1">Modelo *</label>
                    <input v-model="portatil.modelo" type="text" required class="w-full px-3 py-2 border border-theme-primary rounded-lg bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-blue-500" placeholder="Inspiron..." />
                  </div>
                </div>
              </div>
            </div>
            <button type="button" @click="addPortatil" class="w-full py-2 border-2 border-dashed border-theme-secondary rounded-lg text-theme-secondary hover:border-blue-400 hover:text-blue-400 transition-colors">
              <Icon name="plus" :size="16" class="mr-2" />Agregar Otro Portátil
            </button>
          </div>
        </div>

        <!-- Paso 3: Vehículos -->
        <div v-if="currentStep === 3" class="space-y-4">

          <div v-if="form.vehiculos.length === 0" class="text-center py-8">
            <Icon name="car" :size="64" class="mx-auto text-theme-muted mb-4" />
            <p class="text-theme-muted mb-4">No hay vehículos agregados</p>
            <button type="button" @click="addVehiculo" class="inline-flex items-center px-4 py-2 rounded-lg text-black font-medium transition-all duration-200 shadow-theme-md hover:shadow-theme-lg" style="background: linear-gradient(135deg, #FDC300, #E6B000);">
              <Icon name="plus" :size="16" class="mr-2" />Agregar Primer Vehículo
            </button>
          </div>

          <div v-else class="space-y-4">
            <div v-for="(vehiculo, index) in form.vehiculos" :key="`vehiculo-${index}`" class="border border-theme-primary rounded-lg p-4 bg-theme-secondary">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium text-theme-primary flex items-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 text-black text-xs font-bold rounded-full mr-2" style="background: #FDC300;">{{ index + 1 }}</span>
                  Vehículo {{ index + 1 }}
                </h4>
                <button type="button" @click="removeVehiculo(index)" class="text-red-500 hover:text-red-700 transition-colors">
                  <Icon name="trash" :size="16" />
                </button>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-theme-primary mb-1">Tipo de Vehículo *</label>
                  <select v-model="vehiculo.tipo" required class="w-full px-3 py-2 border border-theme-primary rounded-lg bg-theme-primary text-theme-primary focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-yellow-500">
                    <option value="">Seleccionar tipo</option>
                    <option value="Automóvil">🚗 Automóvil</option>
                    <option value="Motocicleta">🏍️ Motocicleta</option>
                    <option value="Bicicleta">🚲 Bicicleta</option>
                    <option value="Camioneta">🚙 Camioneta</option>
                    <option value="Camión">🚛 Camión</option>
                    <option value="Otro">🚐 Otro</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-theme-primary mb-1">Placa *</label>
                  <input v-model="vehiculo.placa" type="text" required class="w-full px-3 py-2 border border-theme-primary rounded-lg bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 uppercase focus:ring-yellow-500" placeholder="ABC-123" @input="vehiculo.placa = vehiculo.placa.toUpperCase()" />
                </div>
              </div>
            </div>
            <button type="button" @click="addVehiculo" class="w-full py-2 border-2 border-dashed border-theme-secondary rounded-lg text-theme-secondary hover:border-yellow-400 hover:text-yellow-400 transition-colors">
              <Icon name="plus" :size="16" class="mr-2" />Agregar Otro Vehículo
            </button>
          </div>
        </div>

        <!-- Paso 4: Resumen -->
        <div v-if="currentStep === 4" class="space-y-4 sm:space-y-5">

          <div class="space-y-4 sm:space-y-5 lg:grid lg:grid-cols-1 xl:grid-cols-2 lg:gap-5">
            <div class="border border-theme-primary rounded-lg p-4 bg-theme-secondary">
              <h4 class="font-medium text-theme-primary mb-3 flex items-center">
                <Icon name="user" :size="16" class="mr-2" style="color: #39A900;" />Información Personal
              </h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-theme-muted">Nombre:</span><span class="text-theme-primary font-medium">{{ form.nombre || 'No especificado' }}</span></div>
                <div class="flex justify-between"><span class="text-theme-muted">Documento:</span><span class="text-theme-primary">{{ form.documento || 'No especificado' }}</span></div>
                <div class="flex justify-between"><span class="text-theme-muted">Tipo:</span><span class="text-theme-primary">{{ form.tipoPersona || 'No especificado' }}</span></div>
                <div class="flex justify-between"><span class="text-theme-muted">Correo:</span><span class="text-theme-primary">{{ form.correo || 'No especificado' }}</span></div>
              </div>
            </div>

            <div v-if="form.portatiles.length > 0" class="border border-theme-primary rounded-lg p-4 bg-theme-secondary">
              <h4 class="font-medium text-theme-primary mb-3 flex items-center">
                <Icon name="laptop" :size="16" class="mr-2" style="color: #50E5F9;" />Portátiles ({{ form.portatiles.length }})
              </h4>
              <div class="space-y-2">
                <div v-for="(portatil, index) in form.portatiles" :key="index" class="text-sm">
                  <div class="flex justify-between"><span class="text-theme-muted">{{ portatil.marca }} {{ portatil.modelo }}:</span><span class="text-theme-primary">{{ portatil.serial }}</span></div>
                </div>
              </div>
            </div>

            <div v-if="form.vehiculos.length > 0" class="border border-theme-primary rounded-lg p-4 bg-theme-secondary">
              <h4 class="font-medium text-theme-primary mb-3 flex items-center">
                <Icon name="car" :size="16" class="mr-2" style="color: #FDC300;" />Vehículos ({{ form.vehiculos.length }})
              </h4>
              <div class="space-y-2">
                <div v-for="(vehiculo, index) in form.vehiculos" :key="index" class="text-sm">
                  <div class="flex justify-between"><span class="text-theme-muted">{{ vehiculo.tipo }}:</span><span class="text-theme-primary">{{ vehiculo.placa }}</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de navegación -->
        <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-0 pt-6">
          <button v-if="currentStep > 1" type="button" @click="previousStep" class="inline-flex items-center justify-center px-4 py-2.5 border border-theme-primary rounded-lg text-theme-secondary hover:bg-theme-secondary transition-all duration-200 order-2 sm:order-1">
            <Icon name="arrow-left" :size="16" class="mr-2" />Anterior
          </button>
          <div v-else class="hidden sm:block"></div>

          <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 order-1 sm:order-2">
            <Link :href="route('personas.index')" class="inline-flex items-center justify-center px-4 py-2.5 border border-theme-primary rounded-lg text-theme-secondary hover:bg-theme-secondary transition-all duration-200">
              <Icon name="x" :size="16" class="mr-2" />Cancelar
            </Link>

            <button v-if="currentStep < totalSteps" type="button" @click="nextStep" :disabled="!canProceedToNextStep()" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg text-white font-medium transition-all duration-200 shadow-theme-md hover:shadow-theme-lg disabled:opacity-50 disabled:cursor-not-allowed" style="background: linear-gradient(135deg, #39A900, #2d7a00);">
              Siguiente<Icon name="arrow-right" :size="16" class="ml-2" />
            </button>

            <button v-else type="submit" @click="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg text-white font-medium transition-all duration-200 shadow-theme-md hover:shadow-theme-lg disabled:opacity-50 disabled:cursor-not-allowed" style="background: linear-gradient(135deg, #39A900, #2d7a00);">
              <Icon v-if="form.processing" name="loader" :size="16" class="animate-spin mr-2" />
              <span v-if="form.processing">Creando...</span><span v-else>Crear Persona</span>
            </button>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
import { useTheme } from '@/composables/useTheme'
import { ref } from 'vue'

defineOptions({ layout: null })

const { isDark, toggleTheme } = useTheme()
const currentStep = ref(1)
const totalSteps = 4

const form = useForm({
  documento: '', nombre: '', tipoPersona: '', correo: '', portatiles: [], vehiculos: []
})

const getStepDescription = () => {
  const descriptions = {
    1: 'Completa la información personal básica',
    2: 'Agrega portátiles asociados (opcional)',
    3: 'Registra vehículos (opcional)',
    4: 'Revisa y confirma la información'
  }
  return descriptions[currentStep.value]
}

const getStepTitle = (step) => {
  const titles = {
    1: 'Información Personal',
    2: 'Portátiles',
    3: 'Vehículos', 
    4: 'Resumen'
  }
  return titles[step]
}

const getStepSubtitle = (step) => {
  const subtitles = {
    1: 'Datos básicos',
    2: 'Equipos (opcional)',
    3: 'Transporte (opcional)',
    4: 'Confirmar datos'
  }
  return subtitles[step]
}

const nextStep = () => {
  if (currentStep.value < totalSteps && canProceedToNextStep()) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const canProceedToNextStep = () => {
  switch (currentStep.value) {
    case 1: return form.nombre.trim() && form.tipoPersona
    case 2:
    case 3: return true
    default: return true
  }
}

const addPortatil = () => {
  form.portatiles.push({ serial: '', marca: '', modelo: '' })
}

const removePortatil = (index) => {
  form.portatiles.splice(index, 1)
}

const addVehiculo = () => {
  form.vehiculos.push({ tipo: '', placa: '' })
}

const removeVehiculo = (index) => {
  form.vehiculos.splice(index, 1)
}

const submit = () => {
  if (!form.nombre.trim()) {
    alert('El nombre es obligatorio');
    return;
  }
  
  if (!form.tipoPersona) {
    alert('Debe seleccionar un tipo de persona');
    return;
  }

  form.post(route('personas.store'), {
    onSuccess: (response) => {
      console.log('Persona creada exitosamente:', response);
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors);
      if (errors.message && (errors.message.includes('CSRF') || errors.message.includes('expired'))) {
        window.location.reload();
        return;
      }
      const firstError = Object.values(errors)[0];
      if (firstError && typeof firstError === 'string') {
        alert(`Error: ${firstError}`);
      }
    },
    preserveScroll: true,
    preserveState: false,
  })
}
</script>
