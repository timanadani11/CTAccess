<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Icon from '@/Components/Icon.vue'
import Modal from '@/Components/Modal.vue'
import axios from 'axios'

const props = defineProps({
  accesos: Object,
  filters: Object,
  estadisticas: Object,
  personas: Array,
})

const q = ref(props.filters?.q ?? '')
const estado = ref(props.filters?.estado ?? '')

watch([q, estado], ([newQ, newEstado]) => {
  router.get(route('system.celador.accesos.index'), 
    { q: newQ, estado: newEstado }, 
    { preserveState: true, replace: true }
  )
}, { debounce: 300 })

const formatDate = (dateString) => {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const calcularDuracion = (entrada, salida) => {
  if (!entrada) return '—'
  const start = new Date(entrada)
  const end = salida ? new Date(salida) : new Date()
  const diff = Math.floor((end - start) / 1000 / 60) // minutos
  
  if (diff < 60) return `${diff}m`
  const hours = Math.floor(diff / 60)
  const mins = diff % 60
  return `${hours}h ${mins}m`
}

// Modal de nuevo acceso
const showModal = ref(false)
const personas = ref([])
const portatiles = ref([])
const vehiculos = ref([])
const loadingPortatiles = ref(false)
const loadingVehiculos = ref(false)

const form = useForm({
  persona_id: '',
  portatil_id: '',
  vehiculo_id: '',
  fecha_entrada: new Date().toISOString().slice(0, 16),
})

const openCreateModal = async () => {
  await loadPersonas()
  form.reset()
  form.persona_id = ''
  form.portatil_id = ''
  form.vehiculo_id = ''
  form.fecha_entrada = new Date().toISOString().slice(0, 16)
  portatiles.value = []
  vehiculos.value = []
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  portatiles.value = []
  vehiculos.value = []
}

const loadPersonas = async () => {
  try {
    const response = await axios.get('/system/admin/portatiles/personas')
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas:', error)
  }
}

const loadPortatilesPersona = async (personaId) => {
  if (!personaId) {
    portatiles.value = []
    return
  }
  loadingPortatiles.value = true
  try {
    const response = await axios.get(`/system/celador/accesos/portatiles/${personaId}`)
    portatiles.value = response.data.portatiles || []
  } catch (error) {
    console.error('Error loading portátiles:', error)
    portatiles.value = []
  } finally {
    loadingPortatiles.value = false
  }
}

const loadVehiculosPersona = async (personaId) => {
  if (!personaId) {
    vehiculos.value = []
    return
  }
  loadingVehiculos.value = true
  try {
    const response = await axios.get(`/system/celador/accesos/vehiculos/${personaId}`)
    vehiculos.value = response.data.vehiculos || []
  } catch (error) {
    console.error('Error loading vehículos:', error)
    vehiculos.value = []
  } finally {
    loadingVehiculos.value = false
  }
}

watch(() => form.persona_id, (newPersonaId) => {
  if (newPersonaId) {
    loadPortatilesPersona(newPersonaId)
    loadVehiculosPersona(newPersonaId)
  } else {
    portatiles.value = []
    vehiculos.value = []
  }
  form.portatil_id = ''
  form.vehiculo_id = ''
})

const submit = () => {
  form.post(route('system.celador.accesos.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      router.reload({ only: ['accesos', 'estadisticas'] })
    },
  })
}
</script>

