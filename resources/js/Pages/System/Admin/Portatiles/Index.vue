<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import Modal from '@/Components/Modal.vue'
import Icon from '@/Components/Icon.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, reactive, computed } from 'vue'
import axios from 'axios'

// Portatiles management state
const portatiles = ref({ data: [], links: [], total: 0, from: 0, to: 0 })
const personas = ref([])
const showModal = ref(false)
const isEditing = ref(false)
const editingPortatilId = ref(null)
const loading = ref(false)

const searchForm = reactive({
  search: '',
  per_page: 15,
})

// Form
const form = useForm({
  persona_id: '',
  serial: '',
  qrCode: '',
  marca: '',
  modelo: '',
})

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

// Load portatiles data
const loadPortatiles = async () => {
  loading.value = true
  try {
    const response = await axios.get('/system/admin/portatiles/data', {
      params: searchForm
    })
    portatiles.value = response.data.portatiles
  } catch (error) {
    console.error('Error loading portatiles:', error)
  } finally {
    loading.value = false
  }
}

// Load personas for select
const loadPersonas = async () => {
  try {
    const response = await axios.get('/system/admin/portatiles/personas')
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas:', error)
  }
}

// Search with debounce
const search = debounce(() => {
  loadPortatiles()
}, 300)

// Open create modal
const openCreateModal = () => {
  isEditing.value = false
  editingPortatilId.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

// Open edit modal
const openEditModal = (portatil) => {
  isEditing.value = true
  editingPortatilId.value = portatil.portatil_id
  form.persona_id = portatil.persona_id
  form.serial = portatil.serial
  form.qrCode = portatil.qrCode || ''
  form.marca = portatil.marca
  form.modelo = portatil.modelo
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
  return form.persona_id && form.serial && form.marca && form.modelo
})

// Submit form
const submit = () => {
  if (isEditing.value) {
    form.put(route('system.admin.portatiles.update', editingPortatilId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadPortatiles()
      },
    })
  } else {
    form.post(route('system.admin.portatiles.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadPortatiles()
      },
    })
  }
}

// Delete portatil
const confirmDelete = (portatil) => {
  if (confirm(`¿Estás seguro de eliminar el portátil ${portatil.serial}?`)) {
    router.delete(route('system.admin.portatiles.destroy', portatil.portatil_id), {
      preserveScroll: true,
      onSuccess: () => loadPortatiles(),
    })
  }
}

// Load portatiles from pagination link
const loadPortatilesPage = async (url) => {
  if (!url) return
  loading.value = true
  try {
    const response = await axios.get(url)
    portatiles.value = response.data.portatiles
  } catch (error) {
    console.error('Error loading portatiles page:', error)
  } finally {
    loading.value = false
  }
}

// Get persona name by id
const getPersonaName = (personaId) => {
  const persona = personas.value.find(p => p.id === personaId)
  return persona ? `${persona.nombre} (${persona.documento})` : '—'
}

// Load data on component mount
onMounted(() => {
  loadPersonas()
  loadPortatiles()
})
</script>

