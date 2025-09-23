<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const page = usePage()
const users = page.props.users
const roles = page.props.roles || []
const filters = page.props.filters || { q: '' }

const q = ref(filters.q || '')
watch(q, (val) => {
  router.get(route('system.admin.users.index'), { q: val }, { preserveState: true, replace: true })
})

const goCreate = () => router.visit(route('system.admin.users.create'))
const edit = (id) => router.visit(route('system.admin.users.edit', id))
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
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Usuarios del Sistema</h2>
        <button class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700" @click="goCreate">Nuevo usuario</button>
      </div>
    </template>

    <div class="space-y-4">
      <div class="rounded-lg border border-slate-200 bg-white p-4">
        <input v-model="q" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" placeholder="Buscar por usuario o nombre..." />
      </div>

      <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-medium text-slate-600">Usuario</th>
              <th class="px-4 py-2 text-left font-medium text-slate-600">Nombre</th>
              <th class="px-4 py-2 text-left font-medium text-slate-600">Activo</th>
              <th class="px-4 py-2 text-left font-medium text-slate-600">Rol principal</th>
              <th class="px-4 py-2 text-left font-medium text-slate-600">Roles</th>
              <th class="px-4 py-2" />
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="u in users.data" :key="u.id" class="hover:bg-slate-50">
              <td class="px-4 py-2">{{ u.UserName }}</td>
              <td class="px-4 py-2">{{ u.nombre }}</td>
              <td class="px-4 py-2">
                <span :class="u.activo ? 'text-emerald-600' : 'text-slate-500'">{{ u.activo ? 'Sí' : 'No' }}</span>
              </td>
              <td class="px-4 py-2">{{ u.rol_principal || '—' }}</td>
              <td class="px-4 py-2">{{ (u.roles || []).join(', ') || '—' }}</td>
              <td class="px-4 py-2 text-right">
                <button class="rounded-md border border-slate-300 px-2 py-1 text-xs hover:bg-slate-50" @click="edit(u.id)">Editar</button>
                <button class="ml-2 rounded-md border border-red-300 px-2 py-1 text-xs text-red-600 hover:bg-red-50" @click="remove(u.id)">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!users.data?.length">
              <td colspan="6" class="px-4 py-8 text-center text-slate-500">Sin usuarios</td>
            </tr>
          </tbody>
        </table>

        <div class="flex items-center justify-between px-4 py-3 text-sm text-slate-600">
          <div>Página {{ users.current_page }} de {{ users.last_page }}</div>
          <div class="space-x-2">
            <Link v-if="users.prev_page_url" :href="users.prev_page_url" preserve-state replace class="rounded-md border border-slate-300 px-2 py-1">Anterior</Link>
            <Link v-if="users.next_page_url" :href="users.next_page_url" preserve-state replace class="rounded-md border border-slate-300 px-2 py-1">Siguiente</Link>
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>
