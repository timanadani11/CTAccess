<template>
  <Head title="Nueva Persona" />
  
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Nueva Persona</h1>
            <p class="mt-2 text-gray-600">Complete la información para registrar una nueva persona</p>
          </div>
          <Link 
            :href="route('personas.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Listado
          </Link>
        </div>
      </div>

      <!-- Formulario Principal -->
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Sección 1: Datos Personales -->
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
          <div class="flex items-center mb-6">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-[#39A900] rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h2 class="text-xl font-semibold text-gray-900">Información Personal</h2>
              <p class="text-sm text-gray-600">Datos básicos de identificación</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Documento -->
            <div>
              <label for="documento" class="block text-sm font-medium text-gray-700 mb-2">
                Documento de Identidad
              </label>
              <input
                id="documento"
                v-model="form.documento"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent transition-all"
                :class="{ 'border-red-500 ring-red-500': form.errors.documento }"
                placeholder="Ej: 12345678"
              >
              <div v-if="form.errors.documento" class="mt-1 text-sm text-red-600">
                {{ form.errors.documento }}
              </div>
            </div>

            <!-- Nombre -->
            <div>
              <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                Nombre Completo *
              </label>
              <input
                id="nombre"
                v-model="form.nombre"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent transition-all"
                :class="{ 'border-red-500 ring-red-500': form.errors.nombre }"
                placeholder="Ej: Juan Pérez García"
              >
              <div v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">
                {{ form.errors.nombre }}
              </div>
            </div>

            <!-- Tipo de Persona -->
            <div>
              <label for="tipoPersona" class="block text-sm font-medium text-gray-700 mb-2">
                Tipo de Persona *
              </label>
              <select
                id="tipoPersona"
                v-model="form.tipoPersona"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent transition-all"
                :class="{ 'border-red-500 ring-red-500': form.errors.tipoPersona }"
              >
                <option value="">Seleccionar tipo</option>
                <option value="Estudiante">Estudiante</option>
                <option value="Docente">Docente</option>
                <option value="Administrativo">Administrativo</option>
                <option value="Visitante">Visitante</option>
                <option value="Contratista">Contratista</option>
              </select>
              <div v-if="form.errors.tipoPersona" class="mt-1 text-sm text-red-600">
                {{ form.errors.tipoPersona }}
              </div>
            </div>

            <!-- Correo -->
            <div>
              <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">
                Correo Electrónico
              </label>
              <input
                id="correo"
                v-model="form.correo"
                type="email"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent transition-all"
                :class="{ 'border-red-500 ring-red-500': form.errors.correo }"
                placeholder="correo@ejemplo.com"
              >
              <div v-if="form.errors.correo" class="mt-1 text-sm text-red-600">
                {{ form.errors.correo }}
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Se enviará un QR por correo si se proporciona
              </p>
            </div>
          </div>
        </div>

        <!-- Sección 2: Portátiles -->
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-[#50E5F9] rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Portátiles</h2>
                <p class="text-sm text-gray-600">Equipos portátiles asociados (opcional)</p>
              </div>
            </div>
            <button
              type="button"
              @click="addPortatil"
              class="inline-flex items-center px-4 py-2 bg-[#50E5F9] text-white rounded-lg hover:bg-[#00B4D8] transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Agregar Portátil
            </button>
          </div>

          <!-- Lista de portátiles -->
          <div v-if="form.portatiles.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay portátiles</h3>
            <p class="mt-1 text-sm text-gray-500">Agregue portátiles haciendo clic en el botón superior</p>
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="(portatil, index) in form.portatiles"
              :key="`portatil-${index}`"
              class="border border-gray-200 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 bg-[#50E5F9] text-white text-xs font-bold rounded-full mr-2">
                    {{ index + 1 }}
                  </span>
                  Portátil {{ index + 1 }}
                </h3>
                <button
                  type="button"
                  @click="removePortatil(index)"
                  class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 bg-red-50 rounded-md hover:bg-red-100 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Eliminar
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Serial -->
                <div>
                  <label :for="`portatil-serial-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                    Serial *
                  </label>
                  <input
                    :id="`portatil-serial-${index}`"
                    v-model="portatil.serial"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#50E5F9] focus:border-transparent transition-all"
                    :class="{ 'border-red-500 ring-red-500': form.errors[`portatiles.${index}.serial`] }"
                    placeholder="Ej: ABC123456"
                  >
                  <div v-if="form.errors[`portatiles.${index}.serial`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`portatiles.${index}.serial`] }}
                  </div>
                </div>

                <!-- Marca -->
                <div>
                  <label :for="`portatil-marca-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                    Marca *
                  </label>
                  <input
                    :id="`portatil-marca-${index}`"
                    v-model="portatil.marca"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#50E5F9] focus:border-transparent transition-all"
                    :class="{ 'border-red-500 ring-red-500': form.errors[`portatiles.${index}.marca`] }"
                    placeholder="Ej: Dell, HP, Lenovo"
                  >
                  <div v-if="form.errors[`portatiles.${index}.marca`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`portatiles.${index}.marca`] }}
                  </div>
                </div>

                <!-- Modelo -->
                <div>
                  <label :for="`portatil-modelo-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                    Modelo *
                  </label>
                  <input
                    :id="`portatil-modelo-${index}`"
                    v-model="portatil.modelo"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#50E5F9] focus:border-transparent transition-all"
                    :class="{ 'border-red-500 ring-red-500': form.errors[`portatiles.${index}.modelo`] }"
                    placeholder="Ej: Inspiron 15, ThinkPad X1"
                  >
                  <div v-if="form.errors[`portatiles.${index}.modelo`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`portatiles.${index}.modelo`] }}
                  </div>
                </div>
              </div>

              <!-- Preview del QR si hay serial -->
              <div v-if="portatil.serial" class="mt-4 p-4 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <img 
                      :src="`https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=${encodeURIComponent(portatil.serial)}`"
                      :alt="`QR para ${portatil.serial}`"
                      class="w-16 h-16 border border-gray-200 rounded"
                    >
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">QR generado automáticamente</p>
                    <p class="text-xs text-gray-500">Serial: {{ portatil.serial }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Errores generales de portátiles -->
          <div v-if="form.errors.portatiles" class="mt-4 text-sm text-red-600">
            {{ form.errors.portatiles }}
          </div>
        </div>

        <!-- Sección 3: Vehículos -->
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-[#FDC300] rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Vehículos</h2>
                <p class="text-sm text-gray-600">Vehículos asociados (opcional)</p>
              </div>
            </div>
            <button
              type="button"
              @click="addVehiculo"
              class="inline-flex items-center px-4 py-2 bg-[#FDC300] text-black rounded-lg hover:bg-[#E6B000] transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Agregar Vehículo
            </button>
          </div>

          <!-- Lista de vehículos -->
          <div v-if="form.vehiculos.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay vehículos</h3>
            <p class="mt-1 text-sm text-gray-500">Agregue vehículos haciendo clic en el botón superior</p>
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="(vehiculo, index) in form.vehiculos"
              :key="`vehiculo-${index}`"
              class="border border-gray-200 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 bg-[#FDC300] text-black text-xs font-bold rounded-full mr-2">
                    {{ index + 1 }}
                  </span>
                  Vehículo {{ index + 1 }}
                </h3>
                <button
                  type="button"
                  @click="removeVehiculo(index)"
                  class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 bg-red-50 rounded-md hover:bg-red-100 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Eliminar
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipo -->
                <div>
                  <label :for="`vehiculo-tipo-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Vehículo *
                  </label>
                  <select
                    :id="`vehiculo-tipo-${index}`"
                    v-model="vehiculo.tipo"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FDC300] focus:border-transparent transition-all"
                    :class="{ 'border-red-500 ring-red-500': form.errors[`vehiculos.${index}.tipo`] }"
                  >
                    <option value="">Seleccionar tipo</option>
                    <option value="Automóvil">🚗 Automóvil</option>
                    <option value="Motocicleta">🏍️ Motocicleta</option>
                    <option value="Bicicleta">🚲 Bicicleta</option>
                    <option value="Camioneta">🚙 Camioneta</option>
                    <option value="Camión">🚛 Camión</option>
                    <option value="Otro">🚐 Otro</option>
                  </select>
                  <div v-if="form.errors[`vehiculos.${index}.tipo`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`vehiculos.${index}.tipo`] }}
                  </div>
                </div>

                <!-- Placa -->
                <div>
                  <label :for="`vehiculo-placa-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                    Placa *
                  </label>
                  <input
                    :id="`vehiculo-placa-${index}`"
                    v-model="vehiculo.placa"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FDC300] focus:border-transparent transition-all uppercase"
                    :class="{ 'border-red-500 ring-red-500': form.errors[`vehiculos.${index}.placa`] }"
                    placeholder="Ej: ABC-123"
                    @input="vehiculo.placa = vehiculo.placa.toUpperCase()"
                  >
                  <div v-if="form.errors[`vehiculos.${index}.placa`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`vehiculos.${index}.placa`] }}
                  </div>
                </div>
              </div>

              <!-- Información adicional del vehículo -->
              <div v-if="vehiculo.tipo && vehiculo.placa" class="mt-4 p-4 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-[#FDC300] rounded-full flex items-center justify-center">
                      <span class="text-lg">
                        {{ getVehicleEmoji(vehiculo.tipo) }}
                      </span>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ vehiculo.tipo }}</p>
                    <p class="text-xs text-gray-500">Placa: {{ vehiculo.placa }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Errores generales de vehículos -->
          <div v-if="form.errors.vehiculos" class="mt-4 text-sm text-red-600">
            {{ form.errors.vehiculos }}
          </div>
        </div>

        <!-- Mensaje de éxito/error -->
        <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
              <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>
          </div>
        </div>

        <div v-if="form.errors.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
              <p class="text-sm text-red-800">{{ form.errors.error }}</p>
            </div>
          </div>
        </div>

        <!-- Botones de acción temporales -->
        <div class="flex justify-end gap-3">
          <Link
            :href="route('personas.index')"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancelar
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-3 bg-[#39A900] text-white rounded-lg hover:bg-[#007832] disabled:opacity-50 transition-colors"
          >
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Crear Persona</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

// Definir el título de la página
defineOptions({
  layout: null
})

// Inicializar formulario
const form = useForm({
  documento: '',
  nombre: '',
  tipoPersona: '',
  correo: '',
  portatiles: [],
  vehiculos: [],
})

// Funciones para manejar portátiles
const addPortatil = () => {
  form.portatiles.push({
    serial: '',
    marca: '',
    modelo: ''
  })
}

const removePortatil = (index) => {
  form.portatiles.splice(index, 1)
}

// Funciones para manejar vehículos
const addVehiculo = () => {
  form.vehiculos.push({
    tipo: '',
    placa: ''
  })
}

const removeVehiculo = (index) => {
  form.vehiculos.splice(index, 1)
}

// Función para obtener emoji del vehículo
const getVehicleEmoji = (tipo) => {
  const emojis = {
    'Automóvil': '🚗',
    'Motocicleta': '🏍️',
    'Bicicleta': '🚲',
    'Camioneta': '🚙',
    'Camión': '🚛',
    'Otro': '🚐'
  }
  return emojis[tipo] || '🚐'
}

// Función para enviar el formulario
const submit = () => {
  form.post(route('personas.store'), {
    onSuccess: () => {
      // Redirigir al índice después del éxito
    },
    onError: (errors) => {
      // Si hay error de token CSRF, recargar la página
      if (errors.message && (errors.message.includes('CSRF') || errors.message.includes('expired'))) {
        window.location.reload();
      }
      console.log('Errores de validación:', errors)
    },
    preserveScroll: true,
    preserveState: false,
  })
}
</script>