<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleClose"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4">
          <div
            class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-white">Escanear QR</h3>
                    <p class="text-xs text-blue-50">Apunta la cámara al código QR</p>
                  </div>
                </div>
                <button
                  @click="handleClose"
                  class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white transition-all hover:bg-white/30 active:scale-95"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="p-6">
              <!-- Video Preview -->
              <div class="relative aspect-square w-full overflow-hidden rounded-xl bg-gray-900">
                <video
                  ref="videoElement"
                  autoplay
                  playsinline
                  class="h-full w-full object-cover"
                ></video>
                
                <!-- Overlay de escaneo -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="relative h-64 w-64">
                    <!-- Esquinas del marco -->
                    <div class="absolute left-0 top-0 h-16 w-16 border-l-4 border-t-4 border-blue-500"></div>
                    <div class="absolute right-0 top-0 h-16 w-16 border-r-4 border-t-4 border-blue-500"></div>
                    <div class="absolute bottom-0 left-0 h-16 w-16 border-b-4 border-l-4 border-blue-500"></div>
                    <div class="absolute bottom-0 right-0 h-16 w-16 border-b-4 border-r-4 border-blue-500"></div>
                    
                    <!-- Línea de escaneo animada -->
                    <div class="scan-line absolute left-0 right-0 top-0 h-1 bg-blue-500 shadow-lg shadow-blue-500/50"></div>
                  </div>
                </div>

                <!-- Estado de carga -->
                <div
                  v-if="loading"
                  class="absolute inset-0 flex items-center justify-center bg-gray-900/80"
                >
                  <div class="text-center">
                    <svg class="mx-auto h-12 w-12 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-3 text-sm font-medium text-white">Iniciando cámara...</p>
                  </div>
                </div>

                <!-- Mensaje de éxito -->
                <Transition name="success">
                  <div
                    v-if="successMessage"
                    class="absolute inset-0 flex items-center justify-center bg-emerald-600/95"
                  >
                    <div class="text-center px-4">
                      <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white">
                        <svg class="h-10 w-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </div>
                      <p class="text-lg font-bold text-white">{{ successMessage }}</p>
                    </div>
                  </div>
                </Transition>
              </div>

              <!-- Canvas oculto para procesar frames -->
              <canvas ref="canvasElement" class="hidden"></canvas>

              <!-- Info y controles -->
              <div class="mt-4 space-y-3">
                <!-- Último escaneo -->
                <div v-if="lastScanResult" class="rounded-lg bg-blue-50 p-3">
                  <div class="flex items-start space-x-2">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                      <p class="text-xs font-medium text-blue-900">Último escaneo</p>
                      <p class="text-sm font-bold text-blue-700">{{ lastScanResult }}</p>
                      <p class="text-xs text-blue-600">{{ lastScanTime }}</p>
                    </div>
                  </div>
                </div>

                <!-- Instrucciones -->
                <div class="rounded-lg bg-gray-50 p-3">
                  <div class="flex items-start space-x-2">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-xs text-gray-600">
                      Centra el código QR dentro del marco. El escaneo es automático.
                    </p>
                  </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-3">
                  <button
                    type="button"
                    @click="handleClose"
                    class="flex-1 rounded-lg border-2 border-gray-300 bg-white px-4 py-3 font-medium text-gray-700 transition-all hover:bg-gray-50 active:scale-95"
                  >
                    Cerrar
                  </button>
                  <button
                    v-if="!cameraActive"
                    @click="startCamera"
                    :disabled="loading"
                    class="flex-1 rounded-lg bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 font-bold text-white shadow-lg transition-all hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <span class="flex items-center justify-center space-x-2">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <span>Activar Cámara</span>
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue'
import { useQrScanner } from '@/utils/qr-scanner'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  autoStart: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'qr-scanned'])

const videoElement = ref(null)
const canvasElement = ref(null)
const loading = ref(false)
const cameraActive = ref(false)
const lastScanResult = ref('')
const lastScanTime = ref('')
const successMessage = ref('')

let scanningInterval = null
let mediaStream = null

const { scanQrCode } = useQrScanner()

// Watch para iniciar cámara cuando se abre el modal
watch(() => props.show, async (newValue) => {
  if (newValue && props.autoStart) {
    await nextTick()
    await startCamera()
  } else if (!newValue) {
    stopCamera()
    // Limpiar estado
    lastScanResult.value = ''
    lastScanTime.value = ''
    successMessage.value = ''
  }
})

