<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import QrScanner from '@/Components/QrScanner.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  estadisticas: Object,
  accesosActivos: Array,
  historialReciente: Array
})

const page = usePage()

// Estado del componente
const scannedCodes = ref({
  persona: null,
  portatil: null,
  vehiculo: null
})

const personaInfo = ref(null)
const isProcessing = ref(false)
const showPersonaInfo = ref(false)
const showConfirmModal = ref(false)
const registroInstantaneo = ref(false)
const refreshInterval = ref(null)
const notification = ref(null)

// Computadas
const canProcess = computed(() => {
  return scannedCodes.value.persona && !isProcessing.value
})

const estadisticasActuales = ref(props.estadisticas)
const accesosActivosActuales = ref(props.accesosActivos)
const historialRecienteActual = ref(props.historialReciente)

// Métodos
const handleQrScanned = async (qrEvent) => {
  const { type, data } = qrEvent

  if (type === 'persona') {
    scannedCodes.value.persona = data
    await buscarPersona(data)
  } else if (type === 'portatil') {
    scannedCodes.value.portatil = data
  } else if (type === 'vehiculo') {
    scannedCodes.value.vehiculo = data
  }

  if (qrEvent.manual && canProcess.value) {
    await procesarAcceso()
  }
}

const buscarPersona = async (qrPersona) => {
  try {
    const response = await fetch(route('system.celador.qr.buscar-persona'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token
      },
      body: JSON.stringify({ qr_persona: qrPersona })
    })
    
    const result = await response.json()
    
    if (response.ok) {
      personaInfo.value = result
      showPersonaInfo.value = true
      
      // Si está activado el registro instantáneo, procesar directamente
      if (registroInstantaneo.value) {
        await procesarAcceso()
      } else {
        showConfirmModal.value = true
      }
    } else {
      throw new Error(result.message || 'Persona no encontrada')
    }
  } catch (error) {
    console.error('Error al buscar persona:', error)
    showNotification('error', error.message || 'Persona no encontrada')
  }
}

const procesarAcceso = async () => {
  if (!canProcess.value) return

  isProcessing.value = true

  try {
    router.post(route('system.celador.qr.registrar'), {
      qr_persona: scannedCodes.value.persona,
      qr_portatil: scannedCodes.value.portatil,
      qr_vehiculo: scannedCodes.value.vehiculo
    }, {
      onSuccess: (page) => {
        limpiarCodigos()
        showNotification('success', 'Acceso registrado correctamente')
        refreshData()
      },
      onError: (errors) => {
        console.error('Error al registrar acceso:', errors)
        const errorMessage = Object.values(errors)[0] || 'Error al procesar el acceso'
        showNotification('error', errorMessage)
      },
      onFinish: () => {
        isProcessing.value = false
      }
    })
  } catch (error) {
    console.error('Error al procesar acceso:', error)
    showNotification('error', error.message || 'Error al procesar el acceso')
    isProcessing.value = false
  }
}

const limpiarCodigos = () => {
  scannedCodes.value = {
    persona: null,
    portatil: null,
    vehiculo: null
  }
  personaInfo.value = null
  showPersonaInfo.value = false
  showConfirmModal.value = false
}

const cerrarModal = () => {
  showConfirmModal.value = false
}

const confirmarAcceso = async () => {
  showConfirmModal.value = false
  await procesarAcceso()
}

// Función para registro directo sin modal (opcional)
const registrarDirecto = async () => {
  if (!canProcess.value) return
  await procesarAcceso()
}

const refreshData = async () => {
  try {
    const statsResponse = await fetch(route('system.celador.qr.estadisticas'))
    if (statsResponse.ok) {
      estadisticasActuales.value = await statsResponse.json()
    }

    const activosResponse = await fetch(route('system.celador.qr.accesos-activos'))
    if (activosResponse.ok) {
      accesosActivosActuales.value = await activosResponse.json()
    }

    const historialResponse = await fetch(route('system.celador.qr.historial'))
    if (historialResponse.ok) {
      historialRecienteActual.value = await historialResponse.json()
    }
  } catch (error) {
    console.error('Error al actualizar datos:', error)
  }
}

const showNotification = (type, message, data = null) => {
  notification.value = {
    type,
    message,
    data,
    timestamp: Date.now()
  }

  setTimeout(() => {
    if (notification.value && notification.value.timestamp === notification.value.timestamp) {
      notification.value = null
    }
  }, 5000)
}

const closeNotification = () => {
  notification.value = null
}

