<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'
import PersonaDetalleModal from '@/Components/PersonaDetalleModal.vue'
import Icon from '@/Components/Icon.vue'

// People management state
const personas = ref({ data: [], links: [], total: 0, from: 0, to: 0 })
const showPersonaModal = ref(false)
const showCreateModal = ref(false)
const selectedPersona = ref(null)
const loading = ref(false)
const currentStep = ref(1)
const totalSteps = 4

const searchForm = reactive({
  search: '',
  per_page: 15,
})

// Form for creating new persona
const form = useForm({
  documento: '',
  nombre: '',
  tipoPersona: '',
  correo: '',
  portatiles: [],
  vehiculos: []
})

// Debounce function
const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Load personas data
const loadPersonas = async () => {
  loading.value = true
  try {
    const response = await axios.get('/system/admin/personas/data', {
      params: searchForm
    })
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas:', error)
  } finally {
    loading.value = false
  }
}

// Search with debounce
const search = debounce(() => {
  loadPersonas()
}, 300)

// Show persona details in modal
const showPersonaDetails = async (persona) => {
  loading.value = true
  try {
    // Load full persona data with relationships
    const response = await axios.get(`/personas/${persona.id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    selectedPersona.value = response.data.persona || persona
    showPersonaModal.value = true
  } catch (error) {
    console.error('Error loading persona:', error)
    selectedPersona.value = persona
    showPersonaModal.value = true
  } finally {
    loading.value = false
  }
}

// Close modal
const closeModal = () => {
  showPersonaModal.value = false
  selectedPersona.value = null
}

// Open create modal
const openCreateModal = () => {
  showCreateModal.value = true
  currentStep.value = 1
  form.reset()
}

// Close create modal
const closeCreateModal = () => {
  showCreateModal.value = false
  currentStep.value = 1
  form.reset()
}

// Step navigation
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

// Portátiles management
const addPortatil = () => {
  form.portatiles.push({ serial: '', marca: '', modelo: '' })
}

const removePortatil = (index) => {
  form.portatiles.splice(index, 1)
}

// Vehículos management
const addVehiculo = () => {
  form.vehiculos.push({ tipo: '', placa: '' })
}

const removeVehiculo = (index) => {
  form.vehiculos.splice(index, 1)
}

// Submit form
const submitCreate = () => {
  if (!form.nombre.trim()) {
    alert('El nombre es obligatorio')
    return
  }

  if (!form.tipoPersona) {
    alert('Debe seleccionar un tipo de persona')
    return
  }

  form.post(route('personas.store'), {
    onSuccess: () => {
      closeCreateModal()
      loadPersonas() // Reload the list
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      const firstError = Object.values(errors)[0]
      if (firstError && typeof firstError === 'string') {
        alert(`Error: ${firstError}`)
      }
    },
    preserveScroll: true,
  })
}

// Helper functions for create modal
const getStepTitle = (step) => {
  const titles = {
    1: 'Información Personal',
    2: 'Portátiles',
    3: 'Vehículos',
    4: 'Resumen'
  }
  return titles[step]
}

// Delete persona
const confirmDelete = async (persona) => {
  if (confirm(`¿Estás seguro de eliminar a ${persona.nombre}?`)) {
    try {
      await router.delete(`/personas/${persona.id}`)
      loadPersonas() // Reload the list
    } catch (error) {
      alert('Error al eliminar: ' + error.message)
    }
  }
}

// Load personas from pagination link
const loadPersonasPage = async (url) => {
  if (!url) return

  loading.value = true
  try {
    const response = await axios.get(url)
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas page:', error)
  } finally {
    loading.value = false
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Load data on component mount
onMounted(() => {
  loadPersonas()
})
</script>

<template>
  <SystemLayout>
    <Head title="Gestión de Personas" />

    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-forest-600">
            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-theme-primary">Gestión de Personas</h2>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white rounded-lg hover:bg-forest-700 transition-colors"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Persona
        </button>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filtros -->
      <div class="bg-theme-card rounded-xl border border-theme-primary p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-theme-primary mb-4">Filtros de búsqueda</h3>
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchForm.search"
              @input="search"
              type="text"
              placeholder="Buscar por nombre, documento o tipo..."
              class="w-full px-4 py-2 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-forest-500 focus:border-transparent"
            >
          </div>
          <select
            v-model="searchForm.per_page"
            @change="search"
            class="px-3 py-2 border border-theme-primary rounded-lg bg-theme-card text-theme-primary focus:ring-2 focus:ring-forest-500"
          >
            <option value="10">10 por página</option>
            <option value="15">15 por página</option>
            <option value="25">25 por página</option>
            <option value="50">50 por página</option>
          </select>
        </div>
      </div>

      <!-- Tabla de personas -->
      <div class="bg-theme-card rounded-xl border border-theme-primary shadow-theme-sm overflow-hidden">
        <div class="border-b border-theme-primary bg-theme-secondary p-6">
          <h3 class="text-lg font-semibold text-theme-primary">
            Lista de personas registradas
            <span v-if="personas.total" class="text-sm font-normal text-theme-secondary ml-2">
              ({{ personas.total }} {{ personas.total === 1 ? 'persona' : 'personas' }})
            </span>
          </h3>
        </div>

        <!-- Loading indicator -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-flex items-center gap-2 text-theme-secondary">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cargando personas...
          </div>
        </div>

        <!-- Tabla -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-theme-primary text-sm">
            <thead class="bg-theme-secondary">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">ID</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Documento</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Nombre</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Tipo</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Portátiles</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Vehículos</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-theme-primary bg-theme-card">
              <tr v-for="persona in personas.data" :key="persona.id" class="transition-colors hover:bg-theme-secondary">
                <td class="px-6 py-4 text-theme-primary">{{ persona.id }}</td>
                <td class="px-6 py-4 text-theme-secondary">{{ persona.documento || '—' }}</td>
                <td class="px-6 py-4 font-medium text-theme-primary">{{ persona.nombre }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-forest-100 dark:bg-forest-800 text-forest-700 dark:text-forest-300">
                    {{ persona.tipoPersona }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300">
                    {{ persona.portatiles?.length || 0 }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-700 dark:text-purple-300">
                    {{ persona.vehiculos?.length || 0 }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <button
                      @click="showPersonaDetails(persona)"
                      class="px-3 py-1 text-xs bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
                      title="Ver detalles"
                    >
                      Ver
                    </button>
                    <a
                      :href="`/personas/${persona.id}/edit`"
                      class="px-3 py-1 text-xs bg-yellow-600 dark:bg-yellow-700 text-white rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors"
                      title="Editar persona"
                    >
                      Editar
                    </a>
                    <button
                      @click="confirmDelete(persona)"
                      class="px-3 py-1 text-xs bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-600 transition-colors"
                      title="Eliminar persona"
                    >
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!personas.data?.length">
                <td colspan="7" class="px-6 py-12 text-center text-theme-muted">
                  <div class="flex flex-col items-center gap-2">
                    <svg class="h-8 w-8 text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>No se encontraron personas</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="personas.links && personas.data?.length > 0" class="border-t border-theme-primary bg-theme-secondary px-6 py-4">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-theme-secondary">
              Mostrando {{ personas.from }} a {{ personas.to }} de {{ personas.total }} registros
            </div>
            <div class="flex gap-1">
              <button
                v-for="(link, index) in personas.links"
                :key="index"
                @click="loadPersonasPage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-2 text-sm border rounded transition-colors',
                  link.active
                    ? 'bg-forest-600 text-white border-forest-600'
                    : link.url
                      ? 'bg-theme-card text-theme-secondary border-theme-primary hover:bg-theme-secondary'
                      : 'bg-theme-tertiary text-theme-muted border-theme-primary cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalles de Persona -->
    <PersonaDetalleModal
      :persona="selectedPersona"
      :show="showPersonaModal"
      @close="closeModal"
    />

    <!-- Modal de Crear Persona -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="showCreateModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
          @click.self="closeCreateModal"
        >
          <!-- Modal Card -->
          <div class="bg-theme-card rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col my-8">
            <!-- Header -->
            <div class="bg-forest-600 px-6 py-4 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-white/20 flex items-center justify-center">
                  <Icon name="user-plus" :size="20" class="text-white" />
                </div>
                <h2 class="text-xl font-bold text-white">Nueva Persona</h2>
              </div>
              <button
                @click="closeCreateModal"
                class="p-2 hover:bg-white/10 rounded-lg transition-colors"
              >
                <Icon name="x" :size="20" class="text-white" />
              </button>
            </div>

            <!-- Progress Indicator -->
            <div class="bg-theme-secondary px-6 py-4 border-b border-theme-primary">
              <div class="flex items-center justify-between">
                <div v-for="step in totalSteps" :key="step" class="flex items-center flex-1">
                  <div class="flex flex-col items-center flex-1">
                    <div
                      class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all"
                      :class="step <= currentStep ? 'bg-forest-600 text-white' : 'bg-theme-tertiary text-theme-muted border-2 border-theme-primary'"
                    >
                      <Icon v-if="step < currentStep" name="check" :size="18" />
                      <span v-else>{{ step }}</span>
                    </div>
                    <div class="mt-2 text-xs font-medium text-theme-primary text-center hidden sm:block">
                      {{ getStepTitle(step) }}
                    </div>
                  </div>
                  <div
                    v-if="step < totalSteps"
                    class="flex-1 h-1 mx-2 rounded-full transition-all -mt-6"
                    :class="step < currentStep ? 'bg-forest-600' : 'bg-theme-tertiary'"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
              <!-- Paso 1: Información Personal -->
              <div v-if="currentStep === 1" class="space-y-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                  <div class="lg:col-span-2">
                    <label for="nombre" class="block text-sm font-medium text-theme-primary mb-2">Nombre Completo *</label>
                    <input
                      id="nombre"
                      v-model="form.nombre"
                      type="text"
                      required
                      class="block w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent"
                      placeholder="Ej: Juan Pérez García"
                    />
                    <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-500">{{ form.errors.nombre }}</div>
                  </div>

                  <div>
                    <label for="documento" class="block text-sm font-medium text-theme-primary mb-2">Documento de Identidad</label>
                    <input
                      id="documento"
                      v-model="form.documento"
                      type="text"
                      class="block w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent"
                      placeholder="Ej: 12345678"
                    />
                  </div>

                  <div>
                    <label for="tipoPersona" class="block text-sm font-medium text-theme-primary mb-2">Tipo de Persona *</label>
                    <select
                      id="tipoPersona"
                      v-model="form.tipoPersona"
                      required
                      class="block w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent"
                    >
                      <option value="">Seleccionar tipo</option>
                      <option value="Aprendiz">Aprendiz</option>
                      <option value="Instructor">Instructor</option>
                      <option value="Visitante">Visitante</option>
                    </select>
                  </div>

                  <div class="lg:col-span-2">
                    <label for="correo" class="block text-sm font-medium text-theme-primary mb-2">Correo Electrónico</label>
                    <input
                      id="correo"
                      v-model="form.correo"
                      type="email"
                      class="block w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent"
                      placeholder="correo@ejemplo.com"
                    />
                    <p class="mt-2 text-xs text-theme-muted flex items-center">
                      <Icon name="info" :size="14" class="mr-1" />Se enviará un QR por correo si se proporciona
                    </p>
                  </div>
                </div>
              </div>

              <!-- Paso 2: Portátiles -->
              <div v-if="currentStep === 2" class="space-y-5">
                <div v-if="form.portatiles.length === 0" class="text-center py-12">
                  <Icon name="laptop" :size="64" class="mx-auto text-theme-muted mb-4" />
                  <h3 class="text-lg font-semibold text-theme-primary mb-2">Sin portátiles registrados</h3>
                  <p class="text-theme-muted mb-6 max-w-md mx-auto">
                    Agrega los portátiles que esta persona utilizará. Puedes omitir este paso si no aplica.
                  </p>
                  <button
                    type="button"
                    @click="addPortatil"
                    class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors"
                  >
                    <Icon name="plus" :size="18" class="mr-2" />Agregar Primer Portátil
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div
                    v-for="(portatil, index) in form.portatiles"
                    :key="`portatil-${index}`"
                    class="border-2 border-theme-primary rounded-lg p-5 bg-theme-secondary"
                  >
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-base font-semibold text-theme-primary flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-white text-sm font-bold rounded-full mr-3 bg-blue-600">
                          {{ index + 1 }}
                        </span>
                        Portátil {{ index + 1 }}
                      </h4>
                      <button
                        type="button"
                        @click="removePortatil(index)"
                        class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                      >
                        <Icon name="trash" :size="18" />
                      </button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                      <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-theme-primary mb-2">Serial *</label>
                        <input
                          v-model="portatil.serial"
                          type="text"
                          required
                          class="w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Ej: ABC123456DEF"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-theme-primary mb-2">Marca *</label>
                        <input
                          v-model="portatil.marca"
                          type="text"
                          required
                          class="w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Dell, HP, Lenovo..."
                        />
                      </div>
                      <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-theme-primary mb-2">Modelo *</label>
                        <input
                          v-model="portatil.modelo"
                          type="text"
                          required
                          class="w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Inspiron 15, ThinkPad X1..."
                        />
                      </div>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addPortatil"
                    class="w-full py-4 border-2 border-dashed border-theme-secondary rounded-lg text-theme-secondary hover:border-blue-400 hover:text-blue-400 hover:bg-blue-50/5 transition-colors font-medium"
                  >
                    <Icon name="plus" :size="18" class="mr-2" />Agregar Otro Portátil
                  </button>
                </div>
              </div>

              <!-- Paso 3: Vehículos -->
              <div v-if="currentStep === 3" class="space-y-5">
                <div v-if="form.vehiculos.length === 0" class="text-center py-12">
                  <Icon name="car" :size="64" class="mx-auto text-theme-muted mb-4" />
                  <h3 class="text-lg font-semibold text-theme-primary mb-2">Sin vehículos registrados</h3>
                  <p class="text-theme-muted mb-6 max-w-md mx-auto">
                    Registra los vehículos que esta persona utiliza para acceder al centro. Puedes omitir este paso si no aplica.
                  </p>
                  <button
                    type="button"
                    @click="addVehiculo"
                    class="inline-flex items-center px-6 py-3 rounded-lg bg-yellow-600 text-white font-medium hover:bg-yellow-700 transition-colors"
                  >
                    <Icon name="plus" :size="18" class="mr-2" />Agregar Primer Vehículo
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div
                    v-for="(vehiculo, index) in form.vehiculos"
                    :key="`vehiculo-${index}`"
                    class="border-2 border-theme-primary rounded-lg p-5 bg-theme-secondary"
                  >
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-base font-semibold text-theme-primary flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-white text-sm font-bold rounded-full mr-3 bg-yellow-600">
                          {{ index + 1 }}
                        </span>
                        Vehículo {{ index + 1 }}
                      </h4>
                      <button
                        type="button"
                        @click="removeVehiculo(index)"
                        class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                      >
                        <Icon name="trash" :size="18" />
                      </button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                      <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-theme-primary mb-2">Tipo de Vehículo *</label>
                        <select
                          v-model="vehiculo.tipo"
                          required
                          class="w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-card text-theme-primary focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                          <option value="">Seleccionar tipo</option>
                          <option value="Automóvil">🚗 Automóvil</option>
                          <option value="Motocicleta">🏍️ Motocicleta</option>
                          <option value="Bicicleta">🚲 Bicicleta</option>
                          <option value="Camioneta">🚙 Camioneta</option>
                          <option value="Camión">🚛 Camión</option>
                          <option value="Otro">🚐 Otro</option>
                        </select>
                      </div>
                      <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-theme-primary mb-2">Placa *</label>
                        <input
                          v-model="vehiculo.placa"
                          type="text"
                          required
                          class="w-full px-4 py-3 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent uppercase font-mono"
                          placeholder="ABC-123"
                          @input="vehiculo.placa = vehiculo.placa.toUpperCase()"
                        />
                      </div>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addVehiculo"
                    class="w-full py-4 border-2 border-dashed border-theme-secondary rounded-lg text-theme-secondary hover:border-yellow-400 hover:text-yellow-400 hover:bg-yellow-50/5 transition-colors font-medium"
                  >
                    <Icon name="plus" :size="18" class="mr-2" />Agregar Otro Vehículo
                  </button>
                </div>
              </div>

              <!-- Paso 4: Resumen -->
              <div v-if="currentStep === 4" class="space-y-5">
                <div class="text-center mb-6">
                  <h3 class="text-xl font-bold text-theme-primary mb-2">Revisa tu información</h3>
                  <p class="text-theme-muted">Verifica que todos los datos sean correctos antes de crear el registro</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                  <!-- Información Personal -->
                  <div class="border-2 border-theme-primary rounded-lg p-5 bg-theme-secondary lg:col-span-2">
                    <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                      <Icon name="user" :size="18" class="mr-2 text-forest-600" />
                      Información Personal
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Nombre Completo</div>
                        <div class="text-base font-semibold text-theme-primary">{{ form.nombre || 'No especificado' }}</div>
                      </div>
                      <div class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Documento</div>
                        <div class="text-base font-semibold text-theme-primary">{{ form.documento || 'No especificado' }}</div>
                      </div>
                      <div class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Tipo de Persona</div>
                        <div class="text-base font-semibold text-theme-primary">{{ form.tipoPersona || 'No especificado' }}</div>
                      </div>
                      <div class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Correo Electrónico</div>
                        <div class="text-base font-semibold text-theme-primary truncate">{{ form.correo || 'No especificado' }}</div>
                      </div>
                    </div>
                  </div>

                  <!-- Portátiles -->
                  <div v-if="form.portatiles.length > 0" class="border-2 border-theme-primary rounded-lg p-5 bg-theme-secondary" :class="form.vehiculos.length === 0 ? 'lg:col-span-2' : ''">
                    <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                      <Icon name="laptop" :size="18" class="mr-2 text-blue-600" />
                      Portátiles
                      <span class="ml-2 text-sm font-normal text-theme-muted">({{ form.portatiles.length }})</span>
                    </h4>
                    <div class="space-y-3">
                      <div v-for="(portatil, index) in form.portatiles" :key="index" class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Portátil {{ index + 1 }}</div>
                        <div class="text-sm font-semibold text-theme-primary mb-1">{{ portatil.marca }} {{ portatil.modelo }}</div>
                        <div class="text-xs text-theme-muted">Serial: <span class="font-mono text-theme-primary">{{ portatil.serial }}</span></div>
                      </div>
                    </div>
                  </div>

                  <!-- Vehículos -->
                  <div v-if="form.vehiculos.length > 0" class="border-2 border-theme-primary rounded-lg p-5 bg-theme-secondary" :class="form.portatiles.length === 0 ? 'lg:col-span-2' : ''">
                    <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                      <Icon name="car" :size="18" class="mr-2 text-yellow-600" />
                      Vehículos
                      <span class="ml-2 text-sm font-normal text-theme-muted">({{ form.vehiculos.length }})</span>
                    </h4>
                    <div class="space-y-3">
                      <div v-for="(vehiculo, index) in form.vehiculos" :key="index" class="bg-theme-card rounded-lg p-4">
                        <div class="text-xs text-theme-muted mb-1">Vehículo {{ index + 1 }}</div>
                        <div class="text-sm font-semibold text-theme-primary mb-1">{{ vehiculo.tipo }}</div>
                        <div class="text-xs text-theme-muted">Placa: <span class="font-mono text-theme-primary font-bold">{{ vehiculo.placa }}</span></div>
                      </div>
                    </div>
                  </div>

                  <!-- Sin portátiles ni vehículos -->
                  <div v-if="form.portatiles.length === 0 && form.vehiculos.length === 0" class="lg:col-span-2 text-center py-6 border-2 border-dashed border-theme-secondary rounded-lg">
                    <Icon name="info" :size="32" class="mx-auto text-theme-muted mb-2" />
                    <p class="text-theme-muted text-sm">No se registraron portátiles ni vehículos para esta persona</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-theme-primary px-6 py-4 bg-theme-secondary">
              <div class="flex justify-between gap-3">
                <button
                  v-if="currentStep > 1"
                  type="button"
                  @click="previousStep"
                  class="inline-flex items-center px-5 py-2.5 border-2 border-theme-primary rounded-lg text-theme-secondary hover:bg-theme-tertiary transition-colors font-medium"
                >
                  <Icon name="arrow-left" :size="18" class="mr-2" />Anterior
                </button>
                <div v-else></div>

                <div class="flex gap-3">
                  <button
                    @click="closeCreateModal"
                    class="inline-flex items-center px-5 py-2.5 border-2 border-red-400 rounded-lg text-red-600 hover:bg-red-50/10 transition-colors font-medium"
                  >
                    <Icon name="x" :size="18" class="mr-2" />Cancelar
                  </button>

                  <button
                    v-if="currentStep < totalSteps"
                    type="button"
                    @click="nextStep"
                    :disabled="!canProceedToNextStep()"
                    class="inline-flex items-center px-6 py-2.5 rounded-lg bg-forest-600 text-white font-semibold hover:bg-forest-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Siguiente
                    <Icon name="arrow-right" :size="18" class="ml-2" />
                  </button>

                  <button
                    v-else
                    type="button"
                    @click="submitCreate"
                    :disabled="form.processing"
                    class="inline-flex items-center px-6 py-2.5 rounded-lg bg-forest-600 text-white font-semibold hover:bg-forest-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <Icon v-if="form.processing" name="loader" :size="18" class="animate-spin mr-2" />
                    <Icon v-else name="check" :size="18" class="mr-2" />
                    <span v-if="form.processing">Creando...</span>
                    <span v-else>Crear Persona</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </SystemLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
}
</style>