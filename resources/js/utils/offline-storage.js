import { openDB } from 'idb'

// Configuración de la base de datos
const DB_NAME = 'CTAccessOffline'
const DB_VERSION = 1

// Nombres de las stores
const STORES = {
  ACCESOS_PENDIENTES: 'accesos_pendientes',
  PERSONAS_CACHE: 'personas_cache',
  PORTATILES_CACHE: 'portatiles_cache',
  VEHICULOS_CACHE: 'vehiculos_cache',
  SYNC_QUEUE: 'sync_queue',
  SETTINGS: 'settings'
}

// Inicializar base de datos
let dbPromise = null

const initDB = async () => {
  if (dbPromise) return dbPromise

  dbPromise = openDB(DB_NAME, DB_VERSION, {
    upgrade(db) {
      // Store para accesos pendientes de sincronización
      if (!db.objectStoreNames.contains(STORES.ACCESOS_PENDIENTES)) {
        const accesosStore = db.createObjectStore(STORES.ACCESOS_PENDIENTES, {
          keyPath: 'id',
          autoIncrement: true
        })
        accesosStore.createIndex('timestamp', 'timestamp')
        accesosStore.createIndex('persona_id', 'persona_id')
        accesosStore.createIndex('synced', 'synced')
      }

      // Store para cache de personas
      if (!db.objectStoreNames.contains(STORES.PERSONAS_CACHE)) {
        const personasStore = db.createObjectStore(STORES.PERSONAS_CACHE, {
          keyPath: 'idPersona'
        })
        personasStore.createIndex('documento', 'documento', { unique: true })
        personasStore.createIndex('qrCode', 'qrCode', { unique: true })
        personasStore.createIndex('lastUpdated', 'lastUpdated')
      }

      // Store para cache de portátiles
      if (!db.objectStoreNames.contains(STORES.PORTATILES_CACHE)) {
        const portatilsStore = db.createObjectStore(STORES.PORTATILES_CACHE, {
          keyPath: 'portatil_id'
        })
        portatilsStore.createIndex('persona_id', 'persona_id')
        portatilsStore.createIndex('serial', 'serial', { unique: true })
        portatilsStore.createIndex('qrCode', 'qrCode', { unique: true })
      }

      // Store para cache de vehículos
      if (!db.objectStoreNames.contains(STORES.VEHICULOS_CACHE)) {
        const vehiculosStore = db.createObjectStore(STORES.VEHICULOS_CACHE, {
          keyPath: 'id'
        })
        vehiculosStore.createIndex('persona_id', 'persona_id')
        vehiculosStore.createIndex('placa', 'placa', { unique: true })
        vehiculosStore.createIndex('qrCode', 'qrCode', { unique: true })
      }

      // Store para cola de sincronización
      if (!db.objectStoreNames.contains(STORES.SYNC_QUEUE)) {
        const syncStore = db.createObjectStore(STORES.SYNC_QUEUE, {
          keyPath: 'id',
          autoIncrement: true
        })
        syncStore.createIndex('action', 'action')
        syncStore.createIndex('timestamp', 'timestamp')
        syncStore.createIndex('priority', 'priority')
      }

      // Store para configuraciones
      if (!db.objectStoreNames.contains(STORES.SETTINGS)) {
        db.createObjectStore(STORES.SETTINGS, { keyPath: 'key' })
      }
    }
  })

  return dbPromise
}

// Clase principal para manejo offline
export class OfflineStorage {
  constructor() {
    this.db = null
    this.isOnline = navigator.onLine
    this.syncInProgress = false
    this.setupEventListeners()
  }

  async init() {
    this.db = await initDB()
    await this.setupInitialSettings()
    
    // Intentar sincronización inicial si estamos online
    if (this.isOnline) {
      this.syncPendingData()
    }
  }

  setupEventListeners() {
    window.addEventListener('online', () => {
      this.isOnline = true
      console.log('Conexión restaurada, iniciando sincronización...')
      this.syncPendingData()
    })

    window.addEventListener('offline', () => {
      this.isOnline = false
      console.log('Conexión perdida, modo offline activado')
    })
  }

