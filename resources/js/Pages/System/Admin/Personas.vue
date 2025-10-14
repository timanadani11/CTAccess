<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import Modal from '@/Components/Modal.vue'
import Icon from '@/Components/Icon.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, reactive, computed } from 'vue'
import axios from 'axios'

// People management state
const personas = ref({ data: [], links: [], total: 0, from: 0, to: 0 })
const showModal = ref(false)
const isEditing = ref(false)
const editingPersonaId = ref(null)
const loading = ref(false)

const searchForm = reactive({
  search: '',
  per_page: 15,
})

// Form
const form = useForm({
  Nombre: '',
  TipoDocumento: '',
  documento: '',
  TipoPersona: '',
  Correo: '',
  Telefono: '',
  Direccion: '',
  Empresa: '',
  observaciones: '',
})

const tiposDocumento = [
  { value: 'DNI', label: 'DNI' },
  { value: 'Pasaporte', label: 'Pasaporte' },
  { value: 'Cédula', label: 'Cédula' },
  { value: 'RUC', label: 'RUC' },
  { value: 'Carnet de Extranjería', label: 'Carnet de Extranjería' },
]

const tiposPersona = [
  { value: 'Estudiante', label: 'Estudiante' },
  { value: 'Docente', label: 'Docente' },
  { value: 'Administrativo', label: 'Administrativo' },
  { value: 'Visitante', label: 'Visitante' },
  { value: 'Proveedor', label: 'Proveedor' },
  { value: 'Contratista', label: 'Contratista' },
  { value: 'Aprendiz', label: 'Aprendiz' },
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

// Load personas data
const loadPersonas = async () => {
  loading.value = true
  try {
    const response = await axios.get('/system/admin/personas/data', {
      params: searchForm
    })
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas:', error)
  } finally {
    loading.value = false
  }
}

// Search with debounce
const search = debounce(() => {
  loadPersonas()
}, 300)

// Open create modal
const openCreateModal = () => {
  isEditing.value = false
  editingPersonaId.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

// Open edit modal
const openEditModal = (persona) => {
  isEditing.value = true
  editingPersonaId.value = persona.id
  form.Nombre = persona.nombre
  form.TipoDocumento = persona.tipo_documento
  form.documento = persona.documento
  form.TipoPersona = persona.tipo_persona
  form.Correo = persona.correo || ''
  form.Telefono = persona.telefono || ''
  form.Direccion = persona.direccion || ''
  form.Empresa = persona.empresa || ''
  form.observaciones = persona.observaciones || ''
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
  return form.Nombre && form.TipoDocumento && form.documento && form.TipoPersona
})

// Submit form
const submit = () => {
  if (isEditing.value) {
    form.put(route('system.admin.personas.update', editingPersonaId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadPersonas()
      },
    })
  } else {
    form.post(route('system.admin.personas.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        loadPersonas()
      },
    })
  }
}

// Delete persona
const confirmDelete = (persona) => {
  if (confirm(`¿Estás seguro de eliminar a ${persona.nombre}?`)) {
    router.delete(route('system.admin.personas.destroy', persona.id), {
      preserveScroll: true,
      onSuccess: () => loadPersonas(),
    })
  }
}

// Load personas from pagination link
const loadPersonasPage = async (url) => {
  if (!url) return
  loading.value = true
  try {
    const response = await axios.get(url)
    personas.value = response.data.personas
  } catch (error) {
    console.error('Error loading personas page:', error)
  } finally {
    loading.value = false
  }
}

// Load data on component mount
onMounted(() => {
  loadPersonas()
})
</script>