<template>
  <SystemLayout>
    <Head title="Accesos" />

    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Icon name="log-in" :size="20" class="text-[#39A900]" />
          <h2 class="text-lg sm:text-xl font-semibold text-theme-primary">Accesos</h2>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-3 py-2 bg-[#39A900] hover:bg-[#2d7f00] active:bg-[#236600] text-white rounded-lg transition-colors text-sm font-medium touch-manipulation shadow-sm"
        >
          <Icon name="plus" :size="16" />
          <span class="hidden sm:inline">Nuevo Acceso</span>
          <span class="sm:hidden">Nuevo</span>
        </button>
      </div>
    </template>

    <div class="py-3 px-3 sm:px-4 lg:px-6">
      <div class="mx-auto max-w-7xl space-y-3 sm:space-y-4">
        
        <!-- Estadísticas Compactas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
          <div class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-theme-secondary">Total</p>
                <p class="text-xl sm:text-2xl font-bold text-theme-primary mt-0.5">{{ estadisticas?.total ?? 0 }}</p>
              </div>
              <div class="p-2 bg-[#39A900]/10 rounded-lg">
                <Icon name="users" :size="18" class="text-[#39A900]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-theme-secondary">Activos</p>
                <p class="text-xl sm:text-2xl font-bold text-[#39A900] mt-0.5">{{ estadisticas?.activos ?? 0 }}</p>
              </div>
              <div class="p-2 bg-[#39A900]/10 rounded-lg">
                <Icon name="check-circle" :size="18" class="text-[#39A900]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-theme-secondary">Hoy</p>
                <p class="text-xl sm:text-2xl font-bold text-[#50E5F9] mt-0.5">{{ estadisticas?.hoy ?? 0 }}</p>
              </div>
              <div class="p-2 bg-[#50E5F9]/10 rounded-lg">
                <Icon name="calendar" :size="18" class="text-[#50E5F9]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-theme-secondary">Finalizados</p>
                <p class="text-xl sm:text-2xl font-bold text-theme-secondary mt-0.5">{{ estadisticas?.finalizados ?? 0 }}</p>
              </div>
              <div class="p-2 bg-theme-secondary/10 rounded-lg">
                <Icon name="check" :size="18" class="text-theme-secondary" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros Compactos PWA -->
        <div class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
          <!-- Búsqueda móvil -->
          <div class="mb-2 sm:hidden">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon name="search" :size="16" class="text-theme-muted" />
              </div>
              <input 
                v-model="q" 
                type="search"
                inputmode="search"
                placeholder="Buscar acceso..." 
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary pl-10 pr-3 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all touch-manipulation"
              />
            </div>
          </div>

          <!-- Filtros en línea -->
          <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end">
            <!-- Búsqueda escritorio -->
            <div class="hidden sm:block flex-1 min-w-0">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="search" :size="14" class="text-theme-muted" />
                </div>
                <input 
                  v-model="q" 
                  type="search"
                  inputmode="search"
                  placeholder="Buscar por persona, documento o correo..." 
                  class="w-full rounded-md border-theme-primary bg-theme-primary text-theme-primary pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#39A900] transition-all"
                />
              </div>
            </div>

            <!-- Estado -->
            <div class="flex-1 sm:w-48">
              <select 
                v-model="estado"
                class="w-full rounded-lg sm:rounded-md border-theme-primary bg-theme-primary text-theme-primary px-3 py-2.5 sm:py-1.5 text-base sm:text-sm focus:outline-none focus:ring-2 sm:focus:ring-1 focus:ring-[#39A900] transition-all touch-manipulation"
              >
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="finalizado">Finalizados</option>
              </select>
            </div>

            <!-- Botón limpiar -->
            <button 
              @click="q = ''; estado = ''"
              class="inline-flex items-center justify-center px-4 py-2.5 sm:py-1.5 bg-theme-secondary text-theme-inverse rounded-lg sm:rounded-md hover:bg-theme-secondary/80 active:bg-theme-secondary/90 transition-all gap-2 text-sm font-medium touch-manipulation"
            >
              <Icon name="x" :size="14" />
              <span class="sm:hidden">Limpiar</span>
            </button>
          </div>
        </div>

        <!-- Lista de Accesos - Vista Móvil -->
        <div class="lg:hidden space-y-2">
          <div v-for="a in accesos.data" :key="a.id" 
            class="bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
            <div class="flex items-start gap-3">
              <!-- Avatar -->
              <div class="w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-[#39A900] to-[#50E5F9] flex items-center justify-center text-white font-semibold">
                {{ (a.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
              </div>
              
              <!-- Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-2">
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-theme-primary text-sm truncate">{{ a.persona?.Nombre ?? '—' }}</p>
                    <p class="text-xs text-theme-secondary truncate">{{ a.persona?.numero_documento ?? '—' }}</p>
                  </div>
                  <span v-if="a.estado === 'activo'" 
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-[#39A900]/10 text-[#39A900] whitespace-nowrap">
                    <Icon name="check-circle" :size="12" />
                    Activo
                  </span>
                  <span v-else 
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-theme-secondary/10 text-theme-secondary whitespace-nowrap">
                    <Icon name="x-circle" :size="12" />
                    Fin
                  </span>
                </div>
                
                <!-- Detalles -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div>
                    <p class="text-theme-muted">Entrada</p>
                    <p class="font-medium text-theme-primary">{{ formatDate(a.fecha_entrada).split(',')[1] }}</p>
                  </div>
                  <div>
                    <p class="text-theme-muted">Salida</p>
                    <p class="font-medium text-theme-primary">{{ a.fecha_salida ? formatDate(a.fecha_salida).split(',')[1] : '—' }}</p>
                  </div>
                </div>
                
                <!-- Recursos y duración -->
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-theme-primary">
                  <div class="flex items-center gap-2">
                    <span v-if="a.portatil" class="inline-flex items-center gap-1 text-xs text-[#50E5F9]">
                      <Icon name="laptop" :size="12" />
                    </span>
                    <span v-if="a.vehiculo" class="inline-flex items-center gap-1 text-xs text-[#FDC300]">
                      <Icon name="car" :size="12" />
                    </span>
                  </div>
                  <span class="text-xs text-theme-muted font-mono">{{ calcularDuracion(a.fecha_entrada, a.fecha_salida) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="!accesos.data?.length" class="bg-theme-card rounded-lg border border-theme-primary p-8 text-center">
            <Icon name="inbox" :size="40" class="mx-auto text-theme-muted mb-2" />
            <p class="text-sm text-theme-secondary font-medium">No hay accesos</p>
          </div>
        </div>

        <!-- Tabla de Accesos - Vista Desktop -->
        <div class="hidden lg:block bg-theme-card rounded-lg border border-theme-primary overflow-hidden shadow-theme-sm">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary">
              <thead class="bg-theme-secondary/5">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Persona
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Documento
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Tipo
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Entrada
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Salida
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Duración
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Estado
                  </th>
                  <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Recursos
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary">
                <tr v-for="a in accesos.data" :key="a.id" class="hover:bg-theme-secondary/5 transition-colors">
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#39A900] to-[#50E5F9] flex items-center justify-center text-white font-semibold text-xs">
                        {{ (a.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div class="min-w-0">
                        <p class="font-medium text-theme-primary text-sm truncate">{{ a.persona?.Nombre ?? '—' }}</p>
                        <p class="text-xs text-theme-secondary truncate">{{ a.persona?.correo ?? '' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-3 text-sm text-theme-primary">
                    {{ a.persona?.numero_documento ?? '—' }}
                  </td>
                  <td class="px-3 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#50E5F9]/10 text-[#50E5F9]">
                      {{ a.persona?.tipo_persona ?? '—' }}
                    </span>
                  </td>
                  <td class="px-3 py-3 text-xs">
                    <p class="font-medium text-theme-primary">{{ formatDate(a.fecha_entrada).split(',')[0] }}</p>
                    <p class="text-theme-secondary">{{ formatDate(a.fecha_entrada).split(',')[1] }}</p>
                  </td>
                  <td class="px-3 py-3 text-xs">
                    <div v-if="a.fecha_salida">
                      <p class="font-medium text-theme-primary">{{ formatDate(a.fecha_salida).split(',')[0] }}</p>
                      <p class="text-theme-secondary">{{ formatDate(a.fecha_salida).split(',')[1] }}</p>
                    </div>
                    <span v-else class="text-theme-muted">—</span>
                  </td>
                  <td class="px-3 py-3 text-sm text-theme-secondary">
                    <span class="font-mono">{{ calcularDuracion(a.fecha_entrada, a.fecha_salida) }}</span>
                  </td>
                  <td class="px-3 py-3">
                    <span v-if="a.estado === 'activo'" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-[#39A900]/10 text-[#39A900] border border-[#39A900]/20">
                      <Icon name="check-circle" :size="12" />
                      Activo
                    </span>
                    <span v-else class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-theme-secondary/10 text-theme-secondary border border-theme-secondary/20">
                      <Icon name="x-circle" :size="12" />
                      Fin
                    </span>
                  </td>
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-2">
                      <span v-if="a.portatil" class="inline-flex items-center gap-1 text-xs text-[#50E5F9]">
                        <Icon name="laptop" :size="14" />
                      </span>
                      <span v-if="a.vehiculo" class="inline-flex items-center gap-1 text-xs text-[#FDC300]">
                        <Icon name="car" :size="14" />
                      </span>
                      <span v-if="!a.portatil && !a.vehiculo" class="text-theme-muted">—</span>
                    </div>
                  </td>
                </tr>
                <tr v-if="!accesos.data?.length">
                  <td colspan="8" class="px-3 py-8 text-center">
                    <Icon name="inbox" :size="40" class="mx-auto text-theme-muted mb-2" />
                    <p class="text-sm text-theme-secondary font-medium">No hay registros de accesos</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginación Compacta -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-theme-card rounded-lg border border-theme-primary p-3 shadow-theme-sm">
          <div class="text-xs sm:text-sm text-theme-secondary">
            <span class="font-semibold text-theme-primary">{{ accesos.from ?? 0 }}</span> - 
            <span class="font-semibold text-theme-primary">{{ accesos.to ?? 0 }}</span> de 
            <span class="font-semibold text-theme-primary">{{ accesos.total ?? 0 }}</span>
          </div>
          <div class="flex flex-wrap gap-1 justify-center">
            <Link 
              v-for="link in accesos.links" 
              :key="link.url + link.label" 
              :href="link.url || '#'" 
              class="min-w-[2rem] h-8 flex items-center justify-center rounded-md px-2 text-xs font-medium transition-all touch-manipulation"
              :class="[
                link.active 
                  ? 'bg-[#39A900] text-white shadow-sm' 
                  : 'bg-theme-card border border-theme-primary text-theme-primary hover:bg-theme-secondary/10 active:bg-theme-secondary/20',
                !link.url && 'opacity-50 cursor-not-allowed'
              ]" 
              v-html="link.label" 
              preserve-scroll 
            />
          </div>
        </div>

      </div>
    </div>

    <!-- Modal para crear nuevo acceso -->
    <Modal :show="showModal" @close="closeModal" max-width="2xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-theme-primary flex items-center gap-2">
            <div class="w-10 h-10 rounded-lg bg-[#39A900]/10 flex items-center justify-center">
              <Icon name="plus" :size="20" class="text-[#39A900]" />
            </div>
            Nuevo Acceso
          </h3>
          <button @click="closeModal" class="text-theme-muted hover:text-theme-primary transition-colors">
            <Icon name="x" :size="24" />
          </button>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Persona -->
          <div>
            <label class="block text-sm font-medium text-theme-primary mb-2">
              <span class="flex items-center gap-2">
                <Icon name="user" :size="16" />
                Persona *
              </span>
            </label>
            <select 
              v-model="form.persona_id"
              required
              class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all"
            >
              <option value="">Seleccionar persona...</option>
              <option v-for="persona in personas" :key="persona.id" :value="persona.id">
                {{ persona.nombre }} - {{ persona.documento }} ({{ persona.tipo_persona }})
              </option>
            </select>
            <p v-if="form.errors.persona_id" class="mt-1 text-sm text-red-600">{{ form.errors.persona_id }}</p>
          </div>

          <!-- Portátil (Opcional) -->
          <div>
            <label class="block text-sm font-medium text-theme-primary mb-2">
              <span class="flex items-center gap-2">
                <Icon name="laptop" :size="16" />
                Portátil (opcional)
              </span>
            </label>
            <select 
              v-model="form.portatil_id"
              :disabled="!form.persona_id || loadingPortatiles"
              class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option value="">{{ loadingPortatiles ? 'Cargando...' : 'Sin portátil' }}</option>
              <option v-for="portatil in portatiles" :key="portatil.portatil_id" :value="portatil.portatil_id">
                {{ portatil.marca }} {{ portatil.modelo }} - {{ portatil.serial }}
              </option>
            </select>
            <p v-if="form.persona_id && !loadingPortatiles && portatiles.length === 0" class="mt-1 text-xs text-theme-muted">
              Esta persona no tiene portátiles registrados
            </p>
            <p v-if="form.errors.portatil_id" class="mt-1 text-sm text-red-600">{{ form.errors.portatil_id }}</p>
          </div>

          <!-- Vehículo (Opcional) -->
          <div>
            <label class="block text-sm font-medium text-theme-primary mb-2">
              <span class="flex items-center gap-2">
                <Icon name="car" :size="16" />
                Vehículo (opcional)
              </span>
            </label>
            <select 
              v-model="form.vehiculo_id"
              :disabled="!form.persona_id || loadingVehiculos"
              class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option value="">{{ loadingVehiculos ? 'Cargando...' : 'Sin vehículo' }}</option>
              <option v-for="vehiculo in vehiculos" :key="vehiculo.vehiculo_id" :value="vehiculo.vehiculo_id">
                {{ vehiculo.tipo }} - {{ vehiculo.placa }}
              </option>
            </select>
            <p v-if="form.persona_id && !loadingVehiculos && vehiculos.length === 0" class="mt-1 text-xs text-theme-muted">
              Esta persona no tiene vehículos registrados
            </p>
            <p v-if="form.errors.vehiculo_id" class="mt-1 text-sm text-red-600">{{ form.errors.vehiculo_id }}</p>
          </div>

          <!-- Fecha y hora de entrada -->
          <div>
            <label class="block text-sm font-medium text-theme-primary mb-2">
              <span class="flex items-center gap-2">
                <Icon name="calendar" :size="16" />
                Fecha y hora de entrada *
              </span>
            </label>
            <input 
              v-model="form.fecha_entrada"
              type="datetime-local"
              required
              class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all"
            />
            <p v-if="form.errors.fecha_entrada" class="mt-1 text-sm text-red-600">{{ form.errors.fecha_entrada }}</p>
          </div>

          <!-- Mensaje informativo -->
          <div class="bg-[#50E5F9]/10 border border-[#50E5F9]/20 rounded-lg p-4">
            <div class="flex gap-3">
              <Icon name="info" :size="20" class="text-[#50E5F9] flex-shrink-0 mt-0.5" />
              <div class="text-sm text-theme-primary">
                <p class="font-medium mb-1">Registro de acceso</p>
                <p class="text-theme-secondary">
                  El acceso se creará en estado <strong>activo</strong>. El portátil y vehículo son opcionales, 
                  solo se mostrarán los que pertenecen a la persona seleccionada.
                </p>
              </div>
            </div>
          </div>

          <!-- Botones -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2.5 bg-theme-secondary text-theme-inverse rounded-lg hover:bg-theme-secondary/80 transition-all font-medium text-sm"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 px-4 py-2.5 bg-[#39A900] hover:bg-[#2d7f00] active:bg-[#236600] text-white rounded-lg transition-all font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <Icon v-if="form.processing" name="loader" :size="16" class="animate-spin" />
              <Icon v-else name="save" :size="16" />
              {{ form.processing ? 'Guardando...' : 'Registrar Acceso' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </SystemLayout>
</template>