  async setupInitialSettings() {
    const settings = await this.getSetting('initialized')
    if (!settings) {
      await this.setSetting('initialized', true)
      await this.setSetting('lastSync', null)
      await this.setSetting('syncEnabled', true)
    }
  }

  // Métodos para configuraciones
  async setSetting(key, value) {
    const tx = this.db.transaction(STORES.SETTINGS, 'readwrite')
    await tx.objectStore(STORES.SETTINGS).put({ key, value, timestamp: Date.now() })
  }

  async getSetting(key) {
    const result = await this.db.get(STORES.SETTINGS, key)
    return result?.value || null
  }

  // Métodos para cache de personas
  async cachePersona(persona) {
    const personaWithTimestamp = {
      ...persona,
      lastUpdated: Date.now(),
      cached: true
    }
    
    const tx = this.db.transaction(STORES.PERSONAS_CACHE, 'readwrite')
    await tx.objectStore(STORES.PERSONAS_CACHE).put(personaWithTimestamp)
  }

  async getPersonaByDocumento(documento) {
    const tx = this.db.transaction(STORES.PERSONAS_CACHE, 'readonly')
    const index = tx.objectStore(STORES.PERSONAS_CACHE).index('documento')
    return await index.get(documento)
  }

  async getPersonaByQr(qrCode) {
    const tx = this.db.transaction(STORES.PERSONAS_CACHE, 'readonly')
    const index = tx.objectStore(STORES.PERSONAS_CACHE).index('qrCode')
    return await index.get(qrCode)
  }

  // Métodos para cache de portátiles
  async cachePortatil(portatil) {
    const portatilWithTimestamp = {
      ...portatil,
      lastUpdated: Date.now(),
      cached: true
    }
    
    const tx = this.db.transaction(STORES.PORTATILES_CACHE, 'readwrite')
    await tx.objectStore(STORES.PORTATILES_CACHE).put(portatilWithTimestamp)
  }

  async getPortatilBySerial(serial) {
    const tx = this.db.transaction(STORES.PORTATILES_CACHE, 'readonly')
    const index = tx.objectStore(STORES.PORTATILES_CACHE).index('serial')
    return await index.get(serial)
  }

  async getPortatilByQr(qrCode) {
    const tx = this.db.transaction(STORES.PORTATILES_CACHE, 'readonly')
    const index = tx.objectStore(STORES.PORTATILES_CACHE).index('qrCode')
    return await index.get(qrCode)
  }

  // Métodos para cache de vehículos
  async cacheVehiculo(vehiculo) {
    const vehiculoWithTimestamp = {
      ...vehiculo,
      lastUpdated: Date.now(),
      cached: true
    }
    
    const tx = this.db.transaction(STORES.VEHICULOS_CACHE, 'readwrite')
    await tx.objectStore(STORES.VEHICULOS_CACHE).put(vehiculoWithTimestamp)
  }

  async getVehiculoByPlaca(placa) {
    const tx = this.db.transaction(STORES.VEHICULOS_CACHE, 'readonly')
    const index = tx.objectStore(STORES.VEHICULOS_CACHE).index('placa')
    return await index.get(placa)
  }

  async getVehiculoByQr(qrCode) {
    const tx = this.db.transaction(STORES.VEHICULOS_CACHE, 'readonly')
    const index = tx.objectStore(STORES.VEHICULOS_CACHE).index('qrCode')
    return await index.get(qrCode)
  }

