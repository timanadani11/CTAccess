<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Detalles de Persona</h1>
      <div class="flex gap-2">
        <Link 
          :href="route('personas.edit', persona.id)"
          class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors"
        >
          Editar
        </Link>
        <Link 
          :href="route('personas.index')"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          Volver al Listado
        </Link>
      </div>
    </div>

    <!-- Información principal -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">Información Personal</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
          <p class="text-gray-900">{{ persona.id }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
          <p class="text-gray-900">{{ persona.documento || '—' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
          <p class="text-gray-900 font-medium">{{ persona.nombre }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Persona</label>
          <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
            {{ persona.tipoPersona }}
          </span>
        </div>
        <div v-if="persona.correo" class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
          <p class="text-gray-900">{{ persona.correo }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Fechas</label>
          <div class="text-sm text-gray-600">
            <p>Creado: {{ formatDate(persona.createdAt) }}</p>
            <p>Actualizado: {{ formatDate(persona.updatedAt) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Portátiles -->
    <div v-if="persona.portatiles && persona.portatiles.length > 0" class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">Portátiles Registrados</h2>
      <div class="grid gap-4">
        <div 
          v-for="portatil in persona.portatiles" 
          :key="portatil.id"
          class="border border-gray-200 rounded-lg p-4"
        >
          <div class="grid md:grid-cols-3 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Serial</label>
              <p class="text-gray-900 font-mono">{{ portatil.serial }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
              <p class="text-gray-900">{{ portatil.marca }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
              <p class="text-gray-900">{{ portatil.modelo }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehículos -->
    <div v-if="persona.vehiculos && persona.vehiculos.length > 0" class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">Vehículos Registrados</h2>
      <div class="grid gap-4">
        <div 
          v-for="vehiculo in persona.vehiculos" 
          :key="vehiculo.id"
          class="border border-gray-200 rounded-lg p-4"
        >
          <div class="grid md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
              <p class="text-gray-900">{{ vehiculo.tipo }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
              <p class="text-gray-900 font-mono">{{ vehiculo.placa }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mensaje si no hay portátiles ni vehículos -->
    <div v-if="(!persona.portatiles || persona.portatiles.length === 0) && (!persona.vehiculos || persona.vehiculos.length === 0)" 
         class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
      <p class="text-gray-600">Esta persona no tiene portátiles ni vehículos registrados.</p>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  persona: Object,
})

// Función para formatear fechas
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
</script>
