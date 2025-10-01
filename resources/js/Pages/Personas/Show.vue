<template>
  <AuthenticatedLayout>
    <Head :title="personaData.Nombre || 'Detalles de Persona'" />
    
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-theme-primary">Detalles de Persona</h1>
      <div class="flex gap-2">
        <Link 
          :href="route('personas.edit', personaData.idPersona || personaData.id)"
          class="px-4 py-2 bg-[#FDC300] text-white rounded-lg hover:bg-[#e0af00] transition-colors"
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
    <div class="bg-theme-card shadow-theme-md rounded-lg p-6 mb-6 border border-theme-primary">
      <h2 class="text-xl font-semibold mb-4 text-theme-primary">Información Personal</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-theme-secondary mb-1">ID</label>
          <p class="text-theme-primary">{{ personaData.idPersona || personaData.id || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-theme-secondary mb-1">Documento</label>
          <p class="text-theme-primary">{{ personaData.documento || '—' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-theme-secondary mb-1">Nombre</label>
          <p class="text-theme-primary font-medium">{{ personaData.Nombre || 'Sin nombre' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-theme-secondary mb-1">Tipo de Persona</label>
          <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
            {{ personaData.TipoPersona || 'Sin tipo' }}
          </span>
        </div>
        <div v-if="personaData.correo" class="md:col-span-2">
          <label class="block text-sm font-medium text-theme-secondary mb-1">Correo Electrónico</label>
          <p class="text-theme-primary">{{ personaData.correo }}</p>
        </div>
        <div v-if="personaData.qrCode" class="md:col-span-2">
          <label class="block text-sm font-medium text-theme-secondary mb-1">Código QR</label>
          <p class="text-theme-primary font-mono text-sm">{{ personaData.qrCode }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-theme-secondary mb-1">Fechas</label>
          <div class="text-sm text-theme-secondary">
            <p>Creado: {{ formatDate(personaData.createdAt || personaData.created_at) }}</p>
            <p>Actualizado: {{ formatDate(personaData.updatedAt || personaData.updated_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Portátiles -->
    <div v-if="portatilesList.length > 0" class="bg-theme-card shadow-theme-md rounded-lg p-6 mb-6 border border-theme-primary">
      <h2 class="text-xl font-semibold mb-4 text-theme-primary">Portátiles Registrados</h2>
      <div class="grid gap-4">
        <div 
          v-for="portatil in portatilesList" 
          :key="portatil.id || portatil.portatil_id"
          class="border border-theme-primary rounded-lg p-4"
        >
          <div class="grid md:grid-cols-3 gap-3">
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-1">Serial</label>
              <p class="text-theme-primary font-mono">{{ portatil.serial || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-1">Marca</label>
              <p class="text-theme-primary">{{ portatil.marca || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-1">Modelo</label>
              <p class="text-theme-primary">{{ portatil.modelo || 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehículos -->
    <div v-if="vehiculosList.length > 0" class="bg-theme-card shadow-theme-md rounded-lg p-6 mb-6 border border-theme-primary">
      <h2 class="text-xl font-semibold mb-4 text-theme-primary">Vehículos Registrados</h2>
      <div class="grid gap-4">
        <div 
          v-for="vehiculo in vehiculosList" 
          :key="vehiculo.id || vehiculo.vehiculo_id"
          class="border border-theme-primary rounded-lg p-4"
        >
          <div class="grid md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-1">Tipo</label>
              <p class="text-theme-primary">{{ vehiculo.tipo || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-1">Placa</label>
              <p class="text-theme-primary font-mono">{{ vehiculo.placa || 'N/A' }}</p>
            </div>
            <div v-if="vehiculo.marca || vehiculo.modelo">
              <label class="block text-sm font-medium text-theme-secondary mb-1">Marca/Modelo</label>
              <p class="text-theme-primary">{{ vehiculo.marca }} {{ vehiculo.modelo }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mensaje si no hay portátiles ni vehículos -->
    <div v-if="portatilesList.length === 0 && vehiculosList.length === 0" 
         class="bg-gray-50 dark:bg-gray-800 border border-theme-primary rounded-lg p-6 text-center">
      <p class="text-theme-secondary">Esta persona no tiene portátiles ni vehículos registrados.</p>
    </div>
  </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  persona: {
    type: Object,
    required: true
  }
})

// Validación de datos
if (!props.persona) {
  console.error('⚠️ Persona no proporcionada a la vista Show')
}

// Computed para acceso seguro a los datos
const personaData = computed(() => props.persona || {})
const portatilesList = computed(() => props.persona?.portatiles || [])
const vehiculosList = computed(() => props.persona?.vehiculos || [])

// Función para formatear fechas
const formatDate = (dateString) => {
  if (!dateString) return '—'
  try {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (e) {
    console.error('Error formateando fecha:', e)
    return '—'
  }
}
</script>
