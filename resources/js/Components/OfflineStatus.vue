<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">Estado del Sistema</h3>
      <div class="flex items-center space-x-2">
        <div 
          :class="{
            'bg-green-100 text-green-800': isOnline,
            'bg-red-100 text-red-800': !isOnline
          }"
          class="px-2 py-1 rounded-full text-xs font-medium"
        >
          {{ isOnline ? 'En línea' : 'Sin conexión' }}
        </div>
      </div>
    </div>

    <!-- Información de cache -->
    <div class="space-y-3">
      <div class="grid grid-cols-3 gap-4 text-sm">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600">{{ storageInfo.personas }}</div>
          <div class="text-gray-500">Personas</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-purple-600">{{ storageInfo.portatiles }}</div>
          <div class="text-gray-500">Portátiles</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-orange-600">{{ storageInfo.vehiculos }}</div>
          <div class="text-gray-500">Vehículos</div>
        </div>
      </div>

      <!-- Datos pendientes de sincronización -->
      <div v-if="hasPendingSync" class="border-t pt-3">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">
            <span class="font-medium">{{ syncStatus.pendingItems }}</span> elemento(s) pendiente(s)
          </div>
          <button
            v-if="isOnline"
            @click="$emit('sync')"
            :disabled="syncStatus.inProgress"
            class="flex items-center space-x-1 px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 disabled:opacity-50"
          >
            <svg 
              :class="{ 'animate-spin': syncStatus.inProgress }"
              class="w-3 h-3" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
            </svg>
            <span>{{ syncStatus.inProgress ? 'Sincronizando...' : 'Sincronizar' }}</span>
          </button>
        </div>
      </div>

      <!-- Última sincronización -->
      <div v-if="syncStatus.lastSync" class="text-xs text-gray-500">
        Última sincronización: {{ formatLastSync(syncStatus.lastSync) }}
      </div>

      <!-- Modo offline activo -->
      <div v-if="!isOnline" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
        <div class="flex items-start space-x-2">
          <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-yellow-800">Modo offline activo</p>
            <p class="text-xs text-yellow-700 mt-1">
              Los accesos se guardarán localmente y se sincronizarán cuando se restablezca la conexión.
            </p>
          </div>
        </div>
      </div>

      <!-- Cache disponible -->
      <div v-if="hasCachedData && !isOnline" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
        <div class="flex items-start space-x-2">
          <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-800">Datos disponibles offline</p>
            <p class="text-xs text-blue-700 mt-1">
              Puedes consultar información de personas y registrar accesos usando los datos en cache.
            </p>
          </div>
        </div>
      </div>

      <!-- Sin datos en cache -->
      <div v-if="!hasCachedData && !isOnline" class="bg-red-50 border border-red-200 rounded-lg p-3">
        <div class="flex items-start space-x-2">
          <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-red-800">Sin datos en cache</p>
            <p class="text-xs text-red-700 mt-1">
              Necesitas conexión a internet para cargar los datos iniciales.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Acciones -->
    <div class="mt-4 pt-4 border-t space-y-2">
      <button
        @click="$emit('clear-cache')"
        class="w-full px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
      >
        Limpiar Cache
      </button>
      
      <button
        v-if="isOnline"
        @click="$emit('refresh-cache')"
        class="w-full px-3 py-2 text-sm bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200"
      >
        Actualizar Cache
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  isOnline: {
    type: Boolean,
    required: true
  },
  storageInfo: {
    type: Object,
    required: true
  },
  syncStatus: {
    type: Object,
    required: true
  }
})

defineEmits(['sync', 'clear-cache', 'refresh-cache'])

const hasCachedData = computed(() => {
  return props.storageInfo.personas > 0 || 
         props.storageInfo.portatiles > 0 || 
         props.storageInfo.vehiculos > 0
})

const hasPendingSync = computed(() => {
  return props.syncStatus.pendingItems > 0
})

const formatLastSync = (timestamp) => {
  if (!timestamp) return 'Nunca'
  
  const date = new Date(timestamp)
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / 60000)
  
  if (diffMins < 1) return 'Hace un momento'
  if (diffMins < 60) return `Hace ${diffMins} minuto${diffMins > 1 ? 's' : ''}`
  
  const diffHours = Math.floor(diffMins / 60)
  if (diffHours < 24) return `Hace ${diffHours} hora${diffHours > 1 ? 's' : ''}`
  
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
