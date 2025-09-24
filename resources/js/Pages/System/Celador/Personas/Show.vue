<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import SystemLayout from '@/Layouts/System/SystemLayout.vue'

const props = defineProps({
  persona: Object
})

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

// Función para volver al listado
const goBack = () => {
  router.visit(route('system.celador.personas.index'))
}

// Función para formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString('es-ES', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <SystemLayout>
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-3 mb-2">
            <button
              @click="goBack"
              class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Volver al listado
            </button>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ persona.Nombre }}
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Información detallada de la persona
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span :class="getTipoBadgeColor(persona.TipoPersona)" class="inline-flex px-3 py-1 text-sm font-medium rounded-full">
            {{ persona.TipoPersona }}
          </span>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Información Personal -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">Información Personal</h3>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-16 w-16">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-forest-400 to-forest-600 flex items-center justify-center">
                  <span class="text-xl font-medium text-white">
                    {{ persona.Nombre.charAt(0).toUpperCase() }}
                  </span>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ persona.Nombre }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  ID: {{ persona.idPersona }}
                </div>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ persona.documento }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Correo Electrónico</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ persona.correo || 'Sin correo registrado' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Código QR</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                  {{ persona.qrCode || 'Sin código QR' }}
                </dd>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Portátiles Asignados -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Portátiles Asignados</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
              {{ persona.portatiles ? persona.portatiles.length : 0 }} portátiles
            </span>
          </div>
        </div>
        <div class="px-6 py-4">
          <div v-if="!persona.portatiles || persona.portatiles.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sin portátiles asignados</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Esta persona no tiene portátiles asignados actualmente.
            </p>
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="portatil in persona.portatiles"
              :key="portatil.id"
              class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
            >
              <div class="flex items-center">
                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ portatil.marca }} {{ portatil.modelo }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Serial: {{ portatil.serial }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehículos Asignados -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Vehículos Asignados</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
              {{ persona.vehiculos ? persona.vehiculos.length : 0 }} vehículos
            </span>
          </div>
        </div>
        <div class="px-6 py-4">
          <div v-if="!persona.vehiculos || persona.vehiculos.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sin vehículos asignados</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Esta persona no tiene vehículos asignados actualmente.
            </p>
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="vehiculo in persona.vehiculos"
              :key="vehiculo.id"
              class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800"
            >
              <div class="flex items-center">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ vehiculo.marca }} {{ vehiculo.modelo }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Placa: {{ vehiculo.placa }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Historial de Accesos Recientes -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Accesos Recientes</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
              Últimos 10 registros
            </span>
          </div>
        </div>
        <div class="px-6 py-4">
          <div v-if="!persona.accesos || persona.accesos.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sin registros de acceso</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Esta persona no tiene registros de acceso recientes.
            </p>
          </div>
          <div v-else class="space-y-3">
            <div
              v-for="acceso in persona.accesos"
              :key="acceso.id"
              class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
            >
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="h-8 w-8 rounded-full bg-forest-100 dark:bg-forest-800 flex items-center justify-center">
                    <svg class="h-4 w-4 text-forest-600 dark:text-forest-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                  </div>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ acceso.tipo_acceso || 'Acceso' }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(acceso.created_at) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                  {{ acceso.estado || 'Permitido' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>
