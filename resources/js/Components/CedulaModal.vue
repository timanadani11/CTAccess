<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleClose"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4">
          <div
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-white">Entrada Manual</h3>
                    <p class="text-xs text-emerald-50">Ingresa el número de cédula</p>
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
              <form @submit.prevent="handleSubmit">
                <!-- Campo de Cédula -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">
                    <div class="flex items-center space-x-2">
                      <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                      </svg>
                      <span>Número de Cédula</span>
                    </div>
                  </label>
                  
                  <div class="relative">
                    <input
                      ref="cedulaInput"
                      v-model="cedula"
                      type="text"
                      inputmode="numeric"
                      pattern="[0-9]*"
                      placeholder="Ej: 123456789"
                      class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-lg font-medium text-gray-900 placeholder-gray-400 transition-all focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                      :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500/20': error }"
                      @input="clearError"
                    />
                    <div
                      v-if="cedula"
                      class="absolute right-3 top-1/2 -translate-y-1/2"
                    >
                      <button
                        type="button"
                        @click="clearInput"
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-gray-600 transition-all hover:bg-gray-300 active:scale-95"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Error message -->
                  <Transition name="error">
                    <p v-if="error" class="text-sm text-red-600 flex items-center space-x-1">
                      <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                      <span>{{ error }}</span>
                    </p>
                  </Transition>

                  <!-- Hint -->
                  <p class="text-xs text-gray-500 flex items-start space-x-1">
                    <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Ingresa solo números, sin espacios ni caracteres especiales</span>
                  </p>
                </div>

                <!-- Keyboard Shortcuts Hint (solo desktop) -->
                <div class="mt-4 hidden sm:flex items-center justify-center space-x-4 text-xs text-gray-500">
                  <div class="flex items-center space-x-1">
                    <kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs font-mono">Enter</kbd>
                    <span>Buscar</span>
                  </div>
                  <div class="flex items-center space-x-1">
                    <kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs font-mono">Esc</kbd>
                    <span>Cerrar</span>
                  </div>
                </div>

                <!-- Buttons -->
                <div class="mt-6 flex space-x-3">
                  <button
                    type="button"
                    @click="handleClose"
                    :disabled="processing"
                    class="flex-1 rounded-lg border-2 border-gray-300 bg-white px-4 py-3 font-medium text-gray-700 transition-all hover:bg-gray-50 active:scale-95 disabled:opacity-50"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    :disabled="!cedula.trim() || processing"
                    class="flex-1 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 py-3 font-bold text-white shadow-lg transition-all hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none"
                  >
                    <span v-if="processing" class="flex items-center justify-center space-x-2">
                      <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>Buscando...</span>
                    </span>
                    <span v-else class="flex items-center justify-center space-x-2">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                      <span>Buscar Persona</span>
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'submit'])

const cedula = ref('')
const error = ref('')
const processing = ref(false)
const cedulaInput = ref(null)

// Focus en el input cuando se abre el modal
watch(() => props.show, (newValue) => {
  if (newValue) {
    nextTick(() => {
      if (cedulaInput.value) {
        cedulaInput.value.focus()
      }
    })
  } else {
    // Limpiar cuando se cierra
    cedula.value = ''
    error.value = ''
    processing.value = false
  }
})

const handleClose = () => {
  if (!processing.value) {
    emit('close')
  }
}

const clearInput = () => {
  cedula.value = ''
  error.value = ''
  if (cedulaInput.value) {
    cedulaInput.value.focus()
  }
}

const clearError = () => {
  error.value = ''
}

const handleSubmit = async () => {
  const trimmedCedula = cedula.value.trim()
  
  if (!trimmedCedula) {
    error.value = 'Por favor ingresa un número de cédula'
    return
  }

  if (trimmedCedula.length < 5) {
    error.value = 'La cédula debe tener al menos 5 caracteres'
    return
  }

  if (trimmedCedula.length > 20) {
    error.value = 'La cédula no puede tener más de 20 caracteres'
    return
  }

  // Validar que solo contenga números
  if (!/^\d+$/.test(trimmedCedula)) {
    error.value = 'La cédula solo debe contener números'
    return
  }

  processing.value = true
  error.value = ''

  try {
    emit('submit', trimmedCedula)
    // El componente padre manejará el cierre si es exitoso
  } catch (err) {
    error.value = err.message || 'Error al procesar la cédula'
    processing.value = false
  }
}

// Cerrar con tecla Escape
const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show && !processing.value) {
    handleClose()
  }
}

// Agregar listener global
if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleKeydown)
}

// Exponer métodos
defineExpose({
  setProcessing: (value) => {
    processing.value = value
  },
  setError: (message) => {
    error.value = message
    processing.value = false
  },
  close: () => {
    emit('close')
  }
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

/* Error message transition */
.error-enter-active,
.error-leave-active {
  transition: all 0.2s ease;
}

.error-enter-from,
.error-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* Input animations */
input:focus {
  animation: input-focus 0.3s ease;
}

@keyframes input-focus {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
  100% {
    transform: scale(1);
  }
}

/* PWA optimizations */
@media (max-width: 640px) {
  /* Aumentar tamaño de toque en móviles */
  button {
    min-height: 44px;
  }
  
  input {
    font-size: 16px; /* Prevenir zoom en iOS */
  }
}

/* Prevenir selección de texto durante interacciones táctiles */
@media (hover: none) and (pointer: coarse) {
  * {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
  }
}
</style>
