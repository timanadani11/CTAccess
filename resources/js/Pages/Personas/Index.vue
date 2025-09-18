<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestión de Personas</h1>
      <Link 
        :href="route('personas.create')" 
        class="px-4 py-2 bg-[#39A900] text-white rounded-lg hover:bg-[#007832] transition-colors"
      >
        Nueva Persona
      </Link>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="mb-6 flex items-center gap-4">
      <div class="flex-1">
        <input 
          v-model="searchForm.search"
          @input="search"
          type="text" 
          placeholder="Buscar por nombre, documento o tipo..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent"
        >
      </div>
      <select 
        v-model="searchForm.per_page" 
        @change="search"
        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900]"
      >
        <option value="10">10 por página</option>
        <option value="15">15 por página</option>
        <option value="25">25 por página</option>
        <option value="50">50 por página</option>
      </select>
    </div>

    <!-- Tabla de personas -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 text-left">
            <th class="p-4 font-semibold text-gray-700">ID</th>
            <th class="p-4 font-semibold text-gray-700">Documento</th>
            <th class="p-4 font-semibold text-gray-700">Nombre</th>
            <th class="p-4 font-semibold text-gray-700">Tipo</th>
            <th class="p-4 font-semibold text-gray-700">Portátiles</th>
            <th class="p-4 font-semibold text-gray-700">Vehículos</th>
            <th class="p-4 font-semibold text-gray-700">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="persona in personas.data" :key="persona.id" class="border-t hover:bg-gray-50">
            <td class="p-4">{{ persona.id }}</td>
            <td class="p-4">{{ persona.documento ?? '—' }}</td>
            <td class="p-4 font-medium">{{ persona.nombre }}</td>
            <td class="p-4">
              <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                {{ persona.tipoPersona }}
              </span>
            </td>
            <td class="p-4">
              <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                {{ persona.portatiles?.length ?? 0 }}
              </span>
            </td>
            <td class="p-4">
              <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                {{ persona.vehiculos?.length ?? 0 }}
              </span>
            </td>
            <td class="p-4">
              <div class="flex items-center gap-2">
                <Link 
                  :href="route('personas.show', persona.id)"
                  class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  Ver
                </Link>
                <Link 
                  :href="route('personas.edit', persona.id)"
                  class="px-3 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700"
                >
                  Editar
                </Link>
                <button 
                  @click="confirmDelete(persona)"
                  class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Eliminar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div v-if="personas.links" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-600">
        Mostrando {{ personas.from }} a {{ personas.to }} de {{ personas.total }} registros
      </div>
      <div class="flex gap-1">
        <Link 
          v-for="link in personas.links" 
          :key="link.label"
          :href="link.url"
          :class="[
            'px-3 py-2 text-sm border rounded',
            link.active 
              ? 'bg-[#39A900] text-white border-[#39A900]' 
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
          ]"
          v-html="link.label"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'

// Props recibidas desde el controlador
const props = defineProps({
  personas: Object,
  filters: Object,
})

// Formulario de búsqueda reactivo
const searchForm = reactive({
  search: props.filters.search || '',
  per_page: props.filters.per_page || 15,
})

// Función debounce simple
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

// Función de búsqueda con debounce para evitar muchas peticiones
const search = debounce(() => {
  router.get(route('personas.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Función para confirmar eliminación
const confirmDelete = (persona) => {
  if (confirm(`¿Estás seguro de eliminar a ${persona.nombre}?`)) {
    router.delete(route('personas.destroy', persona.id), {
      onSuccess: () => {
        // Inertia automáticamente recarga la página con los datos actualizados
      },
      onError: (errors) => {
        alert('Error al eliminar: ' + (errors.error || 'Error desconocido'))
      }
    })
  }
}
</script>
