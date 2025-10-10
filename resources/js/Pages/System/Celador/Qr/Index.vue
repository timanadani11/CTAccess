<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import QrScannerModal from '@/Components/QrScannerModal.vue'
import CedulaModal from '@/Components/CedulaModal.vue'
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

// Modales
const showQrScannerModal = ref(false)
const showCedulaModal = ref(false)
const qrScannerModalRef = ref(null)
const cedulaModalRef = ref(null)

// Computadas
const canProcess = computed(() => {
  return scannedCodes.value.persona && !isProcessing.value
})

const estadisticasActuales = ref(props.estadisticas)
const accesosActivosActuales = ref(props.accesosActivos)
const historialRecienteActual = ref(props.historialReciente)

// Métodos de modales
const openQrScanner = () => {
  showQrScannerModal.value = true
}

const closeQrScanner = () => {
  showQrScannerModal.value = false
}

const openCedulaModal = () => {
  showCedulaModal.value = true
}

const closeCedulaModal = () => {
  showCedulaModal.value = false
}

const handleAccesoRegistrado = (data) => {
  // El modal ya registró el acceso, solo necesitamos actualizar la UI
  console.log('Acceso registrado desde modal:', data)
  
  // Recargar la página para actualizar las estadísticas y el historial
  router.reload({
    only: ['accesosActivos', 'historial', 'estadisticas']
  })
  
  // El modal se encarga de limpiarse y quedar abierto para el siguiente registro
}

