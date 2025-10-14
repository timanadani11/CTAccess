<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import Modal from '@/Components/Modal.vue'
import Icon from '@/Components/Icon.vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

const page = usePage()
const permisos = page.props.permisos
const roles = page.props.roles || []
const modulos = page.props.modulos || []
const filters = page.props.filters || { q: '', modulo: '' }

const q = ref(filters.q || '')
const moduloFilter = ref(filters.modulo || '')

watch([q, moduloFilter], ([newQ, newModulo]) => {
  router.get(
    route('system.admin.permissions.index'), 
    { q: newQ, modulo: newModulo }, 
    { preserveState: true, replace: true }
  )
})

// Modal state
const showModal = ref(false)
const isEditing = ref(false)
const editingPermisoId = ref(null)

// Form
const form = useForm({
  nombre: '',
  modulo: '',
  descripcion: '',
  roles: [],
})

const openCreateModal = () => {
  isEditing.value = false
  editingPermisoId.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const openEditModal = (permiso) => {
  isEditing.value = true
  editingPermisoId.value = permiso.id
  form.nombre = permiso.nombre
  form.modulo = permiso.modulo || ''
  form.descripcion = permiso.descripcion || ''
  form.roles = permiso.roles_ids || []
  form.clearErrors()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

const canSave = computed(() => form.nombre.trim() !== '')

const submit = () => {
  if (isEditing.value) {
    form.put(route('system.admin.permissions.update', editingPermisoId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('system.admin.permissions.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

const remove = (id) => {
  if (confirm('¿Eliminar este permiso? Se desasociará de todos los roles.')) {
    router.delete(route('system.admin.permissions.destroy', id))
  }
}

const clearFilters = () => {
  q.value = ''
  moduloFilter.value = ''
}
</script>

<template>
  <SystemLayout>
    <Head title="Gestión de Permisos" />

    <template #header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-theme-primary">Gestión de Permisos</h2>
          <p class="text-sm text-theme-muted mt-0.5">Administra los permisos del sistema y asígnalos a roles</p>
        </div>
        <button 
          class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition-colors flex items-center gap-2" 
          @click="openCreateModal"
        >
          <Icon name="Plus" :size="16" />
          Nuevo permiso
        </button>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Filtros -->
      <div class="rounded-lg border border-theme-primary bg-theme-card p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-3">
          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-theme-secondary mb-1">Buscar</label>
            <input 
              v-model="q" 
              type="text" 
              class="w-full rounded border border-theme-primary px-3 py-2 text-sm bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
              placeholder="Buscar por nombre o descripción..." 
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-theme-secondary mb-1">Módulo</label>
            <select 
              v-model="moduloFilter"
              class="w-full rounded border border-theme-primary px-3 py-2 text-sm bg-theme-card text-theme-primary focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
            >
              <option value="">Todos los módulos</option>
              <option v-for="mod in modulos" :key="mod" :value="mod">{{ mod }}</option>
            </select>
          </div>
        </div>
        <div v-if="filters.q || filters.modulo" class="mt-3 flex items-center gap-2">
          <button 
            @click="clearFilters"
            class="text-xs text-theme-muted hover:text-theme-primary flex items-center gap-1"
          >
            <Icon name="X" :size="12" />
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto rounded-lg border border-theme-primary bg-theme-card shadow-sm">
        <table class="min-w-full divide-y divide-theme-primary text-sm">
          <thead class="bg-theme-secondary">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Permiso</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Módulo</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Descripción</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Roles asignados</th>
              <th class="px-4 py-3 text-right font-semibold text-theme-secondary">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-theme-primary bg-theme-card">
            <tr v-for="p in permisos.data" :key="p.id" class="hover:bg-theme-secondary transition-colors">
              <td class="px-4 py-3 text-theme-secondary font-medium">
                <div class="flex items-center gap-2">
                  <Icon name="Key" :size="14" class="text-emerald-600" />
                  {{ p.nombre }}
                </div>
              </td>
              <td class="px-4 py-3 text-theme-secondary">
                <span v-if="p.modulo" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                  {{ p.modulo }}
                </span>
                <span v-else class="text-theme-muted">—</span>
              </td>
              <td class="px-4 py-3 text-theme-secondary text-sm">
                <span v-if="p.descripcion">{{ p.descripcion }}</span>
                <span v-else class="text-theme-muted italic">Sin descripción</span>
              </td>
              <td class="px-4 py-3">
                <div v-if="p.roles_count > 0" class="flex flex-wrap gap-1">
                  <span 
                    v-for="rol in p.roles" 
                    :key="rol"
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400"
                  >
                    {{ rol }}
                  </span>
                </div>
                <span v-else class="text-xs text-theme-muted">Sin roles</span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="rounded border border-theme-primary px-2 py-1 text-xs font-medium text-theme-secondary hover:bg-theme-secondary transition-colors flex items-center gap-1" 
                    @click="openEditModal(p)"
                  >
                    <Icon name="Edit" :size="12" />
                    Editar
                  </button>
                  <button 
                    class="rounded border border-red-300 dark:border-red-700 px-2 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-1" 
                    @click="remove(p.id)"
                  >
                    <Icon name="Trash2" :size="12" />
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!permisos.data?.length">
              <td colspan="5" class="px-4 py-12 text-center text-theme-muted">
                <div class="flex flex-col items-center gap-2">
                  <Icon name="Key" :size="48" class="opacity-50" />
                  <span class="font-medium">No se encontraron permisos</span>
                  <button 
                    @click="openCreateModal"
                    class="mt-2 text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
                  >
                    <Icon name="Plus" :size="14" />
                    Crear el primer permiso
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginación -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-theme-primary bg-theme-card">
          <div class="text-sm text-theme-secondary">
            Página <span class="font-medium">{{ permisos.current_page }}</span> de <span class="font-medium">{{ permisos.last_page }}</span>
          </div>
          <div class="flex gap-2">
            <Link 
              v-if="permisos.prev_page_url" 
              :href="permisos.prev_page_url" 
              preserve-state 
              replace 
              class="rounded border border-theme-primary px-3 py-1.5 text-sm text-theme-secondary hover:bg-theme-secondary transition-colors"
            >
              Anterior
            </Link>
            <Link 
              v-if="permisos.next_page_url" 
              :href="permisos.next_page_url" 
              preserve-state 
              replace 
              class="rounded border border-theme-primary px-3 py-1.5 text-sm text-theme-secondary hover:bg-theme-secondary transition-colors"
            >
              Siguiente
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Compacto -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="bg-white dark:bg-sage-800 text-sage-900 dark:text-sage-100">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-sage-200 dark:border-sage-700 px-4 py-3">
          <div class="flex items-center gap-2">
            <Icon name="Key" :size="18" class="text-emerald-600" />
            <h3 class="text-base font-semibold">
              {{ isEditing ? 'Editar Permiso' : 'Nuevo Permiso' }}
            </h3>
          </div>
          <button 
            @click="closeModal" 
            class="text-sage-400 hover:text-sage-600 dark:hover:text-sage-300 transition-colors"
          >
            <Icon name="X" :size="18" />
          </button>
        </div>

        <!-- Body -->
        <form @submit.prevent="submit" class="px-4 py-3">
          <div class="space-y-3">
            <!-- Nombre del permiso -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="Key" :size="12" />
                Nombre del permiso <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.nombre" 
                type="text"
                class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" 
                placeholder="Ej: ver_usuarios, editar_accesos"
              />
              <div v-if="form.errors.nombre" class="mt-0.5 text-xs text-red-500">{{ form.errors.nombre }}</div>
            </div>

            <!-- Módulo -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="Folder" :size="12" />
                Módulo
              </label>
              <input 
                v-model="form.modulo" 
                type="text"
                class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" 
                placeholder="Ej: Usuarios, Accesos, Incidencias"
              />
              <p class="mt-0.5 text-xs text-sage-500 dark:text-sage-400">Agrupa permisos por funcionalidad</p>
            </div>

            <!-- Descripción -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="FileText" :size="12" />
                Descripción
              </label>
              <textarea 
                v-model="form.descripcion" 
                rows="2"
                class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 resize-none" 
                placeholder="Describe qué permite hacer este permiso"
              ></textarea>
            </div>

            <!-- Roles asignados -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1.5">
                <Icon name="Shield" :size="12" />
                Asignar a roles
              </label>
              <div class="flex flex-wrap gap-2">
                <label 
                  v-for="r in roles" 
                  :key="r.id" 
                  class="flex items-center gap-1.5 text-xs text-sage-700 dark:text-sage-300 hover:text-sage-900 dark:hover:text-sage-100 cursor-pointer"
                >
                  <input 
                    type="checkbox" 
                    :value="r.id" 
                    v-model="form.roles" 
                    class="rounded border-sage-300 dark:border-sage-600 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-sage-700"
                  />
                  {{ r.nombre }}
                </label>
              </div>
              <p class="mt-1 text-xs text-sage-500 dark:text-sage-400">Los roles seleccionados tendrán este permiso</p>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-sage-200 dark:border-sage-700">
            <button 
              type="button" 
              @click="closeModal"
              class="flex items-center gap-1.5 rounded border border-sage-300 dark:border-sage-600 px-3 py-1.5 text-sm font-medium text-sage-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-700 transition-colors"
            >
              <Icon name="X" :size="14" />
              Cancelar
            </button>
            <button 
              type="submit"
              :disabled="!canSave || form.processing"
              class="flex items-center gap-1.5 rounded bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <Icon v-if="form.processing" name="Loader2" :size="14" class="animate-spin" />
              <Icon v-else name="Save" :size="14" />
              {{ form.processing ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Crear') }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </SystemLayout>
</template>
