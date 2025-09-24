<template>
  <SystemLayout>
    <Head title="Verificación QR" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Verificación QR</h2>
        <div class="flex items-center space-x-2 text-sm text-gray-600">
          <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <circle cx="10" cy="10" r="3"/>
          </svg>
          <span>En línea</span>
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
                <svg v-if="notification.type === 'success'" class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg v-else-if="notification.type === 'warning'" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
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
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
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
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                  </svg>
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
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"/>
                  </svg>
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
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                  </svg>
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
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
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
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
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
                <button 
                  @click="limpiarCodigos"
                  class="text-sm text-gray-500 hover:text-gray-700"
                >
                  Limpiar
                </button>
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
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800">Persona</span>
                    <code class="text-xs text-green-600">{{ scannedCodes.persona }}</code>
                  </div>
                  <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>

                <div v-if="scannedCodes.portatil" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Portátil</span>
                    <code class="text-xs text-blue-600">{{ scannedCodes.portatil }}</code>
                  </div>
                  <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>

                <div v-if="scannedCodes.vehiculo" class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                      <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-.293-.707L15 4.586A1 1 0 0014.414 4H14v3z"/>
                    </svg>
                    <span class="text-sm font-medium text-orange-800">Vehículo</span>
                    <code class="text-xs text-orange-600">{{ scannedCodes.vehiculo }}</code>
                  </div>
                  <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>

                <!-- Botón procesar -->
                <button
                  @click="procesarAcceso"
                  :disabled="!canProcess"
                  class="w-full mt-4 flex items-center justify-center space-x-2 px-4 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="isProcessing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
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
  </SystemLayout>
</template>
