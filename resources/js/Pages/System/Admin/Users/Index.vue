<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import Modal from '@/Components/Modal.vue'
import Icon from '@/Components/Icon.vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

const page = usePage()
const users = page.props.users
const roles = page.props.roles || []
const filters = page.props.filters || { q: '' }

const q = ref(filters.q || '')
watch(q, (val) => {
  router.get(route('system.admin.users.index'), { q: val }, { preserveState: true, replace: true })
})

// Modal state
const showModal = ref(false)
const isEditing = ref(false)
const editingUserId = ref(null)

// Form
const form = useForm({
  UserName: '',
  password: '',
  nombre: '',
  tipo_documento: '',
  documento: '',
  correo: '',
  activo: true,
  roles: [],
  rol_principal_id: null,
})

const tiposDocumento = [
  { value: 'DNI', label: 'DNI' },
  { value: 'Pasaporte', label: 'Pasaporte' },
  { value: 'Cédula', label: 'Cédula' },
  { value: 'RUC', label: 'RUC' },
]

const openCreateModal = () => {
  isEditing.value = false
  editingUserId.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const openEditModal = (user) => {
  isEditing.value = true
  editingUserId.value = user.id
  form.UserName = user.UserName
  form.password = ''
  form.nombre = user.nombre
  form.tipo_documento = user.tipo_documento || ''
  form.documento = user.documento || ''
  form.correo = user.correo || ''
  form.activo = user.activo
  form.rol_principal_id = user.rol_principal_id || null
  form.roles = user.roles || []
  form.clearErrors()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

const canSave = computed(() => {
  if (isEditing.value) {
    return form.UserName && form.nombre
  }
  return form.UserName && form.password && form.nombre
})

const submit = () => {
  if (isEditing.value) {
    form.put(route('system.admin.users.update', editingUserId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('system.admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

const remove = (id) => {
  if (confirm('¿Eliminar este usuario?')) {
    router.delete(route('system.admin.users.destroy', id))
  }
}
</script>

<template>
  <SystemLayout>
    <Head title="Usuarios del Sistema" />

    <template #header>
      <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-semibold leading-tight text-theme-primary">Usuarios del Sistema</h2>
        <button 
          class="rounded-md bg-sena-green-600 hover:bg-sena-green-700 dark:bg-blue-600 dark:hover:bg-blue-500 px-3 py-2 text-sm font-medium text-white transition-colors flex items-center gap-2" 
          @click="openCreateModal"
        >
          <Icon name="Plus" :size="16" />
          Nuevo usuario
        </button>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Buscador -->
      <div class="rounded-lg border border-theme-primary bg-theme-card p-4 shadow-sm">
        <input 
          v-model="q" 
          type="text" 
          class="w-full rounded-md border border-theme-primary px-3 py-2 text-sm bg-theme-card text-theme-primary placeholder-theme-muted focus:ring-2 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-transparent transition-all" 
          placeholder="Buscar por usuario o nombre..." 
        />
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto rounded-lg border border-theme-primary bg-theme-card shadow-sm">
        <table class="min-w-full divide-y divide-theme-primary text-sm">
          <thead class="bg-theme-secondary">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Usuario</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Nombre</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Documento</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Correo</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Activo</th>
              <th class="px-4 py-3 text-left font-semibold text-theme-secondary">Rol principal</th>
              <th class="px-4 py-3 text-right font-semibold text-theme-secondary">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-theme-primary bg-theme-card">
            <tr v-for="u in users.data" :key="u.id" class="hover:bg-theme-secondary transition-colors">
              <td class="px-4 py-3 text-theme-secondary font-medium">{{ u.UserName }}</td>
              <td class="px-4 py-3 text-theme-secondary">{{ u.nombre }}</td>
              <td class="px-4 py-3 text-theme-secondary">
                <span v-if="u.documento">{{ u.tipo_documento || '' }} {{ u.documento }}</span>
                <span v-else class="text-theme-muted">—</span>
              </td>
              <td class="px-4 py-3 text-theme-secondary">
                <span v-if="u.correo">{{ u.correo }}</span>
                <span v-else class="text-theme-muted">—</span>
              </td>
              <td class="px-4 py-3">
                <span 
                  :class="u.activo 
                    ? 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-sena-green-100 text-sena-green-800 dark:bg-sena-green-900/30 dark:text-sena-green-400' 
                    : 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'"
                >
                  {{ u.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-theme-secondary">
                <span v-if="u.rol_principal" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400">
                  {{ u.rol_principal }}
                </span>
                <span v-else class="text-theme-muted">—</span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="rounded border border-sena-green-300 dark:border-cyan-700 px-2 py-1 text-xs font-medium text-sena-green-700 dark:text-cyan-400 hover:bg-sena-green-50 dark:hover:bg-cyan-900/20 transition-colors flex items-center gap-1" 
                    @click="openEditModal(u)"
                  >
                    <Icon name="Edit" :size="12" />
                    Editar
                  </button>
                  <button 
                    class="rounded border border-red-300 dark:border-red-700 px-2 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-1" 
                    @click="remove(u.id)"
                  >
                    <Icon name="Trash2" :size="12" />
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!users.data?.length">
              <td colspan="7" class="px-4 py-12 text-center text-theme-muted">
                <div class="flex flex-col items-center gap-2">
                  <Icon name="Users" :size="48" class="opacity-50" />
                  <span class="font-medium">No se encontraron usuarios</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginación -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-theme-primary bg-theme-card">
          <div class="text-sm text-theme-secondary">
            Página <span class="font-medium">{{ users.current_page }}</span> de <span class="font-medium">{{ users.last_page }}</span>
          </div>
          <div class="flex gap-2">
            <Link 
              v-if="users.prev_page_url" 
              :href="users.prev_page_url" 
              preserve-state 
              replace 
              class="rounded-md border border-theme-primary px-3 py-1.5 text-sm text-theme-secondary hover:bg-theme-secondary transition-colors"
            >
              Anterior
            </Link>
            <Link 
              v-if="users.next_page_url" 
              :href="users.next_page_url" 
              preserve-state 
              replace 
              class="rounded-md border border-theme-primary px-3 py-1.5 text-sm text-theme-secondary hover:bg-theme-secondary transition-colors"
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
            <Icon :name="isEditing ? 'Edit' : 'UserPlus'" :size="18" class="text-sena-green-600 dark:text-cyan-500" />
            <h3 class="text-base font-semibold">
              {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
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
            <!-- Usuario y Contraseña -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="User" :size="12" />
                  Usuario <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.UserName" 
                  type="text"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500" 
                  placeholder="admin"
                />
                <div v-if="form.errors.UserName" class="mt-0.5 text-xs text-red-500">{{ form.errors.UserName }}</div>
              </div>

              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="Key" :size="12" />
                  Contraseña <span v-if="!isEditing" class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.password" 
                  type="password"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500" 
                  :placeholder="isEditing ? 'Sin cambios' : 'Mínimo 8'"
                />
                <div v-if="form.errors.password" class="mt-0.5 text-xs text-red-500">{{ form.errors.password }}</div>
              </div>
            </div>

            <!-- Nombre completo -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="UserCheck" :size="12" />
                Nombre completo <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.nombre" 
                type="text"
                class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500" 
                placeholder="Administrador General"
              />
              <div v-if="form.errors.nombre" class="mt-0.5 text-xs text-red-500">{{ form.errors.nombre }}</div>
            </div>

            <!-- Documento -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="FileText" :size="12" />
                  Tipo documento
                </label>
                <select 
                  v-model="form.tipo_documento"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500"
                >
                  <option value="">—</option>
                  <option v-for="tipo in tiposDocumento" :key="tipo.value" :value="tipo.value">
                    {{ tipo.label }}
                  </option>
                </select>
              </div>

              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="Badge" :size="12" />
                  Nº documento
                </label>
                <input 
                  v-model="form.documento" 
                  type="text"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500" 
                  placeholder="12345678"
                />
              </div>
            </div>

            <!-- Correo -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                <Icon name="Mail" :size="12" />
                Correo electrónico
              </label>
              <input 
                v-model="form.correo" 
                type="email"
                class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 placeholder-sage-400 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500" 
                placeholder="admin@ctaccess.com"
              />
              <div v-if="form.errors.correo" class="mt-0.5 text-xs text-red-500">{{ form.errors.correo }}</div>
            </div>

            <!-- Estado y Rol -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="CheckCircle" :size="12" />
                  Estado
                </label>
                <select 
                  v-model="form.activo"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500"
                >
                  <option :value="true">Activo</option>
                  <option :value="false">Inactivo</option>
                </select>
              </div>

              <div>
                <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1">
                  <Icon name="Shield" :size="12" />
                  Rol principal
                </label>
                <select 
                  v-model="form.rol_principal_id"
                  class="w-full rounded border border-sage-300 dark:border-sage-600 px-2 py-1.5 text-sm bg-white dark:bg-sage-700 text-sage-900 dark:text-sage-100 focus:ring-1 focus:ring-sena-green-500 dark:focus:ring-cyan-500 focus:border-sena-green-500 dark:focus:border-cyan-500"
                >
                  <option :value="null">—</option>
                  <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
                </select>
              </div>
            </div>

            <!-- Roles adicionales -->
            <div>
              <label class="flex items-center gap-1 text-xs font-medium text-sage-700 dark:text-sage-300 mb-1.5">
                <Icon name="Users" :size="12" />
                Roles adicionales
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
                    class="rounded border-sage-300 dark:border-sage-600 text-sena-green-600 dark:text-cyan-600 focus:ring-sena-green-500 dark:focus:ring-cyan-500 bg-white dark:bg-sage-700"
                  />
                  {{ r.nombre }}
                </label>
              </div>
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
              class="flex items-center gap-1.5 rounded bg-sena-green-600 hover:bg-sena-green-700 dark:bg-blue-600 dark:hover:bg-blue-500 px-3 py-1.5 text-sm font-medium text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
