import { ref, computed, onMounted, onUnmounted } from 'vue'
import { getOfflineStorage } from '@/utils/offline-storage'
import { parseQrData } from '@/utils/qr-scanner'

export function useQrOffline() {
  const offlineStorage = ref(null)
  const isOnline = ref(navigator.onLine)
  const storageInfo = ref({
    personas: 0,
    portatiles: 0,
    vehiculos: 0,
    accesos: 0,
    syncQueue: 0,
    lastSync: null
  })
  
  const syncStatus = ref({
    inProgress: false,
    lastSync: null,
    pendingItems: 0
  })

  // Estado de conexión
  const connectionStatus = computed(() => {
    return isOnline.value ? 'online' : 'offline'
  })

  // Inicializar storage offline
  const initOfflineStorage = async () => {
    try {
      offlineStorage.value = await getOfflineStorage()
      await updateStorageInfo()
      
      // Configurar listeners para cambios de conexión
      setupConnectionListeners()
      
      console.log('Sistema offline inicializado correctamente')
    } catch (error) {
      console.error('Error inicializando sistema offline:', error)
    }
  }

  // Configurar listeners de conexión
  const setupConnectionListeners = () => {
    const handleOnline = () => {
      isOnline.value = true
      console.log('Conexión restaurada')
      syncPendingData()
    }

    const handleOffline = () => {
      isOnline.value = false
      console.log('Conexión perdida - modo offline activado')
    }

    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)

    // Cleanup en unmount
    onUnmounted(() => {
      window.removeEventListener('online', handleOnline)
      window.removeEventListener('offline', handleOffline)
    })
  }

  // Actualizar información del storage
  const updateStorageInfo = async () => {
    if (!offlineStorage.value) return

    try {
      const info = await offlineStorage.value.getStorageInfo()
      storageInfo.value = info
      
      syncStatus.value.pendingItems = info.accesos + info.syncQueue
      syncStatus.value.lastSync = info.lastSync
    } catch (error) {
      console.error('Error obteniendo información del storage:', error)
    }
  }

  // Buscar persona por QR (online/offline)
  const buscarPersonaPorQr = async (qrPersona) => {
    const parsedQr = parseQrData(qrPersona)
    
    if (parsedQr.type !== 'persona') {
      throw new Error('Código QR de persona inválido')
    }

    const documento = parsedQr.documento

    // Intentar búsqueda online primero
    if (isOnline.value) {
      try {
        const response = await fetch('/system/celador/qr/buscar-persona', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify({ qr: qrPersona })
        })

        if (response.ok) {
          const result = await response.json()
          if (result.success) {
            // Cachear resultado para uso offline
            await offlineStorage.value.cachePersona(result.data.persona)
            if (result.data.portatiles) {
              for (const portatil of result.data.portatiles) {
                await offlineStorage.value.cachePortatil(portatil)
              }
            }
            if (result.data.vehiculos) {
              for (const vehiculo of result.data.vehiculos) {
                await offlineStorage.value.cacheVehiculo(vehiculo)
              }
            }
            
            return result.data
          }
        }
      } catch (error) {
        console.warn('Error en búsqueda online, intentando offline:', error)
      }
    }

    // Búsqueda offline
    if (offlineStorage.value) {
      const persona = await offlineStorage.value.getPersonaByDocumento(documento)
      
      if (persona) {
        // Buscar portátiles y vehículos asociados
        const [portatiles, vehiculos] = await Promise.all([
          getPortatilesByPersona(persona.idPersona),
          getVehiculosByPersona(persona.idPersona)
        ])

        return {
          persona,
          portatiles,
          vehiculos,
          tiene_acceso_activo: false, // En offline no podemos verificar esto
          acceso_activo: null,
          offline: true
        }
      }
    }

    throw new Error(`Persona no encontrada: ${documento}`)
  }

  // Buscar portátil por QR (online/offline)
  const buscarPortatilPorQr = async (qrPortatil) => {
    const parsedQr = parseQrData(qrPortatil)
    
    if (parsedQr.type !== 'portatil') {
      throw new Error('Código QR de portátil inválido')
    }

    const serial = parsedQr.serial

    // Intentar búsqueda online primero
    if (isOnline.value && offlineStorage.value) {
      // En modo online, usar cache si está disponible
      const cached = await offlineStorage.value.getPortatilBySerial(serial)
      if (cached && (Date.now() - cached.lastUpdated) < 300000) { // 5 minutos
        return cached
      }
    }

    // Búsqueda offline
    if (offlineStorage.value) {
      const portatil = await offlineStorage.value.getPortatilBySerial(serial)
      if (portatil) {
        return { ...portatil, offline: true }
      }
    }

    throw new Error(`Portátil no encontrado: ${serial}`)
  }

  // Buscar vehículo por QR (online/offline)
  const buscarVehiculoPorQr = async (qrVehiculo) => {
    const parsedQr = parseQrData(qrVehiculo)
    
    if (parsedQr.type !== 'vehiculo') {
      throw new Error('Código QR de vehículo inválido')
    }

    const placa = parsedQr.placa

    // Búsqueda offline
    if (offlineStorage.value) {
      const vehiculo = await offlineStorage.value.getVehiculoByPlaca(placa)
      if (vehiculo) {
        return { ...vehiculo, offline: true }
      }
    }

    throw new Error(`Vehículo no encontrado: ${placa}`)
  }

  // Obtener portátiles por persona
  const getPortatilesByPersona = async (personaId) => {
    if (!offlineStorage.value) return []

    try {
      const tx = offlineStorage.value.db.transaction('portatiles_cache', 'readonly')
      const index = tx.objectStore('portatiles_cache').index('persona_id')
      return await index.getAll(personaId)
    } catch (error) {
      console.error('Error obteniendo portátiles:', error)
      return []
    }
  }

  // Obtener vehículos por persona
  const getVehiculosByPersona = async (personaId) => {
    if (!offlineStorage.value) return []

    try {
      const tx = offlineStorage.value.db.transaction('vehiculos_cache', 'readonly')
      const index = tx.objectStore('vehiculos_cache').index('persona_id')
      return await index.getAll(personaId)
    } catch (error) {
      console.error('Error obteniendo vehículos:', error)
      return []
    }
  }

  // Registrar acceso (online/offline)
  const registrarAcceso = async (accesoData) => {
    if (isOnline.value) {
      // Intentar registro online
      try {
        const response = await fetch('/system/celador/qr/registrar', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify(accesoData)
        })

        if (response.ok) {
          const result = await response.json()
          return { success: true, data: result, online: true }
        } else {
          throw new Error(`Error HTTP: ${response.status}`)
        }
      } catch (error) {
        console.warn('Error en registro online, guardando offline:', error)
      }
    }

    // Registro offline
    if (offlineStorage.value) {
      const offlineAcceso = await offlineStorage.value.saveOfflineAcceso(accesoData)
      await updateStorageInfo()
      
      return {
        success: true,
        data: {
          tipo: 'entrada', // Asumimos entrada en offline
          mensaje: 'Acceso guardado offline - se sincronizará cuando haya conexión',
          persona: accesoData.persona_nombre || 'Persona',
          documento: accesoData.persona_documento || '',
          hora: new Date().toLocaleTimeString(),
          offline: true
        },
        online: false
      }
    }

    throw new Error('No se pudo registrar el acceso')
  }

  // Sincronizar datos pendientes
  const syncPendingData = async () => {
    if (!offlineStorage.value || syncStatus.value.inProgress) {
      return
    }

    syncStatus.value.inProgress = true

    try {
      await offlineStorage.value.syncPendingData()
      await updateStorageInfo()
      syncStatus.value.lastSync = Date.now()
      
      console.log('Sincronización completada')
      return true
    } catch (error) {
      console.error('Error durante la sincronización:', error)
      return false
    } finally {
      syncStatus.value.inProgress = false
    }
  }

  // Limpiar cache
  const clearCache = async () => {
    if (!offlineStorage.value) return

    try {
      await offlineStorage.value.clearCache()
      await updateStorageInfo()
      console.log('Cache limpiado exitosamente')
    } catch (error) {
      console.error('Error limpiando cache:', error)
      throw error
    }
  }

  // Buscar datos offline
  const searchOffline = async (query, type = 'all') => {
    if (!offlineStorage.value) return { personas: [], portatiles: [], vehiculos: [] }

    try {
      return await offlineStorage.value.searchOffline(query, type)
    } catch (error) {
      console.error('Error en búsqueda offline:', error)
      return { personas: [], portatiles: [], vehiculos: [] }
    }
  }

  // Verificar si hay datos en cache
  const hasCachedData = computed(() => {
    return storageInfo.value.personas > 0 || 
           storageInfo.value.portatiles > 0 || 
           storageInfo.value.vehiculos > 0
  })

  // Verificar si hay datos pendientes de sincronización
  const hasPendingSync = computed(() => {
    return syncStatus.value.pendingItems > 0
  })

  // Obtener estado del sistema offline
  const getOfflineStatus = () => {
    return {
      isOnline: isOnline.value,
      connectionStatus: connectionStatus.value,
      storageInfo: storageInfo.value,
      syncStatus: syncStatus.value,
      hasCachedData: hasCachedData.value,
      hasPendingSync: hasPendingSync.value
    }
  }

  // Inicializar en mount
  onMounted(() => {
    initOfflineStorage()
    
    // Actualizar info cada 30 segundos
    const interval = setInterval(updateStorageInfo, 30000)
    
    onUnmounted(() => {
      clearInterval(interval)
    })
  })

  return {
    // Estado
    isOnline,
    connectionStatus,
    storageInfo,
    syncStatus,
    hasCachedData,
    hasPendingSync,
    
    // Métodos de búsqueda
    buscarPersonaPorQr,
    buscarPortatilPorQr,
    buscarVehiculoPorQr,
    searchOffline,
    
    // Métodos de registro
    registrarAcceso,
    
    // Métodos de sincronización
    syncPendingData,
    clearCache,
    updateStorageInfo,
    
    // Utilidades
    getOfflineStatus,
    initOfflineStorage
  }
}