<template>
  <SystemLayout>
    <Head title="Gestión de Personas" />

    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sena-green-600 dark:bg-cyan-600">
            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-theme-primary">Gestión de Personas</h2>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2 bg-sena-green-600 hover:bg-sena-green-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white rounded-lg transition-colors"
        >
          <Icon name="user-plus" class="w-4 h-4" />
          Nueva Persona
        </button>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filtros -->
      <div class="bg-theme-card rounded-xl border border-theme-primary p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-theme-primary mb-4">Filtros de búsqueda</h3>
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchForm.search"
              @input="search"
              type="text"
              placeholder="Buscar por nombre, documento o tipo..."
              class="w-full px-4 py-2 border border-theme-primary rounded-lg bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-transparent"
            >
          </div>
          <select
            v-model="searchForm.per_page"
            @change="search"
            class="px-3 py-2 border border-theme-primary rounded-lg bg-theme-card text-theme-primary focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500"
          >
            <option value="10">10 por página</option>
            <option value="15">15 por página</option>
            <option value="25">25 por página</option>
            <option value="50">50 por página</option>
          </select>
        </div>
      </div>

      <!-- Tabla de personas -->
      <div class="bg-theme-card rounded-xl border border-theme-primary shadow-theme-sm overflow-hidden">
        <div class="border-b border-theme-primary bg-theme-secondary p-6">
          <h3 class="text-lg font-semibold text-theme-primary">
            Lista de personas registradas
            <span v-if="personas.total" class="text-sm font-normal text-theme-secondary ml-2">
              ({{ personas.total }} {{ personas.total === 1 ? 'persona' : 'personas' }})
            </span>
          </h3>
        </div>

        <!-- Loading indicator -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-flex items-center gap-2 text-theme-secondary">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cargando personas...
          </div>
        </div>

        <!-- Tabla -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-theme-primary text-sm">
            <thead class="bg-theme-secondary">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">ID</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Documento</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Nombre</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Tipo</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Portátiles</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Vehículos</th>
                <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-theme-primary bg-theme-card">
              <tr v-for="persona in personas.data" :key="persona.id" class="transition-colors hover:bg-theme-secondary">
                <td class="px-6 py-4 text-theme-primary">{{ persona.id }}</td>
                <td class="px-6 py-4 text-theme-secondary">{{ persona.documento || '—' }}</td>
                <td class="px-6 py-4 font-medium text-theme-primary">{{ persona.nombre }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-sena-green-100 dark:bg-sena-green-800 text-sena-green-700 dark:text-sena-green-300">
                    {{ persona.tipoPersona }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-cyan-100 dark:bg-cyan-800 text-cyan-700 dark:text-cyan-300">
                    {{ persona.portatiles?.length || 0 }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-sena-yellow-100 dark:bg-sena-yellow-800 text-sena-yellow-700 dark:text-sena-yellow-300">
                    {{ persona.vehiculos?.length || 0 }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <button
                      @click="openEditModal(persona)"
                      class="px-3 py-1 text-xs bg-sena-green-600 hover:bg-sena-green-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white rounded transition-colors"
                      title="Editar persona"
                    >
                      <Icon name="pencil" class="w-3 h-3" />
                    </button>
                    <button
                      @click="confirmDelete(persona)"
                      class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white rounded transition-colors"
                      title="Eliminar persona"
                    >
                      <Icon name="trash" class="w-3 h-3" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!personas.data?.length">
                <td colspan="7" class="px-6 py-12 text-center text-theme-muted">
                  <div class="flex flex-col items-center gap-2">
                    <svg class="h-8 w-8 text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>No se encontraron personas</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="personas.links && personas.data?.length > 0" class="border-t border-theme-primary bg-theme-secondary px-6 py-4">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-theme-secondary">
              Mostrando {{ personas.from }} a {{ personas.to }} de {{ personas.total }} registros
            </div>
            <div class="flex gap-1">
              <button
                v-for="(link, index) in personas.links"
                :key="index"
                @click="loadPersonasPage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-2 text-sm border rounded transition-colors',
                  link.active
                    ? 'bg-sena-green-600 dark:bg-blue-600 text-white border-sena-green-600 dark:border-blue-600'
                    : link.url
                      ? 'bg-theme-card text-theme-secondary border-theme-primary hover:bg-theme-secondary'
                      : 'bg-theme-tertiary text-theme-muted border-theme-primary cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para crear/editar persona -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-sage-800 dark:text-sage-100">
            <Icon :name="isEditing ? 'pencil' : 'user-plus'" class="w-5 h-5 inline mr-2" />
            {{ isEditing ? 'Editar Persona' : 'Nueva Persona' }}
          </h3>
          <button @click="closeModal" class="text-sage-400 hover:text-sage-600 dark:hover:text-sage-300">
            <Icon name="x" class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submit" class="space-y-3">
          <!-- Nombre -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="user" class="w-3 h-3 inline mr-1" />
              Nombre *
            </label>
            <input
              v-model="form.Nombre"
              type="text"
              required
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.Nombre }"
            />
            <p v-if="form.errors.Nombre" class="mt-1 text-xs text-red-500">{{ form.errors.Nombre }}</p>
          </div>

          <!-- Tipo Documento y Documento -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="file-text" class="w-3 h-3 inline mr-1" />
                Tipo Documento *
              </label>
              <select
                v-model="form.TipoDocumento"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.TipoDocumento }"
              >
                <option value="">Seleccionar</option>
                <option v-for="tipo in tiposDocumento" :key="tipo.value" :value="tipo.value">
                  {{ tipo.label }}
                </option>
              </select>
              <p v-if="form.errors.TipoDocumento" class="mt-1 text-xs text-red-500">{{ form.errors.TipoDocumento }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="badge" class="w-3 h-3 inline mr-1" />
                Documento *
              </label>
              <input
                v-model="form.documento"
                type="text"
                required
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.documento }"
              />
              <p v-if="form.errors.documento" class="mt-1 text-xs text-red-500">{{ form.errors.documento }}</p>
            </div>
          </div>

          <!-- Tipo Persona -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="users" class="w-3 h-3 inline mr-1" />
              Tipo Persona *
            </label>
            <select
              v-model="form.TipoPersona"
              required
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.TipoPersona }"
            >
              <option value="">Seleccionar</option>
              <option v-for="tipo in tiposPersona" :key="tipo.value" :value="tipo.value">
                {{ tipo.label }}
              </option>
            </select>
            <p v-if="form.errors.TipoPersona" class="mt-1 text-xs text-red-500">{{ form.errors.TipoPersona }}</p>
          </div>

          <!-- Correo y Teléfono -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="mail" class="w-3 h-3 inline mr-1" />
                Correo
              </label>
              <input
                v-model="form.Correo"
                type="email"
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.Correo }"
              />
              <p v-if="form.errors.Correo" class="mt-1 text-xs text-red-500">{{ form.errors.Correo }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="phone" class="w-3 h-3 inline mr-1" />
                Teléfono
              </label>
              <input
                v-model="form.Telefono"
                type="text"
                class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
                :class="{ 'border-red-500': form.errors.Telefono }"
              />
              <p v-if="form.errors.Telefono" class="mt-1 text-xs text-red-500">{{ form.errors.Telefono }}</p>
            </div>
          </div>

          <!-- Dirección -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="map-pin" class="w-3 h-3 inline mr-1" />
              Dirección
            </label>
            <input
              v-model="form.Direccion"
              type="text"
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.Direccion }"
            />
            <p v-if="form.errors.Direccion" class="mt-1 text-xs text-red-500">{{ form.errors.Direccion }}</p>
          </div>

          <!-- Empresa -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="building" class="w-3 h-3 inline mr-1" />
              Empresa
            </label>
            <input
              v-model="form.Empresa"
              type="text"
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.Empresa }"
            />
            <p v-if="form.errors.Empresa" class="mt-1 text-xs text-red-500">{{ form.errors.Empresa }}</p>
          </div>

          <!-- Observaciones -->
          <div>
            <label class="block text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
              <Icon name="file-text" class="w-3 h-3 inline mr-1" />
              Observaciones
            </label>
            <textarea
              v-model="form.observaciones"
              rows="2"
              class="w-full px-3 py-1.5 text-sm border border-sage-300 dark:border-sage-600 rounded-lg focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100"
              :class="{ 'border-red-500': form.errors.observaciones }"
            ></textarea>
            <p v-if="form.errors.observaciones" class="mt-1 text-xs text-red-500">{{ form.errors.observaciones }}</p>
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
              class="px-4 py-1.5 text-sm bg-sena-green-600 hover:bg-sena-green-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
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