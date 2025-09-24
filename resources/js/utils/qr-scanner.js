import { ref } from 'vue'

// Estado global del escáner
const isScanning = ref(false)
let scanningInterval = null
let jsQR = null

// Cargar jsQR dinámicamente
const loadJsQR = async () => {
  if (jsQR) return jsQR
  
  try {
    const module = await import('jsqr')
    jsQR = module.default
    return jsQR
  } catch (error) {
    console.error('Error al cargar jsQR:', error)
    throw new Error('No se pudo cargar la librería de escaneo QR')
  }
}

// Composable principal
export function useQrScanner() {
  const startScanning = async (videoElement, onQrDetected, options = {}) => {
    if (isScanning.value) {
      console.warn('El escáner ya está activo')
      return
    }

    try {
      // Cargar jsQR
      const qrReader = await loadJsQR()
      
      const {
        scanInterval = 100, // ms entre escaneos
        scanDelay = 500,    // ms de delay después de detectar un QR
        debug = false
      } = options

      isScanning.value = true
      let lastScanTime = 0

      const scan = () => {
        if (!isScanning.value || !videoElement) return

        try {
          // Crear canvas para capturar frame del video
          const canvas = document.createElement('canvas')
          const context = canvas.getContext('2d')
          
          if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
            canvas.width = videoElement.videoWidth
            canvas.height = videoElement.videoHeight
            
            // Dibujar frame actual del video en el canvas
            context.drawImage(videoElement, 0, 0, canvas.width, canvas.height)
            
            // Obtener datos de imagen
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height)
            
            // Escanear QR
            const qrCode = qrReader(imageData.data, imageData.width, imageData.height, {
              inversionAttempts: "dontInvert"
            })

            if (qrCode && qrCode.data) {
              const now = Date.now()
              
              // Evitar escaneos duplicados muy rápidos
              if (now - lastScanTime > scanDelay) {
                lastScanTime = now
                
                if (debug) {
                  console.log('QR detectado:', qrCode.data)
                }
                
                // Validar formato del QR
                if (isValidQrFormat(qrCode.data)) {
                  onQrDetected(qrCode.data)
                } else if (debug) {
                  console.warn('Formato de QR no válido:', qrCode.data)
                }
              }
            }
          }
        } catch (error) {
          if (debug) {
            console.error('Error durante el escaneo:', error)
          }
        }
      }

      // Iniciar bucle de escaneo
      scanningInterval = setInterval(scan, scanInterval)
      
      if (debug) {
        console.log('Escáner QR iniciado')
      }

    } catch (error) {
      isScanning.value = false
      throw error
    }
  }

  const stopScanning = () => {
    if (scanningInterval) {
      clearInterval(scanningInterval)
      scanningInterval = null
    }
    isScanning.value = false
    console.log('Escáner QR detenido')
  }

  return {
    isScanning: readonly(isScanning),
    startScanning,
    stopScanning
  }
}

// Utilidades de validación
export function isValidQrFormat(qrData) {
  if (!qrData || typeof qrData !== 'string') return false
  
  const validPrefixes = ['PERSONA_', 'PORTATIL_', 'VEHICULO_']
  return validPrefixes.some(prefix => qrData.startsWith(prefix))
}

export function parseQrData(qrData) {
  if (!isValidQrFormat(qrData)) {
    return { type: 'unknown', value: qrData }
  }

  if (qrData.startsWith('PERSONA_')) {
    return {
      type: 'persona',
      value: qrData.replace('PERSONA_', ''),
      documento: qrData.replace('PERSONA_', '')
    }
  }

  if (qrData.startsWith('PORTATIL_')) {
    return {
      type: 'portatil',
      value: qrData.replace('PORTATIL_', ''),
      serial: qrData.replace('PORTATIL_', '')
    }
  }

  if (qrData.startsWith('VEHICULO_')) {
    return {
      type: 'vehiculo',
      value: qrData.replace('VEHICULO_', ''),
      placa: qrData.replace('VEHICULO_', '')
    }
  }

  return { type: 'unknown', value: qrData }
}

// Utilidades para generar QR codes (para testing)
export function generateQrData(type, value) {
  const prefixes = {
    persona: 'PERSONA_',
    portatil: 'PORTATIL_',
    vehiculo: 'VEHICULO_'
  }

  const prefix = prefixes[type]
  if (!prefix) {
    throw new Error(`Tipo de QR no válido: ${type}`)
  }

  return `${prefix}${value}`
}

// Utilidades para detectar capacidades del dispositivo
export async function checkCameraSupport() {
  try {
    const devices = await navigator.mediaDevices.enumerateDevices()
    const videoDevices = devices.filter(device => device.kind === 'videoinput')
    
    return {
      supported: !!navigator.mediaDevices && !!navigator.mediaDevices.getUserMedia,
      devices: videoDevices.length,
      hasBackCamera: videoDevices.some(device => 
        device.label.toLowerCase().includes('back') || 
        device.label.toLowerCase().includes('rear')
      )
    }
  } catch (error) {
    return {
      supported: false,
      devices: 0,
      hasBackCamera: false,
      error: error.message
    }
  }
}

// Configuraciones predefinidas para diferentes dispositivos
export const scannerConfigs = {
  mobile: {
    scanInterval: 150,
    scanDelay: 800,
    videoConstraints: {
      facingMode: 'environment',
      width: { ideal: 1280, max: 1920 },
      height: { ideal: 720, max: 1080 }
    }
  },
  desktop: {
    scanInterval: 100,
    scanDelay: 500,
    videoConstraints: {
      width: { ideal: 1280 },
      height: { ideal: 720 }
    }
  },
  lowEnd: {
    scanInterval: 200,
    scanDelay: 1000,
    videoConstraints: {
      width: { ideal: 640 },
      height: { ideal: 480 }
    }
  }
}

// Detectar tipo de dispositivo
export function getDeviceConfig() {
  const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
  const isLowEnd = navigator.hardwareConcurrency <= 2 || navigator.deviceMemory <= 2

  if (isLowEnd) return scannerConfigs.lowEnd
  if (isMobile) return scannerConfigs.mobile
  return scannerConfigs.desktop
}

// Helper para readonly (si no está disponible)
function readonly(ref) {
  return {
    get value() {
      return ref.value
    }
  }
}
