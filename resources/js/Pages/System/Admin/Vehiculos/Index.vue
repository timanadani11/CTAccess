<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import Modal from '@/Components/Modal.vue'
import Icon from '@/Components/Icon.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, reactive, computed } from 'vue'
import axios from 'axios'

// Vehiculos management state
const vehiculos = ref({ data: [], links: [], total: 0, from: 0, to: 0 })
const personas = ref([])
const showModal = ref(false)
const isEditing = ref(false)
const editingVehiculoId = ref(null)
const loading = ref(false)

const searchForm = reactive({
  search: '',
  per_page: 15,
})

// Form
const form = useForm({
  persona_id: '',
  tipo: '',
  placa: '',
})

const tiposVehiculo = [
  { value: 'Automóvil', label: 'Automóvil' },
  { value: 'Motocicleta', label: 'Motocicleta' },
  { value: 'Camioneta', label: 'Camioneta' },
  { value: 'Bicicleta', label: 'Bicicleta' },
  { value: 'Otro', label: 'Otro' },
]

// Debounce function
const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Load vehiculos data
const loadVehiculos = async () => {
  loading.value = true
  try {
    const response = await axios.get('/system/admin/vehiculos/data', {
      params: searchForm
    })
    vehiculos.value = response.data.vehiculos
  } catch (error) {
    console.error('Error loading vehiculos:', error)
  } finally {
    loading.value = false
  }
}

// Load personas for select
const loadPersonas = async () => {
  try {
    const response = await axios.get('/system/admin/vehiculos/personas')
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas:', error)
  }
}

// Search with debounce
const search = debounce(() => {
  loadVehiculos()
}, 300)

// Open create modal
const openCreateModal = () => {
  isEditing.value = false
  editingVehiculoId.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

// Open edit modal
const openEditModal = (vehiculo) => {
  isEditing.value = true
  editingVehiculoId.value = vehiculo.id
  form.persona_id = vehiculo.persona_id
  form.tipo = vehiculo.tipo
  form.placa = vehiculo.placa
  form.clearErrors()
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

// Can save
const canSave = computed(() => {
  return form.persona_id && form.tipo && form.placa
})

// Submit form
const submit = () => {
  if (isEditing.value) {
    form.put(route('system.admin.vehiculos.update', editingVehiculoId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadVehiculos()
      },
    })
  } else {
    form.post(route('system.admin.vehiculos.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadVehiculos()
      },
    })
  }
}

// Delete vehiculo
const confirmDelete = (vehiculo) => {
  if (confirm(`¿Estás seguro de eliminar el vehículo con placa ${vehiculo.placa}?`)) {
    router.delete(route('system.admin.vehiculos.destroy', vehiculo.id), {
      preserveScroll: true,
      onSuccess: () => loadVehiculos(),
    })
  }
}

// Load vehiculos from pagination link
const loadVehiculosPage = async (url) => {
  if (!url) return
  loading.value = true
  try {
    const response = await axios.get(url)
    vehiculos.value = response.data.vehiculos
  } catch (error) {
    console.error('Error loading vehiculos page:', error)
  } finally {
    loading.value = false
  }
}

// Load data on component mount
onMounted(() => {
  loadPersonas()
  loadVehiculos()
})
</script>