const startCamera = async () => {
  if (cameraActive.value) return

  try {
    loading.value = true

    const constraints = {
      video: {
        facingMode: 'environment',
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    }

    mediaStream = await navigator.mediaDevices.getUserMedia(constraints)
    
    if (videoElement.value) {
      videoElement.value.srcObject = mediaStream
      await videoElement.value.play()
      cameraActive.value = true
      loading.value = false

      // Iniciar escaneo continuo
      startScanning()
    }
  } catch (error) {
    loading.value = false
    console.error('Error al iniciar cámara:', error)
    
    let errorMessage = 'No se pudo acceder a la cámara'
    if (error.name === 'NotAllowedError') {
      errorMessage = 'Permiso de cámara denegado'
    } else if (error.name === 'NotFoundError') {
      errorMessage = 'No se encontró ninguna cámara'
    }
    
    alert(errorMessage)
  }
}

const stopCamera = () => {
  if (scanningInterval) {
    clearInterval(scanningInterval)
    scanningInterval = null
  }

  if (mediaStream) {
    mediaStream.getTracks().forEach(track => track.stop())
    mediaStream = null
  }

  if (videoElement.value) {
    videoElement.value.srcObject = null
  }

  cameraActive.value = false
  loading.value = false
}

const startScanning = () => {
  if (scanningInterval) return

  scanningInterval = setInterval(async () => {
    await processFrame()
  }, 250) // Escanear cada 250ms
}

const processFrame = async () => {
  if (!videoElement.value || !canvasElement.value || !cameraActive.value) {
    return
  }

  const video = videoElement.value
  const canvas = canvasElement.value

  if (video.readyState !== video.HAVE_ENOUGH_DATA) {
    return
  }

  // Configurar canvas
  canvas.width = video.videoWidth
  canvas.height = video.videoHeight

  const context = canvas.getContext('2d')
  context.drawImage(video, 0, 0, canvas.width, canvas.height)

  // Escanear QR
  const imageData = context.getImageData(0, 0, canvas.width, canvas.height)
  const qrData = await scanQrCode(imageData)

  if (qrData) {
    handleQrDetected(qrData)
  }
}

const handleQrDetected = (qrData) => {
  // Detener escaneo temporalmente
  if (scanningInterval) {
    clearInterval(scanningInterval)
    scanningInterval = null
  }

  lastScanResult.value = qrData
  lastScanTime.value = new Date().toLocaleTimeString()

  // Determinar tipo de QR y emitir evento
  if (qrData.startsWith('PERSONA_')) {
    successMessage.value = '✓ Persona detectada'
    emit('qr-scanned', {
      type: 'persona',
      data: qrData,
      timestamp: new Date(),
      manual: false
    })
  } else if (qrData.startsWith('PORTATIL_')) {
    successMessage.value = '✓ Portátil detectado'
    emit('qr-scanned', {
      type: 'portatil',
      data: qrData,
      timestamp: new Date(),
      manual: false
    })
  } else if (qrData.startsWith('VEHICULO_')) {
    successMessage.value = '✓ Vehículo detectado'
    emit('qr-scanned', {
      type: 'vehiculo',
      data: qrData,
      timestamp: new Date(),
      manual: false
    })
  } else {
    successMessage.value = '✓ QR detectado'
    emit('qr-scanned', {
      type: 'unknown',
      data: qrData,
      timestamp: new Date(),
      manual: false
    })
  }

  // Cerrar modal después de 800ms
  setTimeout(() => {
    handleClose()
  }, 800)
}

const handleClose = () => {
  stopCamera()
  emit('close')
}

// Limpiar al desmontar
onUnmounted(() => {
  stopCamera()
})

// Exponer métodos
defineExpose({
  startCamera,
  stopCamera
})
</script>

<style scoped>
/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active > div > div,
.modal-leave-active > div > div {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div > div,
.modal-leave-to > div > div {
  transform: scale(0.9) translateY(-20px);
}

/* Success message transition */
.success-enter-active,
.success-leave-active {
  transition: all 0.3s ease;
}

.success-enter-from,
.success-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Scanning line animation */
.scan-line {
  animation: scan 2s linear infinite;
}

@keyframes scan {
  0%, 100% {
    top: 0;
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    top: 100%;
    opacity: 0;
  }
}

/* PWA optimizations */
@media (max-width: 640px) {
  button {
    min-height: 44px;
  }
}

@media (hover: none) and (pointer: coarse) {
  * {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
  }
}
</style>
