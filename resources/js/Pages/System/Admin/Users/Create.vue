<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ roles: { type: Array, default: () => [] } })
const form = useForm({
  UserName: '',
  password: '',
  nombre: '',
  activo: true,
  roles: [],
  rol_principal_id: null,
})

const submit = () => form.post(route('system.admin.users.store'))
const canSave = computed(() => form.UserName && form.password && form.nombre)
</script>

<template>
  <SystemLayout>
    <Head title="Nuevo Usuario" />

    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Nuevo Usuario del Sistema</h2>
    </template>

    <form class="space-y-6" @submit.prevent="submit">
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-slate-700">Usuario</label>
          <input v-model="form.UserName" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
          <div v-if="form.errors.UserName" class="mt-1 text-xs text-red-600">{{ form.errors.UserName }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Contraseña</label>
          <input type="password" v-model="form.password" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
          <div v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Nombre</label>
          <input v-model="form.nombre" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
          <div v-if="form.errors.nombre" class="mt-1 text-xs text-red-600">{{ form.errors.nombre }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Activo</label>
          <select v-model="form.activo" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option :value="true">Sí</option>
            <option :value="false">No</option>
          </select>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-slate-700">Roles</label>
          <div class="mt-2 grid grid-cols-1 gap-2">
            <label v-for="r in props.roles" :key="r.id" class="flex items-center gap-2 text-sm">
              <input type="checkbox" :value="r.id" v-model="form.roles" />
              <span>{{ r.nombre }}</span>
            </label>
          </div>
          <div v-if="form.errors.roles" class="mt-1 text-xs text-red-600">{{ form.errors.roles }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Rol principal</label>
          <select v-model="form.rol_principal_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option :value="null">—</option>
            <option v-for="r in props.roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
          </select>
          <div class="mt-1 text-xs text-slate-500">Se incluirá automáticamente en la lista de roles.</div>
        </div>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm" @click="router.visit(route('system.admin.users.index'))">Cancelar</button>
        <button :disabled="!canSave || form.processing" class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50">Guardar</button>
      </div>
    </form>
  </SystemLayout>
</template>