  // Métodos para accesos offline
  async saveOfflineAcceso(accesoData) {
    const offlineAcceso = {
      ...accesoData,
      timestamp: Date.now(),
      synced: false,
      offline: true,
      id: `offline_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
    }

    const tx = this.db.transaction(STORES.ACCESOS_PENDIENTES, 'readwrite')
    const result = await tx.objectStore(STORES.ACCESOS_PENDIENTES).add(offlineAcceso)
    
    console.log('Acceso guardado offline:', offlineAcceso)
    
    // Agregar a cola de sincronización
    await this.addToSyncQueue({
      action: 'CREATE_ACCESO',
      data: offlineAcceso,
      priority: 1
    })

    return { ...offlineAcceso, id: result }
  }

  async getPendingAccesos() {
    const tx = this.db.transaction(STORES.ACCESOS_PENDIENTES, 'readonly')
    const index = tx.objectStore(STORES.ACCESOS_PENDIENTES).index('synced')
    return await index.getAll(false)
  }

  async markAccesoAsSynced(id) {
    const tx = this.db.transaction(STORES.ACCESOS_PENDIENTES, 'readwrite')
    const store = tx.objectStore(STORES.ACCESOS_PENDIENTES)
    const acceso = await store.get(id)
    
    if (acceso) {
      acceso.synced = true
      acceso.syncedAt = Date.now()
      await store.put(acceso)
    }
  }

  // Métodos para cola de sincronización
  async addToSyncQueue(item) {
    const queueItem = {
      ...item,
      timestamp: Date.now(),
      attempts: 0,
      maxAttempts: 3
    }

    const tx = this.db.transaction(STORES.SYNC_QUEUE, 'readwrite')
    await tx.objectStore(STORES.SYNC_QUEUE).add(queueItem)
  }

  async getSyncQueue() {
    const tx = this.db.transaction(STORES.SYNC_QUEUE, 'readonly')
    const index = tx.objectStore(STORES.SYNC_QUEUE).index('priority')
    return await index.getAll()
  }

  async removeSyncItem(id) {
    const tx = this.db.transaction(STORES.SYNC_QUEUE, 'readwrite')
    await tx.objectStore(STORES.SYNC_QUEUE).delete(id)
  }

  async updateSyncItemAttempts(id, attempts) {
    const tx = this.db.transaction(STORES.SYNC_QUEUE, 'readwrite')
    const store = tx.objectStore(STORES.SYNC_QUEUE)
    const item = await store.get(id)
    
    if (item) {
      item.attempts = attempts
      item.lastAttempt = Date.now()
      await store.put(item)
    }
  }

  // Sincronización con el servidor
  async syncPendingData() {
    if (this.syncInProgress || !this.isOnline) {
      return
    }

    this.syncInProgress = true
    console.log('Iniciando sincronización de datos pendientes...')

    try {
      const pendingAccesos = await this.getPendingAccesos()
      const syncQueue = await this.getSyncQueue()

      console.log(`Sincronizando ${pendingAccesos.length} accesos y ${syncQueue.length} elementos en cola`)

      // Sincronizar accesos pendientes
      for (const acceso of pendingAccesos) {
        try {
          await this.syncAcceso(acceso)
        } catch (error) {
          console.error('Error sincronizando acceso:', error)
        }
      }

      // Procesar cola de sincronización
      for (const item of syncQueue) {
        try {
          await this.processSyncItem(item)
        } catch (error) {
          console.error('Error procesando item de sincronización:', error)
          await this.updateSyncItemAttempts(item.id, item.attempts + 1)
          
          if (item.attempts >= item.maxAttempts) {
            console.error('Máximo de intentos alcanzado, removiendo item:', item)
            await this.removeSyncItem(item.id)
          }
        }
      }

      await this.setSetting('lastSync', Date.now())
      console.log('Sincronización completada')

    } catch (error) {
      console.error('Error durante la sincronización:', error)
    } finally {
      this.syncInProgress = false
    }
  }

  async syncAcceso(acceso) {
    try {
      // Intentar enviar al servidor
      const response = await fetch('/system/celador/qr/registrar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
          qr_persona: acceso.qr_persona,
          qr_portatil: acceso.qr_portatil,
          qr_vehiculo: acceso.qr_vehiculo,
          offline_id: acceso.id,
          timestamp: acceso.timestamp
        })
      })

      if (response.ok) {
        await this.markAccesoAsSynced(acceso.id)
        console.log('Acceso sincronizado exitosamente:', acceso.id)
      } else {
        throw new Error(`Error HTTP: ${response.status}`)
      }
    } catch (error) {
      console.error('Error sincronizando acceso:', error)
      throw error
    }
  }

  async processSyncItem(item) {
    switch (item.action) {
      case 'CREATE_ACCESO':
        await this.syncAcceso(item.data)
        await this.removeSyncItem(item.id)
        break
      
      case 'UPDATE_CACHE':
        // Actualizar cache con datos del servidor
        await this.updateCacheFromServer()
        await this.removeSyncItem(item.id)
        break
      
      default:
        console.warn('Acción de sincronización desconocida:', item.action)
        await this.removeSyncItem(item.id)
    }
  }

  async updateCacheFromServer() {
    try {
      // Actualizar cache de personas
      const personasResponse = await fetch('/system/celador/personas/search?all=true')
      if (personasResponse.ok) {
        const personas = await personasResponse.json()
        for (const persona of personas.data || []) {
          await this.cachePersona(persona)
        }
      }

      console.log('Cache actualizado desde el servidor')
    } catch (error) {
      console.error('Error actualizando cache:', error)
    }
  }

  // Métodos de utilidad
  async clearCache() {
    const tx = this.db.transaction([
      STORES.PERSONAS_CACHE,
      STORES.PORTATILES_CACHE,
      STORES.VEHICULOS_CACHE
    ], 'readwrite')

    await Promise.all([
      tx.objectStore(STORES.PERSONAS_CACHE).clear(),
      tx.objectStore(STORES.PORTATILES_CACHE).clear(),
      tx.objectStore(STORES.VEHICULOS_CACHE).clear()
    ])

    console.log('Cache limpiado')
  }

  async getStorageInfo() {
    const [personas, portatiles, vehiculos, accesos, syncQueue] = await Promise.all([
      this.db.count(STORES.PERSONAS_CACHE),
      this.db.count(STORES.PORTATILES_CACHE),
      this.db.count(STORES.VEHICULOS_CACHE),
      this.db.count(STORES.ACCESOS_PENDIENTES),
      this.db.count(STORES.SYNC_QUEUE)
    ])

    return {
      personas,
      portatiles,
      vehiculos,
      accesos,
      syncQueue,
      isOnline: this.isOnline,
      lastSync: await this.getSetting('lastSync')
    }
  }

  // Método para buscar datos offline
  async searchOffline(query, type = 'all') {
    const results = {
      personas: [],
      portatiles: [],
      vehiculos: []
    }

    if (type === 'all' || type === 'personas') {
      const tx = this.db.transaction(STORES.PERSONAS_CACHE, 'readonly')
      const personas = await tx.objectStore(STORES.PERSONAS_CACHE).getAll()
      
      results.personas = personas.filter(persona => 
        persona.Nombre?.toLowerCase().includes(query.toLowerCase()) ||
        persona.documento?.includes(query) ||
        persona.correo?.toLowerCase().includes(query.toLowerCase())
      )
    }

    if (type === 'all' || type === 'portatiles') {
      const tx = this.db.transaction(STORES.PORTATILES_CACHE, 'readonly')
      const portatiles = await tx.objectStore(STORES.PORTATILES_CACHE).getAll()
      
      results.portatiles = portatiles.filter(portatil => 
        portatil.serial?.toLowerCase().includes(query.toLowerCase()) ||
        portatil.marca?.toLowerCase().includes(query.toLowerCase()) ||
        portatil.modelo?.toLowerCase().includes(query.toLowerCase())
      )
    }

    if (type === 'all' || type === 'vehiculos') {
      const tx = this.db.transaction(STORES.VEHICULOS_CACHE, 'readonly')
      const vehiculos = await tx.objectStore(STORES.VEHICULOS_CACHE).getAll()
      
      results.vehiculos = vehiculos.filter(vehiculo => 
        vehiculo.placa?.toLowerCase().includes(query.toLowerCase()) ||
        vehiculo.tipo?.toLowerCase().includes(query.toLowerCase())
      )
    }

    return results
  }
}

// Instancia singleton
let offlineStorageInstance = null

export const getOfflineStorage = async () => {
  if (!offlineStorageInstance) {
    offlineStorageInstance = new OfflineStorage()
    await offlineStorageInstance.init()
  }
  return offlineStorageInstance
}

// Composable para Vue
export const useOfflineStorage = () => {
  return {
    getOfflineStorage,
    isOnline: () => navigator.onLine
  }
}
