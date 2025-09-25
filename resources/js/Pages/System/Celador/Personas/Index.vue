<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { debounce } from 'lodash'
import Icon from '@/Components/Icon.vue'

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

// Función para ver detalles de persona
const viewPersona = (persona) => {
  router.visit(route('system.celador.personas.show', persona.idPersona))
}
</script>

<template>
  <SystemLayout>
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Gestión de Personas
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Consulta y gestiona la información de las personas registradas
          </p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
          <Icon name="users" :size="16" />
          Total: {{ personas.total }} personas
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filtros -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Buscar persona
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon name="search" :size="16" class="text-gray-400" />
              </div>
              <input
                id="search"
                v-model="search"
                type="text"
                placeholder="Nombre, documento o correo..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500"
              />
            </div>
          </div>

          <!-- Tipo de Persona -->
          <div>
            <label for="tipo_persona" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Tipo de Persona
            </label>
            <select
              id="tipo_persona"
              v-model="tipoPersona"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500"
            >
              <option value="">Todos los tipos</option>
              <option v-for="tipo in tiposPersona" :key="tipo" :value="tipo">
                {{ tipo }}
              </option>
            </select>
          </div>

          <!-- Items por página -->
          <div>
            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Por página
            </label>
            <select
              id="per_page"
              v-model="perPage"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500"
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
            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest-500"
          >
            <Icon name="refresh" :size="16" class="mr-2" />
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Lista de Personas -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div v-if="personas.data.length === 0" class="p-8 text-center">
          <Icon name="users" :size="48" class="mx-auto text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay personas</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            No se encontraron personas con los filtros aplicados.
          </p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Persona
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Documento
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Tipo
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Correo
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Recursos
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="persona in personas.data"
                :key="persona.idPersona"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
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
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ persona.Nombre }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        ID: {{ persona.idPersona }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ persona.documento }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getTipoBadgeColor(persona.TipoPersona)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ persona.TipoPersona }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
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
                    @click.stop="viewPersona(persona)"
                    class="text-forest-600 dark:text-forest-400 hover:text-forest-900 dark:hover:text-forest-300"
                  >
                    Ver detalles
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="personas.links && personas.links.length > 3" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="personas.prev_page_url"
                @click="router.visit(personas.prev_page_url)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Anterior
              </button>
              <button
                v-if="personas.next_page_url"
                @click="router.visit(personas.next_page_url)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Siguiente
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
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
                          ? 'z-10 bg-forest-50 dark:bg-forest-900 border-forest-500 text-forest-600 dark:text-forest-400'
                          : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600',
                        index === 0 ? 'rounded-l-md' : '',
                        index === personas.links.length - 1 ? 'rounded-r-md' : ''
                      ]"
                      v-html="link.label"
                    />
                    <span
                      v-else
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400',
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
  </SystemLayout>
</template>