<template>
  <div class="qr-scanner-container">
    <!-- Escáner de Cámara -->
    <div class="relative">
      <div class="aspect-video rounded-lg overflow-hidden bg-gray-900 relative">
        <video
          ref="videoElement"
          class="w-full h-full object-cover"
          autoplay
          muted
          playsinline
        ></video>
        
        <!-- Overlay de escaneo -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-64 h-64 border-2 border-emerald-400 rounded-lg relative">
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>
            
            <!-- Línea de escaneo animada -->
            <div class="absolute inset-x-0 top-0 h-1 bg-emerald-400 animate-pulse"></div>
          </div>
        </div>

        <!-- Estado de la cámara -->
        <div v-if="cameraStatus" class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
          {{ cameraStatus }}
        </div>
      </div>

      <!-- Controles de cámara -->
      <div class="mt-4 flex justify-center space-x-4">
        <button
          @click="toggleCamera"
          :disabled="loading"
          class="flex items-center space-x-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50"
        >
          <svg v-if="!cameraActive" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"></path>
          </svg>
          <span>{{ cameraActive ? 'Detener' : 'Iniciar' }} Cámara</span>
        </button>

        <button
          @click="openCedulaModal"
          class="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all active:scale-95"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          <span>Entrada Manual</span>
        </button>
      </div>
    </div>

    <!-- Modal de Cédula -->
    <CedulaModal 
      :show="showCedulaModal" 
      @close="closeCedulaModal"
      @submit="handleCedulaSubmit"
      ref="cedulaModalRef"
    />

    <!-- Resultado del último escaneo -->
    <div v-if="lastScanResult" class="mt-6 p-4 bg-gray-50 rounded-lg">
      <h4 class="font-medium text-gray-900 mb-2">Último código escaneado:</h4>
      <p class="text-sm text-gray-600 font-mono">{{ lastScanResult }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ lastScanTime }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useQrScanner } from '@/utils/qr-scanner'
import CedulaModal from '@/Components/CedulaModal.vue'

const props = defineProps({
  autoStart: {
    type: Boolean,
    default: true
  },
  scanDelay: {
    type: Number,
    default: 500
  }
})

const emit = defineEmits(['qr-scanned', 'error', 'camera-ready', 'camera-stopped'])

// Referencias
const videoElement = ref(null)
const cedulaModalRef = ref(null)

// Estado del componente
const cameraActive = ref(false)
const cameraStatus = ref('')
const loading = ref(false)
const lastScanResult = ref('')
const lastScanTime = ref('')
const showCedulaModal = ref(false)

// Composable del escáner QR
const { startScanning, stopScanning, isScanning } = useQrScanner()

// Métodos del Modal
const openCedulaModal = () => {
  showCedulaModal.value = true
}

const closeCedulaModal = () => {
  showCedulaModal.value = false
}

const handleCedulaSubmit = async (cedula) => {
  try {
    // Crear QR virtual con formato PERSONA_ para que sea idéntico al escaneo
    const qrVirtual = `PERSONA_${cedula}`
    
    // Emitir IGUAL que cuando escaneas QR - tipo 'persona'
    emit('qr-scanned', {
      type: 'persona',  // 🔥 Mismo tipo que QR escaneado
      data: qrVirtual,   // 🔥 Mismo formato: PERSONA_123456789
      timestamp: new Date(),
      manual: true
    })

    lastScanResult.value = `Cédula: ${cedula}`
    lastScanTime.value = new Date().toLocaleTimeString()
    
    // Cerrar modal inmediatamente - el flujo es igual que escanear QR
    setTimeout(() => {
      if (cedulaModalRef.value) {
        cedulaModalRef.value.close()
      }
    }, 300)
  } catch (error) {
    console.error('Error al procesar cédula:', error)
    if (cedulaModalRef.value) {
      cedulaModalRef.value.setError(error.message || 'Error al procesar la cédula')
    }
  }
}

const startCamera = async () => {
  if (cameraActive.value) return

  try {
    loading.value = true
    cameraStatus.value = 'Iniciando cámara...'

    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment', // Cámara trasera preferida
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    })

    if (videoElement.value) {
      videoElement.value.srcObject = stream
      await videoElement.value.play()
      
      cameraActive.value = true
      cameraStatus.value = 'Cámara activa'
      
      // Iniciar escaneo
      await startScanning(videoElement.value, handleQrDetected)
      
      emit('camera-ready')
      
      // Ocultar status después de 2 segundos
      setTimeout(() => {
        cameraStatus.value = ''
      }, 2000)
    }
  } catch (error) {
    console.error('Error al iniciar cámara:', error)
    cameraStatus.value = 'Error al acceder a la cámara'
    
    let errorMessage = 'No se pudo acceder a la cámara'
    if (error.name === 'NotAllowedError') {
      errorMessage = 'Permiso de cámara denegado'
    } else if (error.name === 'NotFoundError') {
      errorMessage = 'No se encontró cámara disponible'
    }
    
    emit('error', errorMessage)
    
    // Cambiar a modo manual automáticamente
    setTimeout(() => {
      switchMode('manual')
    }, 3000)
  } finally {
    loading.value = false
  }
}

const stopCamera = () => {
  if (!cameraActive.value) return

  try {
    stopScanning()
    
    if (videoElement.value && videoElement.value.srcObject) {
      const tracks = videoElement.value.srcObject.getTracks()
      tracks.forEach(track => track.stop())
      videoElement.value.srcObject = null
    }
    
    cameraActive.value = false
    cameraStatus.value = ''
    
    emit('camera-stopped')
  } catch (error) {
    console.error('Error al detener cámara:', error)
  }
}

const toggleCamera = () => {
  if (cameraActive.value) {
    stopCamera()
  } else {
    startCamera()
  }
}

const handleQrDetected = (qrData) => {
  lastScanResult.value = qrData
  lastScanTime.value = new Date().toLocaleTimeString()
  
  // Determinar tipo de QR y emitir evento
  if (qrData.startsWith('PERSONA_')) {
    emit('qr-scanned', {
      type: 'persona',
      data: qrData,
      timestamp: new Date()
    })
  } else if (qrData.startsWith('PORTATIL_')) {
    emit('qr-scanned', {
      type: 'portatil',
      data: qrData,
      timestamp: new Date()
    })
  } else if (qrData.startsWith('VEHICULO_')) {
    emit('qr-scanned', {
      type: 'vehiculo',
      data: qrData,
      timestamp: new Date()
    })
  } else {
    emit('qr-scanned', {
      type: 'unknown',
      data: qrData,
      timestamp: new Date()
    })
  }
}


// Lifecycle
onMounted(() => {
  if (props.autoStart) {
    nextTick(() => {
      startCamera()
    })
  }
})

onUnmounted(() => {
  stopCamera()
})

// Exponer métodos públicos
defineExpose({
  startCamera,
  stopCamera,
  openCedulaModal,
  closeCedulaModal
})
</script>

<style scoped>
.qr-scanner-container {
  @apply w-full max-w-2xl mx-auto;
}

@keyframes scan-line {
  0% { transform: translateY(0); }
  100% { transform: translateY(256px); }
}

.animate-scan {
  animation: scan-line 2s ease-in-out infinite;
}
</style>
