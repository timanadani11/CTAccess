<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { debounce } from 'lodash'
import Icon from '@/Components/Icon.vue'
import PersonaDetalleModal from '@/Components/PersonaDetalleModal.vue'
import axios from 'axios'

const props = defineProps({
  personas: Object,
  tiposPersona: Array,
  filters: Object
})

const page = usePage()

// Estados reactivos para filtros
const search = ref(props.filters.search || '')
const tipoPersona = ref(props.filters.tipo_persona || '')
const perPage = ref(props.filters.per_page || 15)

// Función debounced para búsqueda
const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

// Aplicar filtros
const applyFilters = () => {
  router.get(route('system.celador.personas.index'), {
    search: search.value,
    tipo_persona: tipoPersona.value,
    per_page: perPage.value,
  }, {
    preserveState: true,
    replace: true,
  })
}

// Watchers para filtros
watch(search, debouncedSearch)
watch(tipoPersona, applyFilters)
watch(perPage, applyFilters)

// Limpiar filtros
const clearFilters = () => {
  search.value = ''
  tipoPersona.value = ''
  perPage.value = 15
  applyFilters()
}

// Función para obtener color del badge según tipo de persona
const getTipoBadgeColor = (tipo) => {
  const colors = {
    'Empleado': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'Visitante': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'Contratista': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'Proveedor': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
  }
  return colors[tipo] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

// Modal de detalles
const showModal = ref(false)
const selectedPersona = ref(null)
const loadingPersona = ref(false)

// Función para ver detalles de persona
const viewPersona = async (persona) => {
  loadingPersona.value = true
  try {
    // Cargar datos completos de la persona con relaciones
    const response = await axios.get(route('system.celador.personas.show', persona.idPersona || persona.id), {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    
    selectedPersona.value = response.data.persona || persona
    showModal.value = true
  } catch (error) {
    console.error('Error al cargar persona:', error)
    // Si falla, usar los datos que ya tenemos
    selectedPersona.value = persona
    showModal.value = true
  } finally {
    loadingPersona.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  selectedPersona.value = null
}
</script>

<template>
  <SystemLayout>
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-theme-primary">
            Gestión de Personas
          </h1>
          <p class="mt-1 text-sm text-theme-secondary">
            Consulta y gestiona la información de las personas registradas
          </p>
        </div>
        <div class="flex items-center gap-2 text-sm text-theme-muted">
          <Icon name="users" :size="16" />
          Total: {{ personas.total }} personas
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filtros -->
      <div class="bg-theme-card rounded-lg shadow-theme-sm border border-theme-primary p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-theme-primary mb-1">
              Buscar persona
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon name="search" :size="16" class="text-theme-muted" />
              </div>
              <input
                id="search"
                v-model="search"
                type="text"
                placeholder="Nombre, documento o correo..."
                class="block w-full pl-10 pr-3 py-2 border border-theme-primary rounded-md leading-5 bg-theme-card text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
              />
            </div>
          </div>

          <!-- Tipo de Persona -->
          <div>
            <label for="tipo_persona" class="block text-sm font-medium text-theme-primary mb-1">
              Tipo de Persona
            </label>
            <select
              id="tipo_persona"
              v-model="tipoPersona"
              class="block w-full px-3 py-2 border border-theme-primary rounded-md bg-theme-card text-theme-primary focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option value="">Todos los tipos</option>
              <option v-for="tipo in tiposPersona" :key="tipo" :value="tipo">
                {{ tipo }}
              </option>
            </select>
          </div>

          <!-- Items por página -->
          <div>
            <label for="per_page" class="block text-sm font-medium text-theme-primary mb-1">
              Por página
            </label>
            <select
              id="per_page"
              v-model="perPage"
              class="block w-full px-3 py-2 border border-theme-primary rounded-md bg-theme-card text-theme-primary focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option :value="10">10</option>
              <option :value="15">15</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>

        <!-- Botón limpiar filtros -->
        <div class="mt-4 flex justify-end">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-3 py-2 border border-theme-primary shadow-theme-sm text-sm leading-4 font-medium rounded-md text-theme-primary bg-theme-card hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <Icon name="refresh" :size="16" class="mr-2" />
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Lista de Personas -->
      <div class="bg-theme-card rounded-lg shadow-theme-sm border border-theme-primary overflow-hidden">
        <div v-if="personas.data.length === 0" class="p-8 text-center">
          <Icon name="users" :size="48" class="mx-auto text-theme-muted" />
          <h3 class="mt-2 text-sm font-medium text-theme-primary">No hay personas</h3>
          <p class="mt-1 text-sm text-theme-secondary">
            No se encontraron personas con los filtros aplicados.
          </p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-theme-primary">
            <thead class="bg-theme-secondary">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-secondary uppercase tracking-wider">
                  Persona
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-secondary uppercase tracking-wider">
                  Documento
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-secondary uppercase tracking-wider">
                  Tipo
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-secondary uppercase tracking-wider">
                  Correo
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-secondary uppercase tracking-wider">
                  Recursos
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-theme-card divide-y divide-theme-primary">
              <tr
                v-for="persona in personas.data"
                :key="persona.idPersona"
                class="hover:bg-theme-secondary cursor-pointer"
                @click="viewPersona(persona)"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-br from-forest-400 to-forest-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                          {{ persona.Nombre.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-theme-primary">
                        {{ persona.Nombre }}
                      </div>
                      <div class="text-sm text-theme-secondary">
                        ID: {{ persona.idPersona }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-theme-primary">{{ persona.documento }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getTipoBadgeColor(persona.TipoPersona)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ persona.TipoPersona }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-theme-primary">
                    {{ persona.correo || 'Sin correo' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <div v-if="persona.portatiles && persona.portatiles.length > 0" class="flex items-center text-xs text-blue-600 dark:text-blue-400">
                      <Icon name="laptop" :size="16" class="mr-1" />
                      {{ persona.portatiles.length }}
                    </div>
                    <div v-if="persona.vehiculos && persona.vehiculos.length > 0" class="flex items-center text-xs text-green-600 dark:text-green-400">
                      <Icon name="car" :size="16" class="mr-1" />
                      {{ persona.vehiculos.length }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="viewPersona(persona)"
                    :disabled="loadingPersona"
                    class="inline-flex items-center px-3 py-1.5 bg-[#00304D] text-white text-sm font-medium rounded-lg hover:bg-[#002033] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <Icon :name="loadingPersona ? 'loader' : 'eye'" :size="16" :class="{ 'mr-1': true, 'animate-spin': loadingPersona }" />
                    {{ loadingPersona ? 'Cargando...' : 'Ver detalles' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="personas.links && personas.links.length > 3" class="bg-theme-card px-4 py-3 border-t border-theme-primary sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="personas.prev_page_url"
                @click="router.visit(personas.prev_page_url)"
                class="relative inline-flex items-center px-4 py-2 border border-theme-primary text-sm font-medium rounded-md text-theme-primary bg-theme-card hover:bg-theme-secondary"
              >
                Anterior
              </button>
              <button
                v-if="personas.next_page_url"
                @click="router.visit(personas.next_page_url)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-theme-primary text-sm font-medium rounded-md text-theme-primary bg-theme-card hover:bg-theme-secondary"
              >
                Siguiente
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-theme-secondary">
                  Mostrando
                  <span class="font-medium">{{ personas.from }}</span>
                  a
                  <span class="font-medium">{{ personas.to }}</span>
                  de
                  <span class="font-medium">{{ personas.total }}</span>
                  resultados
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <template v-for="(link, index) in personas.links" :key="index">
                    <button
                      v-if="link.url"
                      @click="router.visit(link.url)"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        link.active
                          ? 'z-10 bg-green-50 border-green-500 text-green-600'
                          : 'bg-theme-card border-theme-primary text-theme-secondary hover:bg-theme-secondary',
                        index === 0 ? 'rounded-l-md' : '',
                        index === personas.links.length - 1 ? 'rounded-r-md' : ''
                      ]"
                      v-html="link.label"
                    />
                    <span
                      v-else
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border border-theme-primary bg-theme-card text-sm font-medium text-theme-muted',
                        index === 0 ? 'rounded-l-md' : '',
                        index === personas.links.length - 1 ? 'rounded-r-md' : ''
                      ]"
                      v-html="link.label"
                    />
                  </template>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalles de Persona -->
    <PersonaDetalleModal 
      :persona="selectedPersona" 
      :show="showModal" 
      @close="closeModal" 
    />
  </SystemLayout>
</template>