<template>
  <SystemLayout>
    <Head title="Gestión de Portátiles" />

    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-600">
            <Icon name="laptop" class="w-4 h-4 text-white" />
          </div>
          <h2 class="text-lg sm:text-xl font-bold text-theme-primary">Gestión de Portátiles</h2>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-3 py-2 bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 text-white rounded-lg transition-colors text-sm touch-manipulation"
        >
          <Icon name="plus" class="w-4 h-4" />
          <span class="hidden sm:inline">Nuevo Portátil</span>
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
              placeholder="Buscar serial, marca, modelo..."
              class="w-full px-3 py-2 text-sm border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-cyan-500 focus:border-transparent touch-manipulation"
            >
          </div>
          <select
            v-model="searchForm.per_page"
            @change="search"
            class="px-3 py-2 text-sm border border-theme-primary rounded-lg bg-theme-card text-theme-primary focus:ring-2 focus:ring-cyan-500 touch-manipulation"
          >
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- Tabla de portátiles -->
      <div class="bg-theme-card rounded-xl border border-theme-primary shadow-theme-sm overflow-hidden">
        <div class="border-b border-theme-primary bg-theme-secondary px-3 py-3">
          <h3 class="text-base font-semibold text-theme-primary">
            Portátiles
            <span v-if="portatiles.total" class="text-sm font-normal text-theme-secondary ml-1">
              ({{ portatiles.total }})
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
          <div v-for="portatil in portatiles.data" :key="portatil.portatil_id" class="p-3 hover:bg-theme-secondary transition-colors touch-manipulation">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <div class="font-mono font-semibold text-theme-primary mb-1">{{ portatil.serial }}</div>
                <div class="text-sm space-y-0.5">
                  <div class="text-theme-primary">{{ portatil.marca }} {{ portatil.modelo }}</div>
                  <div v-if="portatil.qrCode" class="text-xs text-theme-secondary">QR: {{ portatil.qrCode }}</div>
                  <div v-if="portatil.persona" class="text-theme-primary">
                    <div>{{ portatil.persona.Nombre }}</div>
                    <div class="text-xs text-theme-secondary">{{ portatil.persona.documento }}</div>
                  </div>
                  <div v-else class="text-theme-muted text-xs">Sin persona</div>
                </div>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <button
                  @click="openEditModal(portatil)"
                  class="p-2 bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 text-white rounded transition-colors touch-manipulation"
                  title="Editar"
                >
                  <Icon name="pencil" class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="confirmDelete(portatil)"
                  class="p-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded transition-colors touch-manipulation"
                  title="Eliminar"
                >
                  <Icon name="trash" class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
          <div v-if="!portatiles.data?.length" class="px-3 py-8 text-center text-theme-muted">
            <div class="flex flex-col items-center gap-2">
              <Icon name="laptop" class="w-8 h-8 text-theme-muted" />
              <span class="text-sm">No se encontraron portátiles</span>
            </div>
          </div>
        </div>

        <!-- Vista desktop (tabla) -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="min-w-full divide-y divide-theme-primary text-sm">
            <thead class="bg-theme-secondary">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Serial</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">QR</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Marca</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Modelo</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Persona</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-theme-primary bg-theme-card">
              <tr v-for="portatil in portatiles.data" :key="portatil.portatil_id" class="transition-colors hover:bg-theme-secondary">
                <td class="px-3 py-3 font-mono text-theme-primary">{{ portatil.serial }}</td>
                <td class="px-3 py-3 text-theme-secondary text-xs">{{ portatil.qrCode || '—' }}</td>
                <td class="px-3 py-3 text-theme-primary">{{ portatil.marca }}</td>
                <td class="px-3 py-3 text-theme-secondary">{{ portatil.modelo }}</td>
                <td class="px-3 py-3">
                  <div v-if="portatil.persona">
                    <div class="font-medium text-theme-primary">{{ portatil.persona.Nombre }}</div>
                    <div class="text-xs text-theme-secondary">{{ portatil.persona.documento }}</div>
                  </div>
                  <span v-else class="text-theme-muted">—</span>
                </td>
                <td class="px-3 py-3">
                  <div class="flex items-center gap-1">
                    <button
                      @click="openEditModal(portatil)"
                      class="px-2 py-1 text-xs bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 text-white rounded transition-colors touch-manipulation"
                      title="Editar"
                    >
                      <Icon name="pencil" class="w-3 h-3" />
                    </button>
                    <button
                      @click="confirmDelete(portatil)"
                      class="px-2 py-1 text-xs bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded transition-colors touch-manipulation"
                      title="Eliminar"
                    >
                      <Icon name="trash" class="w-3 h-3" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!portatiles.data?.length">
                <td colspan="6" class="px-3 py-8 text-center text-theme-muted">
                  <div class="flex flex-col items-center gap-2">
                    <Icon name="laptop" class="w-8 h-8 text-theme-muted" />
                    <span class="text-sm">No se encontraron portátiles</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="portatiles.data?.length" class="border-t border-theme-primary bg-theme-secondary px-3 py-3">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-theme-secondary">
              {{ portatiles.from }}-{{ portatiles.to }} de {{ portatiles.total }}
            </div>
            <div class="flex gap-1">
              <button
                v-for="(link, index) in portatiles.links"
                :key="index"
                @click="loadPortatilesPage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-2 py-1 text-xs border rounded transition-colors touch-manipulation h-8 min-w-[2rem]',
                  link.active
                    ? 'bg-cyan-600 text-white border-cyan-600 font-semibold'
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

    <!-- Modal para crear/editar portátil -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-sage-800 dark:text-sage-100">
            <Icon :name="isEditing ? 'pencil' : 'plus'" class="w-5 h-5 inline mr-2" />
            {{ isEditing ? 'Editar Portátil' : 'Nuevo Portátil' }}
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
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.persona_id }"
            >
              <option value="">Seleccionar persona</option>
              <option v-for="persona in personas" :key="persona.id" :value="persona.id">
                {{ persona.nombre }} - {{ persona.documento }} ({{ persona.tipo_persona }})
              </option>
            </select>
            <p v-if="form.errors.persona_id" class="mt-1 text-xs text-red-500">{{ form.errors.persona_id }}</p>
          </div>

          <!-- Serial y QR Code -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="hash" class="w-3 h-3 inline mr-1" />
                Serial *
              </label>
              <input
                v-model="form.serial"
                type="text"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.serial }"
              />
              <p v-if="form.errors.serial" class="mt-1 text-xs text-red-500">{{ form.errors.serial }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="qr-code" class="w-3 h-3 inline mr-1" />
                QR Code
              </label>
              <input
                v-model="form.qrCode"
                type="text"
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.qrCode }"
              />
              <p v-if="form.errors.qrCode" class="mt-1 text-xs text-red-500">{{ form.errors.qrCode }}</p>
            </div>
          </div>

          <!-- Marca y Modelo -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="laptop" class="w-3 h-3 inline mr-1" />
                Marca *
              </label>
              <input
                v-model="form.marca"
                type="text"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.marca }"
              />
              <p v-if="form.errors.marca" class="mt-1 text-xs text-red-500">{{ form.errors.marca }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="tag" class="w-3 h-3 inline mr-1" />
                Modelo *
              </label>
              <input
                v-model="form.modelo"
                type="text"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.modelo }"
              />
              <p v-if="form.errors.modelo" class="mt-1 text-xs text-red-500">{{ form.errors.modelo }}</p>
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
              class="px-4 py-1.5 text-sm bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
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
