<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'

// People management state
const personas = ref({ data: [], links: [], total: 0, from: 0, to: 0 })
const showPersonaModal = ref(false)
const selectedPersona = ref(null)
const loading = ref(false)

const searchForm = reactive({
  search: '',
  per_page: 15,
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
const showPersonaDetails = (persona) => {
  selectedPersona.value = persona
  showPersonaModal.value = true
}

// Close modal
const closeModal = () => {
  showPersonaModal.value = false
  selectedPersona.value = null
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
        <a
          href="/personas/create"
          class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white rounded-lg hover:bg-forest-700 transition-colors"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Persona
        </a>
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

    <!-- Modal para detalles de persona -->
    <div v-if="showPersonaModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div
          class="fixed inset-0 transition-opacity bg-black bg-opacity-50"
          @click="closeModal"
        ></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-theme-card rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-theme-primary">Detalles de Persona</h3>
            <button
              @click="closeModal"
              class="text-theme-muted hover:text-theme-secondary"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="selectedPersona" class="space-y-6">
            <!-- Información personal -->
            <div class="bg-theme-secondary rounded-lg p-4">
              <h4 class="text-md font-semibold mb-4 text-theme-primary">Información Personal</h4>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-theme-secondary mb-1">ID</label>
                  <p class="text-theme-primary">{{ selectedPersona.id }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-theme-secondary mb-1">Documento</label>
                  <p class="text-theme-primary">{{ selectedPersona.documento || '—' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-theme-secondary mb-1">Nombre</label>
                  <p class="text-theme-primary font-medium">{{ selectedPersona.nombre }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-theme-secondary mb-1">Tipo de Persona</label>
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-forest-100 dark:bg-forest-800 text-forest-700 dark:text-forest-300">
                    {{ selectedPersona.tipoPersona }}
                  </span>
                </div>
                <div v-if="selectedPersona.correo" class="md:col-span-2">
                  <label class="block text-sm font-medium text-theme-secondary mb-1">Correo Electrónico</label>
                  <p class="text-theme-primary">{{ selectedPersona.correo }}</p>
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-theme-secondary mb-1">Fechas</label>
                  <div class="text-sm text-theme-secondary">
                    <p>Creado: {{ formatDate(selectedPersona.createdAt) }}</p>
                    <p>Actualizado: {{ formatDate(selectedPersona.updatedAt) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Portátiles -->
            <div v-if="selectedPersona.portatiles && selectedPersona.portatiles.length > 0" class="bg-theme-secondary rounded-lg p-4">
              <h4 class="text-md font-semibold mb-4 text-theme-primary">Portátiles Registrados</h4>
              <div class="space-y-3">
                <div
                  v-for="portatil in selectedPersona.portatiles"
                  :key="portatil.id"
                  class="border border-theme-primary rounded-lg p-3 bg-theme-card"
                >
                  <div class="grid md:grid-cols-3 gap-3">
                    <div>
                      <label class="block text-sm font-medium text-theme-secondary mb-1">Serial</label>
                      <p class="text-theme-primary font-mono text-sm">{{ portatil.serial }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-theme-secondary mb-1">Marca</label>
                      <p class="text-theme-primary">{{ portatil.marca }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-theme-secondary mb-1">Modelo</label>
                      <p class="text-theme-primary">{{ portatil.modelo }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vehículos -->
            <div v-if="selectedPersona.vehiculos && selectedPersona.vehiculos.length > 0" class="bg-theme-secondary rounded-lg p-4">
              <h4 class="text-md font-semibold mb-4 text-theme-primary">Vehículos Registrados</h4>
              <div class="space-y-3">
                <div
                  v-for="vehiculo in selectedPersona.vehiculos"
                  :key="vehiculo.id"
                  class="border border-theme-primary rounded-lg p-3 bg-theme-card"
                >
                  <div class="grid md:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-sm font-medium text-theme-secondary mb-1">Tipo</label>
                      <p class="text-theme-primary">{{ vehiculo.tipo }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-theme-secondary mb-1">Placa</label>
                      <p class="text-theme-primary font-mono">{{ vehiculo.placa }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mensaje si no hay portátiles ni vehículos -->
            <div v-if="(!selectedPersona.portatiles || selectedPersona.portatiles.length === 0) && (!selectedPersona.vehiculos || selectedPersona.vehiculos.length === 0)"
                 class="bg-theme-tertiary border border-theme-primary rounded-lg p-4 text-center">
              <p class="text-theme-secondary">Esta persona no tiene portátiles ni vehículos registrados.</p>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end gap-3 pt-4 border-t border-theme-primary">
              <a
                :href="`/personas/${selectedPersona.id}/edit`"
                class="px-4 py-2 bg-yellow-600 dark:bg-yellow-700 text-white rounded-lg hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors"
              >
                Editar
              </a>
              <button
                @click="closeModal"
                class="px-4 py-2 bg-sage-600 dark:bg-sage-700 text-white rounded-lg hover:bg-sage-700 dark:hover:bg-sage-600 transition-colors"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>