<template>
  <SystemLayout>
    <Head title="Gestión de Vehículos" />

    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sena-yellow-600">
            <Icon name="car" class="w-4 h-4 text-gray-900" />
          </div>
          <h2 class="text-lg sm:text-xl font-bold text-theme-primary">Gestión de Vehículos</h2>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-3 py-2 bg-sena-yellow-600 hover:bg-sena-yellow-700 active:bg-sena-yellow-800 text-gray-900 rounded-lg transition-colors font-semibold text-sm touch-manipulation"
        >
          <Icon name="plus" class="w-4 h-4" />
          <span class="hidden sm:inline">Nuevo Vehículo</span>
          <span class="sm:hidden">Nuevo</span>
        </button>
      </div>
    </template>

    <div class="space-y-3 sm:space-y-4">
      <!-- Filtros -->
      <div class="bg-theme-card rounded-xl border border-theme-primary p-3 shadow-theme-sm">
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="flex-1">
            <input
              v-model="searchForm.search"
              @input="search"
              type="search"
              inputmode="search"
              placeholder="Buscar placa, tipo o persona..."
              class="w-full px-3 py-2 text-sm border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-sena-yellow-500 focus:border-transparent touch-manipulation"
            >
          </div>
          <select
            v-model="searchForm.per_page"
            @change="search"
            class="px-3 py-2 text-sm border border-theme-primary rounded-lg bg-theme-card text-theme-primary focus:ring-2 focus:ring-sena-yellow-500 touch-manipulation"
          >
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- Tabla de vehículos -->
      <div class="bg-theme-card rounded-xl border border-theme-primary shadow-theme-sm overflow-hidden">
        <div class="border-b border-theme-primary bg-theme-secondary px-3 py-3">
          <h3 class="text-base font-semibold text-theme-primary">
            Vehículos
            <span v-if="vehiculos.total" class="text-sm font-normal text-theme-secondary ml-1">
              ({{ vehiculos.total }})
            </span>
          </h3>
        </div>

        <!-- Loading indicator -->
        <div v-if="loading" class="text-center py-8">
          <div class="inline-flex items-center gap-2 text-theme-secondary text-sm">
            <Icon name="loader" class="w-4 h-4 animate-spin" />
            Cargando...
          </div>
        </div>

        <!-- Vista móvil (cards) -->
        <div v-else class="lg:hidden divide-y divide-theme-primary">
          <div v-for="vehiculo in vehiculos.data" :key="vehiculo.id" class="p-3 hover:bg-theme-secondary transition-colors touch-manipulation">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span class="font-mono font-semibold text-theme-primary">{{ vehiculo.placa }}</span>
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-sena-yellow-100 dark:bg-sena-yellow-800 text-sena-yellow-700 dark:text-sena-yellow-300">
                    {{ vehiculo.tipo }}
                  </span>
                </div>
                <div v-if="vehiculo.persona" class="text-sm">
                  <div class="text-theme-primary">{{ vehiculo.persona.Nombre }}</div>
                  <div class="text-xs text-theme-secondary">{{ vehiculo.persona.documento }}</div>
                </div>
                <div v-else class="text-sm text-theme-muted">Sin persona</div>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <button
                  @click="openEditModal(vehiculo)"
                  class="p-2 bg-sena-yellow-600 hover:bg-sena-yellow-700 active:bg-sena-yellow-800 text-gray-900 rounded transition-colors touch-manipulation"
                  title="Editar"
                >
                  <Icon name="pencil" class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="confirmDelete(vehiculo)"
                  class="p-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded transition-colors touch-manipulation"
                  title="Eliminar"
                >
                  <Icon name="trash" class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
          <div v-if="!vehiculos.data?.length" class="px-3 py-8 text-center text-theme-muted">
            <div class="flex flex-col items-center gap-2">
              <Icon name="car" class="w-8 h-8 text-theme-muted" />
              <span class="text-sm">No se encontraron vehículos</span>
            </div>
          </div>
        </div>

        <!-- Vista desktop (tabla) -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="min-w-full divide-y divide-theme-primary text-sm">
            <thead class="bg-theme-secondary">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Placa</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Tipo</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Persona</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-theme-primary bg-theme-card">
              <tr v-for="vehiculo in vehiculos.data" :key="vehiculo.id" class="transition-colors hover:bg-theme-secondary">
                <td class="px-3 py-3 font-mono text-theme-primary font-semibold">{{ vehiculo.placa }}</td>
                <td class="px-3 py-3">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-sena-yellow-100 dark:bg-sena-yellow-800 text-sena-yellow-700 dark:text-sena-yellow-300">
                    {{ vehiculo.tipo }}
                  </span>
                </td>
                <td class="px-3 py-3">
                  <div v-if="vehiculo.persona">
                    <div class="font-medium text-theme-primary">{{ vehiculo.persona.Nombre }}</div>
                    <div class="text-xs text-theme-secondary">{{ vehiculo.persona.documento }}</div>
                  </div>
                  <span v-else class="text-theme-muted">—</span>
                </td>
                <td class="px-3 py-3">
                  <div class="flex items-center gap-1">
                    <button
                      @click="openEditModal(vehiculo)"
                      class="px-2 py-1 text-xs bg-sena-yellow-600 hover:bg-sena-yellow-700 active:bg-sena-yellow-800 text-gray-900 rounded transition-colors font-semibold touch-manipulation"
                      title="Editar"
                    >
                      <Icon name="pencil" class="w-3 h-3" />
                    </button>
                    <button
                      @click="confirmDelete(vehiculo)"
                      class="px-2 py-1 text-xs bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded transition-colors touch-manipulation"
                      title="Eliminar"
                    >
                      <Icon name="trash" class="w-3 h-3" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!vehiculos.data?.length">
                <td colspan="4" class="px-3 py-8 text-center text-theme-muted">
                  <div class="flex flex-col items-center gap-2">
                    <Icon name="car" class="w-8 h-8 text-theme-muted" />
                    <span class="text-sm">No se encontraron vehículos</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="vehiculos.data?.length" class="border-t border-theme-primary bg-theme-secondary px-3 py-3">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-theme-secondary">
              {{ vehiculos.from }}-{{ vehiculos.to }} de {{ vehiculos.total }}
            </div>
            <div class="flex gap-1">
              <button
                v-for="(link, index) in vehiculos.links"
                :key="index"
                @click="loadVehiculosPage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-2 py-1 text-xs border rounded transition-colors touch-manipulation h-8 min-w-[2rem]',
                  link.active
                    ? 'bg-sena-yellow-600 text-gray-900 border-sena-yellow-600 font-semibold'
                    : link.url
                      ? 'bg-theme-card text-theme-secondary border-theme-primary hover:bg-theme-secondary active:bg-theme-tertiary'
                      : 'bg-theme-tertiary text-theme-muted border-theme-primary cursor-not-allowed opacity-50'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para crear/editar vehículo -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-sage-800 dark:text-sage-100">
            <Icon :name="isEditing ? 'pencil' : 'plus'" class="w-5 h-5 inline mr-2" />
            {{ isEditing ? 'Editar Vehículo' : 'Nuevo Vehículo' }}
          </h3>
          <button @click="closeModal" class="text-sage-400 hover:text-sage-600 dark:hover:text-sage-300">
            <Icon name="x" class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submit" class="space-y-3">
          <!-- Persona -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="user" class="w-3 h-3 inline mr-1" />
              Persona *
            </label>
            <select
              v-model="form.persona_id"
              required
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-yellow-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.persona_id }"
            >
              <option value="">Seleccionar persona</option>
              <option v-for="persona in personas" :key="persona.id" :value="persona.id">
                {{ persona.nombre }} - {{ persona.documento }} ({{ persona.tipo_persona }})
              </option>
            </select>
            <p v-if="form.errors.persona_id" class="mt-1 text-xs text-red-500">{{ form.errors.persona_id }}</p>
          </div>

          <!-- Tipo y Placa -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="car" class="w-3 h-3 inline mr-1" />
                Tipo *
              </label>
              <select
                v-model="form.tipo"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-yellow-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.tipo }"
              >
                <option value="">Seleccionar tipo</option>
                <option v-for="tipo in tiposVehiculo" :key="tipo.value" :value="tipo.value">
                  {{ tipo.label }}
                </option>
              </select>
              <p v-if="form.errors.tipo" class="mt-1 text-xs text-red-500">{{ form.errors.tipo }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="hash" class="w-3 h-3 inline mr-1" />
                Placa *
              </label>
              <input
                v-model="form.placa"
                type="text"
                required
                placeholder="ABC-123"
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-yellow-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 uppercase"
                :class="{ 'border-red-500': form.errors.placa }"
              />
              <p v-if="form.errors.placa" class="mt-1 text-xs text-red-500">{{ form.errors.placa }}</p>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-2 pt-3 border-t border-sage-200 dark:border-sage-700">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-1.5 text-sm border border-sage-300 dark:border-sage-600 text-sage-700 dark:text-sage-300 rounded-lg hover:bg-sage-50 dark:hover:bg-sage-700"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="!canSave || form.processing"
              class="px-4 py-1.5 text-sm bg-sena-yellow-600 hover:bg-sena-yellow-700 text-gray-900 font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <Icon name="save" class="w-3.5 h-3.5" />
              {{ form.processing ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Guardar') }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </SystemLayout>
</template>