// Funciones de utilidad

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDuration = (entrada, salida = null) => {
  const start = new Date(entrada)
  const end = salida ? new Date(salida) : new Date()
  const diff = Math.floor((end - start) / 1000 / 60)
  
  if (diff < 60) return `${diff}m`
  const hours = Math.floor(diff / 60)
  const minutes = diff % 60
  return `${hours}h ${minutes}m`
}

onMounted(() => {
  refreshInterval.value = setInterval(refreshData, 30000)
  
  if (page.props.flash?.success) {
    showNotification('success', page.props.flash.success.mensaje, page.props.flash.success)
  }
  if (page.props.flash?.warning) {
    showNotification('warning', page.props.flash.warning.mensaje, page.props.flash.warning)
  }
  if (page.props.flash?.error) {
    showNotification('error', page.props.flash.error)
  }
})

onUnmounted(() => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
})
</script>

<template>
  <SystemLayout>
    <Head title="Verificación QR" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-theme-primary">Verificación QR</h2>
        <div class="flex items-center space-x-4">
          <!-- Estado de conexión -->
          <div class="flex items-center space-x-2 text-sm">
            <svg 
              :class="{
                'text-green-500': isOnline,
                'text-red-500': !isOnline
              }" 
              class="w-4 h-4" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <circle cx="10" cy="10" r="3"/>
            </svg>
            <span :class="{ 'text-theme-secondary': isOnline, 'text-red-600': !isOnline }">
              {{ isOnline ? 'En línea' : 'Sin conexión' }}
            </span>
          </div>

          <!-- Indicador de datos pendientes -->
          <div v-if="hasPendingSync" class="flex items-center space-x-2 text-sm text-orange-600">
            <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
            </svg>
            <span>{{ syncStatus.pendingItems }} pendiente(s)</span>
          </div>

          <!-- Botón de sincronización manual -->
          <button
            v-if="isOnline && hasPendingSync"
            @click="handleSyncData"
            :disabled="syncStatus.inProgress"
            class="flex items-center space-x-1 px-3 py-1 text-xs bg-theme-tertiary text-blue-700 rounded-full hover:bg-theme-secondary disabled:opacity-50"
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
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        
        <!-- Notificaciones -->
        <div v-if="notification" class="fixed top-4 right-4 z-50 max-w-md">
          <div 
            :class="{
              'bg-green-50 border-green-200 text-green-800': notification.type === 'success',
              'bg-yellow-50 border-yellow-200 text-yellow-800': notification.type === 'warning',
              'bg-red-50 border-red-200 text-red-800': notification.type === 'error'
            }"
            class="border rounded-lg p-4 shadow-lg"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <Icon v-if="notification.type === 'success'" name="check-circle" :size="20" class="text-green-400" />
                <Icon v-else-if="notification.type === 'warning'" name="alert-triangle" :size="20" class="text-yellow-400" />
                <Icon v-else name="x-circle" :size="20" class="text-red-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-sm font-medium">{{ notification.message }}</p>
                <div v-if="notification.data" class="mt-2 text-xs">
                  <div v-if="notification.data.persona">
                    <strong>{{ notification.data.persona }}</strong> - {{ notification.data.documento }}
                  </div>
                  <div v-if="notification.data.hora">
                    Hora: {{ notification.data.hora }}
                  </div>
                  <div v-if="notification.data.duracion">
                    Duración: {{ notification.data.duracion }}
                  </div>
                </div>
              </div>
              <button @click="closeNotification" class="ml-3 flex-shrink-0">
                <Icon name="x" :size="16" />
              </button>
            </div>
          </div>
        </div>

        <!-- Estadísticas del día -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                  <Icon name="log-in" :size="20" class="text-green-600" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Entradas</p>
                <p class="text-lg font-semibold text-gray-900">{{ estadisticasActuales?.total_entradas || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                  <Icon name="log-out" :size="20" class="text-red-600" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Salidas</p>
                <p class="text-lg font-semibold text-gray-900">{{ estadisticasActuales?.total_salidas || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <Icon name="users" :size="20" class="text-blue-600" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Activos</p>
                <p class="text-lg font-semibold text-gray-900">{{ estadisticasActuales?.activos_actuales || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                  <Icon name="laptop" :size="20" class="text-purple-600" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Portátiles</p>
                <p class="text-lg font-semibold text-gray-900">{{ estadisticasActuales?.con_portatil || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                  <Icon name="car" :size="20" class="text-orange-600" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Vehículos</p>
                <p class="text-lg font-semibold text-gray-900">{{ estadisticasActuales?.con_vehiculo || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Área principal de escaneo -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Escáner QR -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Escáner QR</h3>
                <div class="flex items-center space-x-4">
                  <!-- Toggle registro instantáneo -->
                  <label class="flex items-center space-x-2 text-sm">
                    <input 
                      type="checkbox" 
                      v-model="registroInstantaneo"
                      class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >
                    <span class="text-gray-700">Registro instantáneo</span>
                  </label>
                  
                  <button 
                    @click="limpiarCodigos"
                    class="text-sm text-gray-500 hover:text-gray-700"
                  >
                    Limpiar
                  </button>
                </div>
              </div>

              <QrScanner 
                @qr-scanned="handleQrScanned"
                @error="(error) => showNotification('error', error)"
                :auto-start="true"
              />

              <!-- Códigos escaneados -->
              <div v-if="scannedCodes.persona || scannedCodes.portatil || scannedCodes.vehiculo" class="mt-6 space-y-3">
                <h4 class="font-medium text-gray-900">Códigos Escaneados:</h4>
                
                <div v-if="scannedCodes.persona" class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <Icon name="user" :size="20" class="text-green-600" />
                    <span class="text-sm font-medium text-green-800">Persona</span>
                    <code class="text-xs text-green-600">{{ scannedCodes.persona }}</code>
                  </div>
                  <Icon name="check" :size="20" class="text-green-600" />
                </div>

                <div v-if="scannedCodes.portatil" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <Icon name="laptop" :size="20" class="text-blue-600" />
                    <span class="text-sm font-medium text-blue-800">Portátil</span>
                    <code class="text-xs text-blue-600">{{ scannedCodes.portatil }}</code>
                  </div>
                  <Icon name="check" :size="20" class="text-blue-600" />
                </div>

                <div v-if="scannedCodes.vehiculo" class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <Icon name="car" :size="20" class="text-orange-600" />
                    <span class="text-sm font-medium text-orange-800">Vehículo</span>
                    <code class="text-xs text-orange-600">{{ scannedCodes.vehiculo }}</code>
                  </div>
                  <Icon name="check" :size="20" class="text-orange-600" />
                </div>

                <!-- Botón procesar -->
                <button
                  @click="procesarAcceso"
                  :disabled="!canProcess"
                  class="w-full mt-4 flex items-center justify-center space-x-2 px-4 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <Icon v-if="isProcessing" name="loader" :size="20" class="animate-spin" />
                  <Icon v-else name="check-circle" :size="20" />
                  <span>{{ isProcessing ? 'Procesando...' : 'Registrar Acceso' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Panel lateral -->
          <div class="space-y-6">
            <!-- Info de persona escaneada -->
            <div v-if="showPersonaInfo && personaInfo" class="bg-white rounded-lg shadow p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Persona</h3>
              
              <div class="space-y-3">
                <div>
                  <p class="text-sm text-gray-500">Nombre</p>
                  <p class="font-medium">{{ personaInfo.persona.Nombre }}</p>
                </div>
                
                <div>
                  <p class="text-sm text-gray-500">Documento</p>
                  <p class="font-medium">{{ personaInfo.persona.documento }}</p>
                </div>
                
                <div>
                  <p class="text-sm text-gray-500">Tipo</p>
                  <p class="font-medium">{{ personaInfo.persona.TipoPersona }}</p>
                </div>

                <div v-if="personaInfo.tiene_acceso_activo" class="p-3 bg-yellow-50 rounded-lg">
                  <p class="text-sm font-medium text-yellow-800">⚠️ Tiene acceso activo</p>
                  <p class="text-xs text-yellow-600">Esta persona ya tiene un acceso registrado sin salida</p>
                </div>

                <div v-if="personaInfo.portatiles?.length" class="mt-4">
                  <p class="text-sm text-gray-500 mb-2">Portátiles asignados</p>
                  <div class="space-y-1">
                    <div v-for="portatil in personaInfo.portatiles" :key="portatil.portatil_id" class="text-xs bg-gray-100 rounded px-2 py-1">
                      {{ portatil.marca }} {{ portatil.modelo }} - {{ portatil.serial }}
                    </div>
                  </div>
                </div>

                <div v-if="personaInfo.vehiculos?.length" class="mt-4">
                  <p class="text-sm text-gray-500 mb-2">Vehículos asignados</p>
                  <div class="space-y-1">
                    <div v-for="vehiculo in personaInfo.vehiculos" :key="vehiculo.id" class="text-xs bg-gray-100 rounded px-2 py-1">
                      {{ vehiculo.tipo }} - {{ vehiculo.placa }}
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Accesos activos -->
            <div class="bg-white rounded-lg shadow p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Accesos Activos</h3>
              
              <div v-if="accesosActivosActuales?.length" class="space-y-3">
                <div v-for="acceso in accesosActivosActuales.slice(0, 5)" :key="acceso.id" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                  <div>
                    <p class="text-sm font-medium text-blue-900">{{ acceso.persona?.Nombre }}</p>
                    <p class="text-xs text-blue-600">Entrada: {{ formatTime(acceso.fecha_entrada) }}</p>
                    <p class="text-xs text-blue-600">{{ formatDuration(acceso.fecha_entrada) }}</p>
                  </div>
                  <div class="text-right">
                    <div v-if="acceso.portatil" class="text-xs text-blue-600">💻</div>
                    <div v-if="acceso.vehiculo" class="text-xs text-blue-600">🚗</div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center text-gray-500 py-4">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <p class="text-sm">No hay accesos activos</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Historial reciente -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Historial del Día</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persona</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entrada</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salida</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recursos</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="acceso in historialRecienteActual?.slice(0, 10)" :key="acceso.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ acceso.persona?.Nombre }}</div>
                      <div class="text-sm text-gray-500">{{ acceso.persona?.documento }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatTime(acceso.fecha_entrada) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ acceso.fecha_salida ? formatTime(acceso.fecha_salida) : '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDuration(acceso.fecha_entrada, acceso.fecha_salida) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-1">
                      <span v-if="acceso.portatil" title="Portátil">💻</span>
                      <span v-if="acceso.vehiculo" title="Vehículo">🚗</span>
                      <span v-if="!acceso.portatil && !acceso.vehiculo">-</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="{
                        'bg-green-100 text-green-800': acceso.estado === 'activo',
                        'bg-gray-100 text-gray-800': acceso.estado === 'finalizado',
                        'bg-red-100 text-red-800': acceso.estado === 'incidencia'
                      }"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ acceso.estado === 'activo' ? 'Activo' : acceso.estado === 'finalizado' ? 'Finalizado' : 'Incidencia' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="!historialRecienteActual?.length" class="text-center py-8 text-gray-500">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No hay registros del día</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="cerrarModal"></div>

        <!-- Contenido del modal -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                <Icon name="user" :size="24" class="text-blue-600" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Confirmar Acceso
                </h3>
                <div class="mt-4">
                  <div v-if="personaInfo" class="space-y-4">
                    <!-- Información de la persona -->
                    <div class="bg-gray-50 rounded-lg p-4">
                      <div class="text-center">
                        <h4 class="text-lg font-bold text-gray-900">{{ personaInfo.persona?.Nombre }}</h4>
                        <p class="text-sm text-gray-600">{{ personaInfo.persona?.TipoPersona }} • {{ personaInfo.persona?.documento }}</p>
                      </div>
                    </div>

                    <!-- Estado del acceso -->
                    <div class="text-center p-4 rounded-lg" :class="personaInfo.tiene_acceso_activo ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-800'">
                      <div class="text-2xl mb-2">
                        {{ personaInfo.tiene_acceso_activo ? '🚪➡️' : '🚪⬅️' }}
                      </div>
                      <p class="font-bold text-lg">
                        {{ personaInfo.tiene_acceso_activo ? 'REGISTRAR SALIDA' : 'REGISTRAR ENTRADA' }}
                      </p>
                    </div>

                    <!-- Recursos adicionales -->
                    <div v-if="scannedCodes.portatil || scannedCodes.vehiculo" class="bg-blue-50 rounded-lg p-3">
                      <h5 class="text-sm font-medium text-blue-900 mb-2">Recursos adicionales:</h5>
                      <div class="space-y-1 text-xs">
                        <div v-if="scannedCodes.portatil" class="flex items-center text-blue-700">
                          <Icon name="laptop" :size="16" class="mr-1" />
                          Portátil: {{ scannedCodes.portatil }}
                        </div>
                        <div v-if="scannedCodes.vehiculo" class="flex items-center text-blue-700">
                          <Icon name="car" :size="16" class="mr-1" />
                          Vehículo: {{ scannedCodes.vehiculo }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 flex gap-3">
            <button
              @click="cerrarModal"
              :disabled="isProcessing"
              type="button"
              class="flex-1 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              @click="confirmarAcceso"
              :disabled="isProcessing"
              type="button"
              class="flex-1 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 text-base font-medium text-white disabled:opacity-50 disabled:cursor-not-allowed"
              :class="personaInfo?.tiene_acceso_activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
            >
              <Icon v-if="isProcessing" name="loader" :size="16" class="animate-spin -ml-1 mr-2 text-white" />
              {{ isProcessing ? 'Procesando...' : (personaInfo?.tiene_acceso_activo ? 'REGISTRAR SALIDA' : 'REGISTRAR ENTRADA') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>