// Métodos de escaneo
const handleQrScanned = async (qrEvent) => {
  const { type, data } = qrEvent

  if (type === 'persona') {
    // 🔥 MISMO FLUJO para QR escaneado Y entrada manual
    scannedCodes.value.persona = data
    await buscarPersona(data)
  } else if (type === 'portatil') {
    scannedCodes.value.portatil = data
  } else if (type === 'vehiculo') {
    scannedCodes.value.vehiculo = data
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
      
      // Establecer el QR de persona para procesamiento
      scannedCodes.value.persona = qrPersona
      
      // 🔥 INFORMACIÓN IMPORTANTE PARA EL CELADOR
      const esEntrada = result.es_entrada
      const esSalida = result.es_salida
      
      // Mostrar información al celador
      let mensaje = `${result.persona.Nombre} - ${result.mensaje_accion}`
      
      // Agregar info de portátil/vehículo automáticamente detectados
      if (esEntrada) {
        const elementos = []
        if (result.tiene_portatil) {
          elementos.push(`✓ Portátil: ${result.portatil_asociado.marca} ${result.portatil_asociado.modelo}`)
        }
        if (result.tiene_vehiculo) {
          elementos.push(`✓ Vehículo: ${result.vehiculo_asociado.placa}`)
        }
        
        if (elementos.length > 0) {
          showNotification('info', `${mensaje}\n${elementos.join('\n')}`)
        }
      }
      
      // Si es SALIDA, verificar si necesita escanear portátil/vehículo
      if (esSalida && result.acceso_activo) {
        const requiereVerificaciones = []
        
        if (result.acceso_activo.requiere_verificacion_portatil) {
          requiereVerificaciones.push(`📱 Debe escanear QR del portátil: ${result.acceso_activo.portatil_entrada.serial}`)
        }
        
        if (result.acceso_activo.requiere_verificacion_vehiculo) {
          requiereVerificaciones.push(`🚗 Debe escanear QR del vehículo: ${result.acceso_activo.vehiculo_entrada.placa}`)
        }
        
        if (requiereVerificaciones.length > 0) {
          showNotification('warning', `SALIDA - Verificación requerida:\n${requiereVerificaciones.join('\n')}`)
          // NO procesar automáticamente - debe escanear portátil/vehículo
          showConfirmModal.value = true
          return
        }
      }
      
      // Si está activado el registro instantáneo Y no requiere verificaciones, procesar directamente
      if (registroInstantaneo.value && (!esSalida || !result.acceso_activo?.requiere_verificacion_portatil)) {
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

// buscarPersonaPorCedula eliminada - ahora todo usa buscarPersona con QR virtual

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

    <div class="py-3">
      <div class="mx-auto max-w-7xl space-y-3 px-2 sm:px-4">
        
        <!-- Notificaciones -->
        <div v-if="notification" class="fixed top-2 right-2 z-50 max-w-sm">
          <div 
            :class="{
              'bg-green-50 border-green-200 text-green-800': notification.type === 'success',
              'bg-yellow-50 border-yellow-200 text-yellow-800': notification.type === 'warning',
              'bg-red-50 border-red-200 text-red-800': notification.type === 'error'
            }"
            class="border rounded-lg p-2 shadow-lg text-xs"
          >
            <div class="flex items-start gap-2">
              <Icon v-if="notification.type === 'success'" name="check-circle" :size="16" class="text-green-400 flex-shrink-0" />
              <Icon v-else-if="notification.type === 'warning'" name="alert-triangle" :size="16" class="text-yellow-400 flex-shrink-0" />
              <Icon v-else name="x-circle" :size="16" class="text-red-400 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium">{{ notification.message }}</p>
                <div v-if="notification.data" class="mt-1 text-xs opacity-75">
                  <div v-if="notification.data.persona">{{ notification.data.persona }}</div>
                </div>
              </div>
              <button @click="closeNotification" class="flex-shrink-0">
                <Icon name="x" :size="14" />
              </button>
            </div>
          </div>
        </div>

        <!-- Estadísticas compactas -->
        <div class="grid grid-cols-5 gap-2">
          <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-2">
            <div class="flex flex-col items-center text-center">
              <Icon name="log-in" :size="16" class="text-green-600 mb-1" />
              <p class="text-xs text-theme-secondary">Entradas</p>
              <p class="text-lg font-bold text-theme-primary">{{ estadisticasActuales?.total_entradas || 0 }}</p>
            </div>
          </div>

          <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-2">
            <div class="flex flex-col items-center text-center">
              <Icon name="log-out" :size="16" class="text-red-600 mb-1" />
              <p class="text-xs text-theme-secondary">Salidas</p>
              <p class="text-lg font-bold text-theme-primary">{{ estadisticasActuales?.total_salidas || 0 }}</p>
            </div>
          </div>

          <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-2">
            <div class="flex flex-col items-center text-center">
              <Icon name="users" :size="16" class="text-blue-600 mb-1" />
              <p class="text-xs text-theme-secondary">Activos</p>
              <p class="text-lg font-bold text-theme-primary">{{ estadisticasActuales?.activos_actuales || 0 }}</p>
            </div>
          </div>

          <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-2">
            <div class="flex flex-col items-center text-center">
              <Icon name="laptop" :size="16" class="text-purple-600 mb-1" />
              <p class="text-xs text-theme-secondary">Portátiles</p>
              <p class="text-lg font-bold text-theme-primary">{{ estadisticasActuales?.con_portatil || 0 }}</p>
            </div>
          </div>

          <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-2">
            <div class="flex flex-col items-center text-center">
              <Icon name="car" :size="16" class="text-orange-600 mb-1" />
              <p class="text-xs text-theme-secondary">Vehículos</p>
              <p class="text-lg font-bold text-theme-primary">{{ estadisticasActuales?.con_vehiculo || 0 }}</p>
            </div>
          </div>
        </div>

        <!-- Área principal compacta -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
          <!-- Botones de Acción PWA -->
          <div class="lg:col-span-2">
            <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-3">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-theme-primary">Control de Accesos</h3>
                <div class="flex items-center gap-2">
                  <!-- Toggle registro instantáneo -->
                  <label class="flex items-center gap-1 text-xs">
                    <input 
                      type="checkbox" 
                      v-model="registroInstantaneo"
                      class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-3 h-3"
                    >
                    <span class="text-theme-primary hidden sm:inline">Instantáneo</span>
                  </label>
                  
                  <button 
                    @click="limpiarCodigos"
                    class="text-xs text-theme-muted hover:text-theme-primary transition-colors px-2 py-1 rounded hover:bg-theme-secondary"
                  >
                    <Icon name="x" :size="12" />
                  </button>
                </div>
              </div>

              <!-- Botones compactos PWA -->
              <div class="grid grid-cols-2 gap-2 mb-3">
                <!-- Botón Escanear QR -->
                <button
                  @click="openQrScanner"
                  class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-3 shadow-sm active:scale-95 transition-all touch-manipulation"
                >
                  <Icon name="qr-code" :size="20" />
                  <div class="text-left">
                    <div class="text-sm font-bold">Escanear QR</div>
                    <div class="text-xs opacity-90">Cámara</div>
                  </div>
                </button>

                <!-- Botón Entrada Manual -->
                <button
                  @click="openCedulaModal"
                  class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg p-3 shadow-sm active:scale-95 transition-all touch-manipulation"
                >
                  <Icon name="edit" :size="20" />
                  <div class="text-left">
                    <div class="text-sm font-bold">Manual</div>
                    <div class="text-xs opacity-90">Cédula</div>
                  </div>
                </button>
              </div>

              <!-- Códigos escaneados compactos -->
              <div v-if="scannedCodes.persona || scannedCodes.portatil || scannedCodes.vehiculo" class="space-y-2">
                <div v-if="scannedCodes.persona" class="flex items-center gap-2 p-2 bg-green-50 rounded text-xs">
                  <Icon name="user" :size="14" class="text-green-600" />
                  <span class="font-medium text-green-800">Persona</span>
                  <Icon name="check" :size="14" class="text-green-600 ml-auto" />
                </div>

                <div v-if="scannedCodes.portatil" class="flex items-center gap-2 p-2 bg-blue-50 rounded text-xs">
                  <Icon name="laptop" :size="14" class="text-blue-600" />
                  <span class="font-medium text-blue-800">Portátil</span>
                  <Icon name="check" :size="14" class="text-blue-600 ml-auto" />
                </div>

                <div v-if="scannedCodes.vehiculo" class="flex items-center gap-2 p-2 bg-orange-50 rounded text-xs">
                  <Icon name="car" :size="14" class="text-orange-600" />
                  <span class="font-medium text-orange-800">Vehículo</span>
                  <Icon name="check" :size="14" class="text-orange-600 ml-auto" />
                </div>

                <!-- Botón procesar compacto -->
                <button
                  @click="procesarAcceso"
                  :disabled="!canProcess"
                  class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium touch-manipulation active:scale-95 transition-all"
                >
                  <Icon v-if="isProcessing" name="loader" :size="16" class="animate-spin" />
                  <Icon v-else name="check-circle" :size="16" />
                  <span>{{ isProcessing ? 'Procesando...' : 'Registrar' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Panel lateral compacto -->
          <div class="space-y-3">
            <!-- Info de persona escaneada -->
            <div v-if="showPersonaInfo && personaInfo" class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-3">
              <h3 class="text-sm font-semibold text-theme-primary mb-2">Info Persona</h3>
              
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span class="text-theme-secondary">Nombre:</span>
                  <span class="font-medium text-theme-primary">{{ personaInfo.persona.Nombre }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-theme-secondary">Doc:</span>
                  <span class="font-medium text-theme-primary">{{ personaInfo.persona.documento }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-theme-secondary">Tipo:</span>
                  <span class="font-medium text-theme-primary">{{ personaInfo.persona.TipoPersona }}</span>
                </div>

                <div v-if="personaInfo.tiene_acceso_activo" class="p-2 bg-yellow-50 rounded text-xs">
                  <p class="font-medium text-yellow-800">⚠️ Acceso activo</p>
                </div>

                <div v-if="personaInfo.portatiles?.length" class="pt-2 border-t border-theme-primary">
                  <p class="text-theme-secondary mb-1">💻 Portátiles</p>
                  <div v-for="portatil in personaInfo.portatiles" :key="portatil.portatil_id" class="text-xs bg-gray-100 rounded px-2 py-1 mb-1">
                    {{ portatil.marca }} {{ portatil.modelo }}
                  </div>
                </div>

                <div v-if="personaInfo.vehiculos?.length" class="pt-2 border-t border-theme-primary">
                  <p class="text-theme-secondary mb-1">🚗 Vehículos</p>
                  <div v-for="vehiculo in personaInfo.vehiculos" :key="vehiculo.id" class="text-xs bg-gray-100 rounded px-2 py-1 mb-1">
                    {{ vehiculo.tipo }} - {{ vehiculo.placa }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Accesos activos compactos -->
            <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm p-3">
              <h3 class="text-sm font-semibold text-theme-primary mb-2">Accesos Activos</h3>
              
              <div v-if="accesosActivosActuales?.length" class="space-y-2">
                <div v-for="acceso in accesosActivosActuales.slice(0, 3)" :key="acceso.id" class="flex items-start justify-between p-2 bg-blue-50 rounded text-xs">
                  <div class="flex-1 min-w-0">
                    <p class="font-medium text-blue-900 truncate">{{ acceso.persona?.Nombre }}</p>
                    <p class="text-blue-600">{{ formatTime(acceso.fecha_entrada) }} • {{ formatDuration(acceso.fecha_entrada) }}</p>
                  </div>
                  <div class="flex gap-1 flex-shrink-0 ml-2">
                    <span v-if="acceso.portatil" title="Portátil">💻</span>
                    <span v-if="acceso.vehiculo" title="Vehículo">🚗</span>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center text-theme-secondary py-3 text-xs">
                <Icon name="users" :size="20" class="mx-auto mb-1 opacity-50" />
                <p>Sin accesos activos</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Historial compacto -->
        <div class="bg-theme-card border border-theme-primary rounded-lg shadow-sm">
          <div class="px-3 py-2 border-b border-theme-primary flex justify-between items-center">
            <h3 class="text-sm font-semibold text-theme-primary">Historial del Día</h3>
            <span class="text-xs text-theme-secondary">Últimos 10</span>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-xs">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-3 py-2 text-left font-medium text-theme-secondary uppercase">Persona</th>
                  <th class="px-3 py-2 text-left font-medium text-theme-secondary uppercase">Entrada</th>
                  <th class="px-3 py-2 text-left font-medium text-theme-secondary uppercase">Salida</th>
                  <th class="px-3 py-2 text-left font-medium text-theme-secondary uppercase">Duración</th>
                  <th class="px-3 py-2 text-center font-medium text-theme-secondary uppercase">Rec.</th>
                  <th class="px-3 py-2 text-left font-medium text-theme-secondary uppercase">Estado</th>
                </tr>
              </thead>
              <tbody class="bg-theme-card divide-y divide-theme-primary">
                <tr v-for="acceso in historialRecienteActual?.slice(0, 10)" :key="acceso.id" class="hover:bg-theme-secondary transition-colors">
                  <td class="px-3 py-2">
                    <div class="font-medium text-theme-primary truncate max-w-xs">{{ acceso.persona?.Nombre }}</div>
                    <div class="text-theme-secondary">{{ acceso.persona?.documento }}</div>
                  </td>
                  <td class="px-3 py-2 text-theme-primary whitespace-nowrap">
                    {{ formatTime(acceso.fecha_entrada) }}
                  </td>
                  <td class="px-3 py-2 text-theme-primary whitespace-nowrap">
                    {{ acceso.fecha_salida ? formatTime(acceso.fecha_salida) : '-' }}
                  </td>
                  <td class="px-3 py-2 text-theme-primary whitespace-nowrap">
                    {{ formatDuration(acceso.fecha_entrada, acceso.fecha_salida) }}
                  </td>
                  <td class="px-3 py-2 text-center">
                    <div class="flex gap-1 justify-center">
                      <span v-if="acceso.portatil" title="Portátil">💻</span>
                      <span v-if="acceso.vehiculo" title="Vehículo">🚗</span>
                      <span v-if="!acceso.portatil && !acceso.vehiculo" class="text-theme-secondary">-</span>
                    </div>
                  </td>
                  <td class="px-3 py-2">
                    <span 
                      :class="{
                        'bg-green-100 text-green-800': acceso.estado === 'activo',
                        'bg-gray-100 text-gray-800': acceso.estado === 'finalizado',
                        'bg-red-100 text-red-800': acceso.estado === 'incidencia'
                      }"
                      class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                    >
                      {{ acceso.estado === 'activo' ? 'Activo' : acceso.estado === 'finalizado' ? 'Fin' : 'Inc' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="!historialRecienteActual?.length" class="text-center py-4 text-theme-secondary text-xs">
            <Icon name="file-text" :size="24" class="mx-auto mb-1 opacity-50" />
            <p>Sin registros del día</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación Compacto -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="cerrarModal"></div>

      <!-- Contenido del modal -->
      <div class="relative bg-theme-card border-2 border-theme-primary rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-4">
          <!-- Header con icono -->
          <div class="flex items-center gap-3 mb-3">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
              <Icon name="user" :size="20" class="text-blue-600" />
            </div>
            <h3 class="text-base font-bold text-theme-primary">Confirmar Acceso</h3>
          </div>

          <div v-if="personaInfo" class="space-y-3">
            <!-- Información de la persona -->
            <div class="bg-theme-secondary border border-theme-primary rounded-lg p-3">
              <div class="text-center">
                <h4 class="text-base font-bold text-theme-primary">{{ personaInfo.persona?.Nombre }}</h4>
                <p class="text-xs text-theme-secondary mt-1">{{ personaInfo.persona?.TipoPersona }} • {{ personaInfo.persona?.documento }}</p>
              </div>
            </div>

            <!-- Estado del acceso -->
            <div class="text-center p-3 rounded-lg" :class="personaInfo.tiene_acceso_activo ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-800'">
              <div class="text-3xl mb-1">
                {{ personaInfo.tiene_acceso_activo ? '🚪➡️' : '🚪⬅️' }}
              </div>
              <p class="font-bold text-sm">
                {{ personaInfo.tiene_acceso_activo ? 'REGISTRAR SALIDA' : 'REGISTRAR ENTRADA' }}
              </p>
            </div>

            <!-- Recursos adicionales -->
            <div v-if="scannedCodes.portatil || scannedCodes.vehiculo" class="bg-blue-50 rounded-lg p-2">
              <h5 class="text-xs font-semibold text-blue-900 mb-1">Recursos adicionales:</h5>
              <div class="space-y-1 text-xs">
                <div v-if="scannedCodes.portatil" class="flex items-center text-blue-700">
                  <Icon name="laptop" :size="14" class="mr-1" />
                  Portátil incluido
                </div>
                <div v-if="scannedCodes.vehiculo" class="flex items-center text-blue-700">
                  <Icon name="car" :size="14" class="mr-1" />
                  Vehículo incluido
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="bg-theme-secondary px-4 py-3 flex gap-2 border-t border-theme-primary">
          <button
            @click="cerrarModal"
            :disabled="isProcessing"
            type="button"
            class="flex-1 px-4 py-2 bg-theme-card border border-theme-primary text-sm font-medium text-theme-primary hover:bg-theme-tertiary disabled:opacity-50 transition-colors rounded-lg touch-manipulation"
          >
            Cancelar
          </button>
          <button
            @click="confirmarAcceso"
            :disabled="isProcessing"
            type="button"
            class="flex-1 px-4 py-2 text-sm font-bold text-white disabled:opacity-50 disabled:cursor-not-allowed rounded-lg touch-manipulation active:scale-95 transition-all"
            :class="personaInfo?.tiene_acceso_activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
          >
            <Icon v-if="isProcessing" name="loader" :size="14" class="inline animate-spin mr-1" />
            {{ isProcessing ? 'Procesando...' : (personaInfo?.tiene_acceso_activo ? 'SALIDA' : 'ENTRADA') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modales -->
    <QrScannerModal
      :show="showQrScannerModal"
      @close="closeQrScanner"
      @acceso-registrado="handleAccesoRegistrado"
      ref="qrScannerModalRef"
    />

    <CedulaModal
      :show="showCedulaModal"
      @close="closeCedulaModal"
      @acceso-registrado="handleAccesoRegistrado"
      ref="cedulaModalRef"
    />
  </SystemLayout>
</template>
